<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\ItemPembelian;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PembelianController extends Controller
{
    public function index()
    {
        $data_pembelian = Pembelian::with(['customer', 'perusahaan_cabang', 'user', 'item_pembelian_draft'])
                                    ->latest()
                                    ->paginate(10);

        return view('admin.dataPembelian', [
            'data_pembelian' => $data_pembelian
        ]);
    }

    public function create()
    {
        // 1. Ambil semua data yang dibutuhkan untuk dropdown di form
        $data_customer = Customer::orderBy('nama', 'asc')->get();
        $data_cabang = Branch::orderBy('nama', 'asc')->get();
        $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // 2. Kirim semua data itu ke view
        return view('admin.inputPembelian', [
            'semua_customer' => $data_customer,
            'semua_cabang' => $data_cabang,
            'semua_kategori' => $data_kategori
        ]);
    }

    /**
     * Menyimpan data pembelian baru dari form wizard.
     */
    public function store(Request $request)
    {
        $status = $request->input('status_pembelian', 'draft');

        // Validasi
        $validator = Validator::make($request->all(), [
            // pembelian_id WAJIB ADA, karena sudah dibuat oleh AJAX
            'pembelian_id' => 'required|exists:pembelian,id',
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => 'required|exists:perusahaan_cabang,id',
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0',
            'harga_tawaran_toko' => 'nullable|numeric|min:0',
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0',
            // Kita tidak lagi memvalidasi 'items' di sini
        ], [
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".',
            'pembelian_id.required' => 'Terjadi kesalahan. Coba muat ulang halaman. (ID Pembelian tidak ditemukan)'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mulai Transaksi Database
        DB::beginTransaction();
        try {
            // Temukan data Pembelian (Induk) yang sudah dibuat AJAX
            $pembelian = Pembelian::findOrFail($request->pembelian_id);

            // Update data Induk
            $pembelian->customer_id = $request->customer_id; // Update jika customer diganti
            $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id; // Update jika cabang diganti
            $pembelian->harga_tawaran_customer = $request->harga_tawaran_customer;
            $pembelian->harga_tawaran_toko = $request->harga_tawaran_toko;
            $pembelian->harga_deal = $request->harga_deal;
            $pembelian->status_pembelian = $status;
            $pembelian->save();

            // Kita TIDAK PERLU lagi loop $request->items, karena sudah disimpan

            DB::commit();

            // Logika Redirect Anda sudah benar
            if ($status == 'draft') {
                return redirect()->route('admin.purchases.show', $pembelian->id)
                                 ->with('success', 'Draft berhasil disimpan. Link tinjauan telah disalin ke clipboard!')
                                 ->with('auto_copy_link', true);
            } else {
                return redirect()->route('admin.purchases.index')
                                 ->with('success', 'Transaksi pembelian telah difinalisasi.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }
    }
        /**
     * Menampilkan detail 1 transaksi pembelian (Read-Only Link)
     */
    public function show($id)
    {
        $pembelian = Pembelian::with([
                            'customer',
                            'perusahaan_cabang', // Ganti ke 'branch' jika itu nama relasi Anda
                            'user',
                            'item_pembelian_draft.kategori'
                        ])->findOrFail($id);

        // Buat file view baru ini (Langkah 3)
        return view('admin.reviewPembelian', ['pembelian' => $pembelian]);
    }

    public function ajaxStoreItemDraft(Request $request)
    {
        // 1. Validasi data item
        // (Sesuaikan aturan 'nullable' berdasarkan migrasi Anda)
        $itemRules = [
            'nama_item' => 'required|string|max:200',
            'kategori_id' => 'required|exists:kategori,id',
            'serial_number' => 'nullable|string|max:50',
            'serial_lens' => 'nullable|string|max:50',
            'kondisi_fisik' => 'nullable|string|max:100',
            'kondisi_baut' => 'nullable|string|max:50',
            'kondisi_tutup_usb' => 'nullable|string|max:50',
            'kondisi_grip' => 'nullable|string|max:50',
            'kondisi_jamur_lensa' => 'nullable|string|max:100',
            'kondisi_view_finder' => 'nullable|string|max:50',
            'kondisi_mounting' => 'nullable|string|max:50',
            'kondisi_slot_memori' => 'nullable|string|max:50',
            'kondisi_jamur_sensor' => 'nullable|string|max:100',
            'kondisi_lcd' => 'nullable|string|max:100',
            'kondisi_tombol' => 'nullable|string|max:50',
            'kondisi_zoom_lensa' => 'nullable|string|max:50',
            'kondisi_af_lensa' => 'nullable|string|max:50',
            'kondisi_diafragma_lensa' => 'nullable|string|max:50',
            'kondisi_kalibrasi_fokus' => 'nullable|string|max:50',
            'kondisi_flash' => 'nullable|string|max:100',
            'kondisi_sound_mic' => 'nullable|string|max:50',
            'kondisi_lain_lain' => 'nullable|string|max:255',
            'kelengkapan_awal' => 'nullable|string', // Ini dari JS
        ];

        // Ganti nama 'kelengkapan_awal' (dari JS) menjadi 'kelengkapan' (sesuai DB)
        $request->merge(['kelengkapan' => $request->input('kelengkapan_awal')]);

        $parentRules = [];
        if (!$request->input('pembelian_id')) {
            // Jika ini item PERTAMA, kita butuh data parent (Customer/Cabang)
            $parentRules = [
                'customer_id' => 'required|exists:customer,id',
                'perusahaan_cabang_id' => 'required|exists:perusahaan_cabang,id',
                'user_id' => 'required|exists:users,id',
            ];
        } else {
            // Jika item kedua dst., kita hanya perlu ID parent
            $parentRules = ['pembelian_id' => 'required|exists:pembelian,id'];
        }

        $validator = Validator::make($request->all(), array_merge($itemRules, $parentRules));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // 2. Mulai Transaksi DB
        DB::beginTransaction();
        try {
            $pembelian = null;
            if (!$request->input('pembelian_id')) {
                // Item PERTAMA: Buat data Pembelian (Induk)
                $pembelian = new Pembelian();
                $pembelian->customer_id = $request->customer_id;
                $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id;
                $pembelian->user_id = $request->user_id;
                $pembelian->status_pembelian = 'draft'; // Status default
                $pembelian->save();
            } else {
                // Item KEDUA dst.: Cari data Pembelian (Induk)
                $pembelian = Pembelian::findOrFail($request->input('pembelian_id'));
            }

            // 3. Buat data ItemPembelian (Anak)
            $itemData = $request->only((new ItemPembelian)->getFillable());
            $itemData['qty'] = 1; // Sesuai migrasi
            $itemData['status'] = 'Second'; // Sesuai migrasi

            $item = $pembelian->item_pembelian_draft()->create($itemData);

            // Load relasi kategori untuk dikirim balik ke JS (untuk tampilan tabel)
            $item->load('kategori');

            DB::commit();

            // 4. Kirim respon sukses
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil disimpan!',
                'pembelian_id' => $pembelian->id, // Kirim ID pembelian (baru atau lama)
                'item' => $item // Kirim data item yg baru dibuat (lengkap dgn ID DB)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * =======================================================
     * METHOD BARU 2: Hapus Item via AJAX
     * =======================================================
     */
    public function ajaxDeleteItemDraft($item_id)
    {
        try {
            // Cari item berdasarkan ID database-nya
            $item = ItemPembelian::findOrFail($item_id);

            // Hapus item
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Item berhasil dihapus.']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }

}
