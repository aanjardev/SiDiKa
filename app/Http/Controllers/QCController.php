<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPembelian;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class QCController extends Controller
{
    /**
     * Tampilkan halaman daftar item yang menunggu QC.
     */
    public function index(Request $request)
    {
        // 1. Ambil semua kategori untuk filter dropdown
        $semua_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // 2. Bangun query dasar: item menunggu QC dari pembelian yang 'deal'
        $query = ItemPembelian::with(['pembelian', 'kategori'])
            ->where('status_qc', 'menunggu_qc')
            ->whereHas('pembelian', function ($q) {
                $q->where('status_pembelian', 'deal');
            });

        // 3. Filter: search (nama item, serial, atau kode transaksi)
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('nama_item', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('serial_lens', 'like', "%{$s}%")
                  ->orWhereHas('pembelian', function ($q2) use ($s) {
                      $q2->where('kode_transaksi', 'like', "%{$s}%");
                  });
            });
        }

        // 4. Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->input('kategori'));
        }

        // 5. Sort
        $sort = $request->input('sort', 'terbaru');
        if ($sort === 'terlama') {
            $query->oldest('created_at');
        } else {
            $query->latest('created_at');
        }

        // 6. Paginate
        $data_qc = $query->paginate(10);

        // 7. Jika AJAX -> kembalikan JSON berisi HTML partial untuk tbody + pagination
        if ($request->ajax() || $request->wantsJson()) {
            $table_html = view('admin.partials.qc_table_rows', compact('data_qc'))->render();
            $pagination_html = $data_qc->hasPages() ? $data_qc->appends($request->query())->links('pagination::bootstrap-5')->render() : '';
            return response()->json([
                'table_html' => $table_html,
                'pagination_html' => $pagination_html,
            ]);
        }

        // 8. Normal page render
        return view('admin.dataQC', [
            'data_qc' => $data_qc,
            'semua_kategori' => $semua_kategori,
            'search_term' => $request->input('search', ''),
            'sort_filter' => $sort,
        ]);
    }

    /**
     * Tampilkan halaman/form untuk memPROSES item QC.
     */
    public function edit($id)
    {
        $item = ItemPembelian::with(['pembelian', 'kategori'])->findOrFail($id);
        $semua_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();
        return view('admin.dataQC_form', [
            'item' => $item,
            'semua_kategori' => $semua_kategori,
        ]);
    }

    /**
     * Update item setelah diproses QC.
     */
    public function update(Request $request, $id)
    {
        $item = ItemPembelian::findOrFail($id);

        // Get status_qc dari request
        $statusQc = $request->input('status_qc', $item->status_qc);
        $action = $request->input('action', 'save');

        // Define base rules untuk semua status
        $baseRules = [
            'nama_item' => 'nullable|string|max:200',
            'kategori_id' => 'nullable|exists:kategori,id',
            'kode_sku' => 'nullable|string|max:20',
            'serial_number' => 'nullable|string|max:50',
            'serial_lens' => 'nullable|string|max:50',
            'kelengkapan' => 'nullable|string',
            'qty' => 'nullable|integer|min:1',
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
            'harga_jual' => 'nullable|integer|min:0',
            'harga_beli' => 'nullable|integer|min:0',
            'harga_servis' => 'nullable|integer|min:0',
            'grade' => 'nullable|in:Unggulan,Standar,Minus',
            'status' => 'nullable|in:Second,Baru',
            'deskripsi_produk' => 'nullable|string',
            'status_qc' => 'nullable|in:menunggu_qc,lolos_qc,gagal_qc,diarsipkan',
            'catatan_qc' => 'nullable|string',
        ];

        // Rules khusus berdasarkan status QC
        if ($statusQc === 'lolos_qc' || $action === 'save') {
            // Untuk lolos_qc, field tertentu harus required
            $rules = array_merge($baseRules, [
                'nama_item' => 'required|string|max:200',
                'kategori_id' => 'required|exists:kategori,id',
                'kode_sku' => 'required|string|max:20',
                'harga_jual' => 'required|integer|min:1',
                'deskripsi_produk' => 'required|string',
                'qty' => 'required|integer|min:1',
            ]);
        } elseif ($statusQc === 'gagal_qc' || $statusQc === 'diarsipkan') {
            // Untuk gagal/diarsipkan, semua optional
            $rules = $baseRules;
        } else {
            // Untuk menunggu_qc (draft), minimal nama_item dan kategori
            $rules = array_merge($baseRules, [
                'nama_item' => 'required|string|max:200',
                'kategori_id' => 'required|exists:kategori,id',
            ]);
        }

        // Validasi data
        $data = $request->validate($rules);

        // Force status berdasarkan action
        if ($action === 'archive') {
            $data['status_qc'] = 'diarsipkan';
            $statusQc = 'diarsipkan';
        } elseif ($action === 'draft') {
            $data['status_qc'] = 'menunggu_qc';
            $statusQc = 'menunggu_qc';
        }

        // Handle different QC outcomes
        if ($statusQc === 'lolos_qc') {
            // Create or update product in `produk` table
            DB::beginTransaction();
            try {
                // Ensure there's a SKU; generate one if missing
                $kodeSku = $data['kode_sku'] ?? null;
                if (empty($kodeSku)) {
                    $kodeSku = 'QC-' . time() . '-' . rand(100, 999);
                    $data['kode_sku'] = $kodeSku;
                }

                // Prepare product payload
                $productPayload = [
                    'kode_sku' => $kodeSku,
                    'id_kategori' => $data['kategori_id'] ?? $item->kategori_id,
                    'nama_produk' => $data['nama_item'] ?? $item->nama_item,
                    'harga_jual' => $data['harga_jual'] ?? $item->harga_jual,
                    'harga_beli' => $data['harga_beli'] ?? $item->harga_beli,
                    'harga_servis' => $data['harga_servis'] ?? $item->harga_servis,
                    'stok_produk' => $data['qty'] ?? $item->qty ?? 0,
                    'deskripsi_produk' => $data['deskripsi_produk'] ?? $item->deskripsi_produk,
                    'status' => $data['status'] ?? $item->status,
                    'grade' => $data['grade'] ?? $item->grade,
                    'serial_number' => $data['serial_number'] ?? $item->serial_number,
                    'serial_lens' => $data['serial_lens'] ?? $item->serial_lens,
                    'kelengkapan' => $data['kelengkapan'] ?? $item->kelengkapan,
                ];

                // If a product with same SKU exists, update it (increase stock), otherwise create
                $existing = Produk::where('kode_sku', $kodeSku)->first();
                if ($existing) {
                    $existing->nama_produk = $productPayload['nama_produk'];
                    $existing->id_kategori = $productPayload['id_kategori'];
                    $existing->harga_jual = $productPayload['harga_jual'];
                    $existing->harga_beli = $productPayload['harga_beli'];
                    $existing->harga_servis = $productPayload['harga_servis'];
                    $existing->status = $productPayload['status'];
                    $existing->grade = $productPayload['grade'];
                    $existing->serial_number = $productPayload['serial_number'];
                    $existing->serial_lens = $productPayload['serial_lens'];
                    $existing->kelengkapan = $productPayload['kelengkapan'];
                    $existing->stok_produk = ($existing->stok_produk ?? 0) + ($productPayload['stok_produk'] ?? 0);
                    $existing->deskripsi_produk = $productPayload['deskripsi_produk'];
                    $existing->save();
                    $createdProduct = $existing;
                } else {
                    $createdProduct = Produk::create($productPayload);
                }

                // mark item as lolos_qc and save all fields
                $item->fill($data);
                $item->status_qc = 'lolos_qc';
                $item->save();

                DB::commit();

                // Redirect back to QC list
                return redirect()->route('admin.quality-control.index')
                    ->with('success', 'Item lolos QC dan dipindahkan ke tabel Produk. Kode Produk: ' . $kodeSku);

            } catch (\Throwable $th) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Gagal memindahkan item ke produk: ' . $th->getMessage()]);
            }
        }

        // Jika status gagal_qc atau diarsipkan
        if (in_array($statusQc, ['gagal_qc', 'diarsipkan'])) {
            $item->fill($data);
            $item->save();

            return redirect()->route('admin.quality-control.archived')
                ->with('success', 'Item telah dipindahkan ke arsip produk.');
        }

        // Default: simpan perubahan sebagai draft / menunggu QC
        $item->fill($data);
        $item->save();

        $message = $action === 'draft'
            ? 'Item QC berhasil disimpan sebagai draft.'
            : 'Item QC berhasil diperbarui.';

        return redirect()->route('admin.quality-control.index')->with('success', $message);
    }

    /**
     * Tampilkan daftar item yang diarsipkan (tidak layak jual).
     */
    public function archived(Request $request)
    {
        // Ambil parameter dari request
        $search_term = $request->input('search');
        $selected_kategori = $request->input('kategori', 'all');
        $sort_by = $request->input('sort_by', 'updated_at');
        $sort_order = $request->input('sort_order', 'desc');

        // Ambil semua kategori untuk dropdown filter
        $semua_kategori = Kategori::orderBy('nama_kategori', 'asc')->get();

        // Query dasar
        $query = ItemPembelian::with(['pembelian', 'kategori'])
            ->whereIn('status_qc', ['diarsipkan', 'gagal_qc'])
            ->whereHas('pembelian', function ($q) {
                $q->where('status_pembelian', 'deal');
            });

        // === SEARCH ===
        if ($search_term) {
            $query->where(function ($q) use ($search_term) {
                $q->where('nama_item', 'like', "%{$search_term}%")
                ->orWhere('serial_number', 'like', "%{$search_term}%")
                ->orWhereHas('pembelian', function ($q2) use ($search_term) {
                    $q2->where('kode_transaksi', 'like', "%{$search_term}%");
                });
            });
        }

        // === FILTER KATEGORI ===
        if ($selected_kategori !== 'all') {
            $query->where('kategori_id', $selected_kategori);
        }

        // === SORTING ===
        switch ($sort_by) {
            case 'nama_item':
                $query->orderBy('nama_item', 'asc');
                break;
            case 'nama_item_desc':
                $query->orderBy('nama_item', 'desc');
                break;
            case 'pembelian_id':
                $query->orderBy('pembelian_id', 'asc');
                break;
            case 'updated_at':
            default:
                $query->orderBy('updated_at', $sort_order);
                break;
        }

        // Pagination
        $data_qc = $query->paginate(15)->withQueryString();

        return view('admin.dataQC_archived', compact(
            'data_qc',
            'semua_kategori',
            'search_term',
            'selected_kategori',
            'sort_by',
            'sort_order'
        ));
    }

    /**
     * Restore an archived item back to 'lolos_qc'.
     */
    public function restore(Request $request, $id)
    {
        $item = ItemPembelian::findOrFail($id);
        // Kembalikan ke antrian QC (menunggu_qc), bukan langsung lolos
        $item->status_qc = 'menunggu_qc';
        $item->save();
        return redirect()->back()->with('success', 'Item dikembalikan ke antrian QC untuk diperiksa ulang.');
    }
}
