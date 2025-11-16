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
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => 'required|exists:perusahaan_cabang,id',
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0',
            'harga_tawaran_toko' => 'nullable|numeric|min:0',
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0', // Wajib jika 'deal'

            'items' => ($status == 'draft' ? 'nullable' : 'required') . '|array', // Wajib jika bukan draft
            'items.*.nama_item' => 'required_with:items|string|max:200',
            'items.*.kategori_id' => 'required_with:items|exists:kategori,id',
            // (Tambahkan validasi lain untuk 'items.*. ...' di sini)
        ], [
            'items.required' => 'Setidaknya satu item wajib ditambahkan untuk status "Deal" atau "Tidak Deal".',
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mulai Transaksi Database
        DB::beginTransaction();
        try {

            // Buat data Pembelian (Induk)
            $pembelian = new Pembelian();
            $pembelian->customer_id = $request->customer_id;
            $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id;
            $pembelian->user_id = $request->user_id;
            $pembelian->harga_tawaran_customer = $request->harga_tawaran_customer;
            $pembelian->harga_tawaran_toko = $request->harga_tawaran_toko;
            $pembelian->harga_deal = $request->harga_deal;
            $pembelian->status_pembelian = $status;
            // 'kas' akan diisi nanti saat halaman 'edit' jika status 'deal'
            $pembelian->save();

            // Loop dan simpan data Item (Anak)
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $itemData['qty'] = 1;
                    $itemData['status'] = 'Second';
                    $pembelian->item_pembelian_draft()->create($itemData);
                }
            }

            DB::commit();

            // =======================================================
            // PERBAIKAN LOGIKA REDIRECT (SESUAI PERMINTAAN ANDA)
            // =======================================================

            if ($status == 'draft') {
                // 1. Jika DRAFT: Redirect ke halaman Tinjauan (show)
                // Kita juga "titipkan" pesan flash 'auto_copy_link'
                return redirect()->route('admin.purchases.show', $pembelian->id)
                                 ->with('success', 'Draft berhasil disimpan. Link tinjauan telah disalin ke clipboard!')
                                 ->with('auto_copy_link', true); // <-- INI KUNCINYA
            } else {
                // 2. Jika DEAL atau TIDAK DEAL: Redirect kembali ke halaman DAFTAR
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

}
