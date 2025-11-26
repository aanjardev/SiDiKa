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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;


class PembelianController extends Controller
{
    public function index(Request $request)
    {
        // Ambil parameter dari request
        $search = $request->query('search');
        $status = $request->query('status');
        $sort = $request->query('sort', 'terbaru');

        $query = Pembelian::with(['customer', 'perusahaan_cabang', 'user', 'item_pembelian_draft']);

        // Filter Search (Berdasarkan ID Pembelian atau Nama Customer)
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Menggunakan kode_transaksi jika ada, fallback ke id
                $q->where('kode_transaksi', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%')
                  ->orWhereHas('customer', function ($qC) use ($search) {
                      $qC->where('nama', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter Status
        if ($status && $status != 'semua') {
            $query->where('status_pembelian', $status);
        }

        // Sortir
        if ($sort == 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        // Ambil data dengan pagination, pastikan query string dipertahankan untuk link pagination
        $data_pembelian = $query->paginate(10)->withQueryString();

        $data = [
            'data_pembelian' => $data_pembelian,
            'search_term' => $search,
            'status_filter' => $status,
            'sort_filter' => $sort,
        ];

        // LOGIKA BARU UNTUK AJAX: RENDERING LANGSUNG
        if ($request->ajax()) {
            $html = '';

            // Render TR (Baris Tabel)
            if ($data_pembelian->isEmpty()) {
                // Render Empty State (pastikan colspan 9)
                $html .= '<tr class="tr-empty">';
                $html .= '<td colspan="9" class="p-0">';
                $html .= '<div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">';
                $html .= '<i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>';
                $html .= '<h5 class="mb-1">Tidak Ada Data Pembelian</h5>';
                $html .= '<p class="text-muted mb-0">Silakan <a href="' . route('admin.purchases.create') . '">tambah transaksi pembelian</a> baru.</p>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '</tr>';
            } else {
                foreach ($data_pembelian as $idx => $pembelian) {
                    $iteration = $data_pembelian->firstItem() ? $data_pembelian->firstItem() + $idx : $idx + 1;

                    $kodeTransaksi = $pembelian->kode_transaksi ?? $pembelian->id;
                    $customerName = $pembelian->customer->nama ?? '-';
                    $createdAt = $pembelian->created_at->format('d M Y, H:i');
                    $cabangName = $pembelian->perusahaan_cabang->nama ?? '-';

                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                    $itemSummary = Str::limit($itemNames, 40, '...');


                    // Logika Status Badge
                    $statusClass = '';
                    $statusText = '';
                    if ($pembelian->status_pembelian == 'deal') {
                        $statusClass = 'bg-success-subtle text-success-emphasis';
                        $statusText = 'Deal';
                    } elseif ($pembelian->status_pembelian == 'tidak_deal') {
                        $statusClass = 'bg-danger-subtle text-danger-emphasis';
                        $statusText = 'Tidak Deal';
                    } else {
                        $statusClass = 'bg-secondary-subtle text-secondary-emphasis';
                        $statusText = 'Draft';
                    }

                    // Logika Harga Deal
                    $hargaDeal = $pembelian->harga_deal ? 'Rp ' . number_format($pembelian->harga_deal, 0, ',', '.') : '-';

                    $html .= '<tr>';
                    $html .= '<td class="text-center" style="width: 60px;">' . $iteration . '</td>'; // Menggunakan $loop->iteration dari Blade (perlu diperbaiki jika ini bukan loop Blade)
                    $html .= '<td>' . $kodeTransaksi . '</td>';
                    $html .= '<td>' . $customerName . '</td>';
                    $html .= '<td>' . $createdAt . '</td>';
                    $html .= '<td>' . $cabangName . '</td>';
                    $html .= '<td>' . $itemSummary . '</td>';
                    $html .= '<td><span class="badge ' . $statusClass . '">' . $statusText . '</span></td>';
                    $html .= '<td>' . $hargaDeal . '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<div class="d-flex justify-content-center gap-2">';
                    // Tombol Aksi
                    $html .= '<a href="' . route('admin.purchases.show', $pembelian->id) . '" title="Lihat Detail Transaksi"><i class="fa-solid fa-eye" style="color: black;"></i></a>';
                    $html .= '<a href="' . route('admin.purchases.edit', $pembelian->id) . '" title="Edit Transaksi"><i class="fa-solid fa-pen-to-square"></i></a>';
                    $html .= '<form action="' . route('admin.purchases.destroy', $pembelian->id) . '" method="POST" onsubmit="return confirm(\'Yakin mau hapus data ini?\')" class="d-inline">';
                    $html .= csrf_field(); // Helper untuk CSRF token
                    $html .= method_field('DELETE'); // Helper untuk DELETE method
                    $html .= '<button type="submit" class="btn-icon" title="Hapus"><i class="fa-solid fa-trash"></i></button>';
                    $html .= '</form>';
                    $html .= '</div>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }

            // Kembalikan respons JSON dengan HTML dan link pagination
            return response()->json([
                'table_html' => $html,
                'pagination_html' => $data_pembelian->links()->render(),
            ]);
        }

        // Jika bukan AJAX, kembalikan tampilan halaman penuh
        return view('admin.dataPembelian', $data);
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
        $max_int = 2147483647; // Batasan maksimum untuk tipe data INTEGER (32-bit signed) MySQL

        // Validasi
        $validator = Validator::make($request->all(), [
            // pembelian_id WAJIB ADA, karena sudah dibuat oleh AJAX
            'pembelian_id' => 'required|exists:pembelian,id',
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => 'required|exists:perusahaan_cabang,id',
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_tawaran_toko' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0|max:' . $max_int,
            // Kita tidak lagi memvalidasi 'items' di sini
        ], [
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".',
            'harga_tawaran_customer.max' => 'Harga Tawaran Customer melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_tawaran_toko.max' => 'Harga Tawaran Toko melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_deal.max' => 'Harga Deal melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'pembelian_id.required' => 'Terjadi kesalahan. Coba muat ulang halaman. (ID Pembelian tidak ditemukan)'
        ]);

       if ($validator->fails()) {
            // PERBAIKAN 2: Jika validasi gagal, muat ulang data LENGKAP
            if ($request->pembelian_id) {
                try {
                    // Muat ulang data Pembelian DENGAN item draft dan relasi kategori
                    $pembelian = Pembelian::with('item_pembelian_draft.kategori')
                                            ->findOrFail($request->pembelian_id);

                    // Muat ulang data dropdown
                    $data_customer = Customer::orderBy('nama', 'asc')->get();
                    $data_cabang = Branch::orderBy('nama', 'asc')->get();
                    $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get(); // Pastikan Kategori di-load

                    // Kembalikan ke view dengan data LENGKAP dan error
                    return view('admin.inputPembelian', [
                        'pembelian' => $pembelian, // Gantikan input default
                        'semua_customer' => $data_customer,
                        'semua_cabang' => $data_cabang,
                        'semua_kategori' => $data_kategori
                    ])->withErrors($validator)
                      ->withInput(); 

                } catch (\Exception $e) {
                    // Jika ada error saat fetch, kembali ke mode normal
                }
            }
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
                                 ->with('success', 'Draft berhasil disimpan')
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

    /**
     * Print nota pembelian (Synchronous - Direct Download)
     * This method is kept for direct PDF download functionality
     */
    public function printNota($id)
    {
        // 1. Ambil data pembelian dengan semua relasi yang dibutuhkan
        $pembelian = Pembelian::with([
                            'customer',
                            'perusahaan_cabang',
                            'user',
                            'item_pembelian_draft.kategori'
                        ])->findOrFail($id);

        // Pastikan transaksi sudah 'deal' sebelum mencetak nota resmi (opsional)
        if ($pembelian->status_pembelian !== 'deal') {
            return back()->with('error', 'Nota hanya bisa dicetak untuk transaksi yang statusnya "Deal".');
        }

        $data = [
            'pembelian' => $pembelian,
            'title' => 'Nota Pembelian #' . $pembelian->kode_transaksi
        ];

        // 2. Load view template PDF
        // Asumsi template ada di resources/views/admin/notaPembelian.blade.php
        $pdf = Pdf::loadView('admin.notaPembelian', $data);

        // 3. Kembalikan PDF untuk di-stream di browser
        return $pdf->stream('Nota_Pembelian_' . $pembelian->kode_transaksi . '.pdf');
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

    public function ajaxUpdateItemDraft(Request $request, $item_id)
    {
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
            'kelengkapan' => 'nullable|string',
        ];

        if ($request->has('kelengkapan_awal') && !$request->has('kelengkapan')) {
            $request->merge(['kelengkapan' => $request->input('kelengkapan_awal')]);
        }

        $validator = Validator::make($request->all(), $itemRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item = ItemPembelian::findOrFail($item_id);
            $fields = array_keys($itemRules);
            $item->fill($request->only($fields));
            $item->save();
            $item->load('kategori');

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui.',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        // 1. Ambil data pembelian beserta item draft-nya
        $pembelian = Pembelian::with('item_pembelian_draft.kategori')->findOrFail($id);
        // 2. Ambil semua data yang dibutuhkan untuk dropdown di form
        $data_customer = Customer::orderBy('nama', 'asc')->get();
        $data_cabang = Branch::orderBy('nama', 'asc')->get();
        $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // 3. Kirim semua data itu ke view yang SAMA (inputPembelian)
        // Note: Anda perlu membuat file inputPembelian.blade.php bisa mode 'edit'
        return view('admin.inputPembelian', [
            'pembelian' => $pembelian, // Kirim data pembelian yang akan diedit
            'semua_customer' => $data_customer,
            'semua_cabang' => $data_cabang,
            'semua_kategori' => $data_kategori
        ]);
    }

    public function update(Request $request, $id)
    {
        $status = $request->input('status_pembelian', 'draft');
        $max_int = 2147483647; // Batasan maksimum untuk tipe data INTEGER (32-bit signed) MySQL

        // Validasi
        // Perhatikan: Kita sekarang menggunakan $id dari URL route, bukan dari hidden input request
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customer,id',
            'perusahaan_cabang_id' => 'required|exists:perusahaan_cabang,id',
            'user_id' => 'required|exists:users,id',
            'status_pembelian' => 'required|in:draft,deal,tidak_deal',
            'harga_tawaran_customer' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_tawaran_toko' => 'nullable|numeric|min:0|max:' . $max_int,
            'harga_deal' => ($status == 'deal' ? 'required' : 'nullable') . '|numeric|min:0|max:' . $max_int,
        ], [
            'harga_deal.required' => 'Harga Deal wajib diisi jika status "Deal".',
            'harga_tawaran_customer.max' => 'Harga Tawaran Customer melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_tawaran_toko.max' => 'Harga Tawaran Toko melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
            'harga_deal.max' => 'Harga Deal melebihi batas maksimum (Rp ' . number_format($max_int, 0, ',', '.') . ').',
        ]);

        if ($validator->fails()) {
            // PERBAIKAN 3: Jika validasi gagal di mode update, re-fetch dan kirim ulang data lengkap
            try {
                // Muat ulang data Pembelian DENGAN item draft dan kategori
                $pembelian = Pembelian::with('item_pembelian_draft.kategori')
                                        ->findOrFail($id);

                // Muat ulang data dropdown
                $data_customer = Customer::orderBy('nama', 'asc')->get();
                $data_cabang = Branch::orderBy('nama', 'asc')->get();
                $data_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

                // Kembalikan ke view dengan data lengkap dan error
                return view('admin.inputPembelian', [
                    'pembelian' => $pembelian,
                    'semua_customer' => $data_customer,
                    'semua_cabang' => $data_cabang,
                    'semua_kategori' => $data_kategori
                ])->withErrors($validator)
                  ->withInput();
            } catch (\Exception $e) {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Mulai Transaksi Database
        DB::beginTransaction();
        try {
            // Temukan data Pembelian berdasarkan ID dari URL ($id)
            $pembelian = Pembelian::findOrFail($id);

            // Update data Induk
            $pembelian->customer_id = $request->customer_id;
            $pembelian->perusahaan_cabang_id = $request->perusahaan_cabang_id;
            // $pembelian->user_id tidak perlu diupdate karena harusnya tetap
            $pembelian->harga_tawaran_customer = $request->harga_tawaran_customer;
            $pembelian->harga_tawaran_toko = $request->harga_tawaran_toko;
            $pembelian->harga_deal = $request->harga_deal;
            $pembelian->status_pembelian = $status;
            $pembelian->save();

            // Item-item draft sudah diurus oleh AJAX Store/Delete Item

            DB::commit();

            if ($status == 'draft') {
                return redirect()->route('admin.purchases.show', $pembelian->id)
                                 ->with('success', 'Draft berhasil diupdate')
                                 ->with('auto_copy_link', true);
            } else {
                return redirect()->route('admin.purchases.index')
                                 ->with('success', 'Transaksi pembelian telah diupdate dan difinalisasi.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Cari data Pembelian (Induk)
            $pembelian = Pembelian::findOrFail($id);

            // Hapus data Pembelian. Jika foreign key item_pembelian_draft
            // diatur dengan onDelete('cascade'), semua item draft akan ikut terhapus.
            $pembelian->delete();

            return redirect()->route('admin.purchases.index')
                             ->with('success', 'Transaksi Pembelian ' . $pembelian->kode_transaksi . ' berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
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
