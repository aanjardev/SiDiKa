@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
    <a href="#" {{-- Nanti: route('admin.sales.create') --}} class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Penjualan</span>
    </a>
@endpush

@section('content')

{{-- Search & Filter --}}
<div class="d-flex flex-wrap gap-2 align-items-center mb-4">
    {{-- Search Bar --}}
    <div class="flex-grow-1">
        <div class="input-group shadow-sm">
            <span class="input-group-text" style="background: #fff; border-right: 0;">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari Kode Penjualan (ID) atau Nama Customer..." style="border-left: 0; box-shadow: none;">
        </div>
    </div>

    {{-- Filter Kategori (Dinamis dari Controller) --}}
    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option value="" selected>Semua Kategori</option>
        @foreach ($semua_kategori as $kat)
            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
        @endforeach
    </select>

    {{-- Filter Urutkan --}}
    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option selected>Terbaru</option>
        <option>Terlama</option>
    </select>
</div>


<div class="card shadow-sm">
    <div class="card-body p-0 table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-product">
                <thead class="table-light">
                    {{-- ======================================================= --}}
                    {{-- PERUBAHAN: Kolom Tabel (thead) untuk Penjualan --}}
                    {{-- ======================================================= --}}
                    <tr>
                        <th class="text-center" style="width: 60px;">ID</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th style="width: 25%">Item Terjual</th> {{-- <-- KOLOM BARU --}}
                        <th>Cabang</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ======================================================= --}}
                    {{-- PERUBAHAN: Menggunakan data $data_penjualan --}}
                    {{-- ======================================================= --}}
                    @forelse ($data_penjualan as $penjualan)
                        <tr>
                            <td class="text-center">#{{ $penjualan->id }}</td>
                            <td>{{ $penjualan->customer->nama ?? 'N/A' }}</td>
                            <td>{{ $penjualan->created_at->format('d M Y') }}</td>

                            {{-- Kolom Item Terjual --}}
                            <td>
                                @php
                                    // 1. Ambil semua nama produk dari relasi yg sudah di-load
                                    $itemNames = $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ');

                                    // 2. Batasi panjangnya ke 40 karakter
                                    echo \Illuminate\Support\Str::limit($itemNames, 40, '...');
                                @endphp
                            </td>

                            <td>{{ $penjualan->perusahaan_cabang->nama ?? 'N/A' }}</td>
                            <td>Rp {{ number_format($penjualan->harga_total, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" {{-- route('admin.sales.show', $penjualan->id) --}} title="Lihat Detail Transaksi">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    <a href="#" {{-- route('admin.sales.edit', $penjualan->id) --}} title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="#" {{-- route('admin.sales.destroy', $penjualan->id) --}} method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    {{-- Bagian @empty state --}}
                    @empty
                        <tr class="tr-empty">
                            <td colspan="7" class="text-center"> {{-- Colspan 7 (sesuai jumlah <th>) --}}
                                <div>
                                    <i class="fa-solid fa-receipt fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Penjualan</h5>
                                    <p class="text-muted mb-0">Belum ada transaksi penjualan yang tercatat.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination (Dinamis dari Controller) --}}
    @if ($data_penjualan->hasPages())
        <div class="card-footer bg-white">
            {{ $data_penjualan->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection

{{-- PENTING: Salin SEMUA style dari master template --}}
@push('styles')
<style>
    .table {
        border-radius: 5px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-product tbody tr:nth-child(even) {
        background-color: #F8F9FC;
    }
    .table-product tbody tr:hover {
        background-color: #EFF3F9;
        transition: 0.2s;
    }

    /* Style untuk Tombol Hapus (btn-icon) */
    button.btn-icon,
    .table-product button.btn-icon,
    form .btn-icon {
        background: transparent !important; border: none !important;
        padding: 0 !important; color: #dc3545 !important;
        cursor: pointer !important; font-size: 16px !important;
        line-height: 1 !important; appearance: none !important;
        box-shadow: none !important; outline: none !important;
    }
    .btn-icon i, .btn-icon svg, .btn-icon .fa-solid {
        color: inherit !important; fill: currentColor !important;
        stroke: currentColor !important;
    }
    button.btn-icon:focus, button.btn-icon:active,
    .btn-icon:focus, .btn-icon:active {
        outline: none !important; box-shadow: none !important;
    }
    .btn-icon:hover { color: #bb2d3b !important; }

    /* CSS UNTUK TINGGI TABEL FIX & EMPTY STATE */
    .table-wrapper {
        min-height: 700px; /* Atur tinggi minimal */
        display: flex;
        flex-direction: column;
    }
    .table-responsive {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .table-product {
        flex-grow: 1;
    }
    .table-product tr.tr-empty {
        flex-grow: 1;
        display: table-row;
    }
    .table-product tr.tr-empty td {
        vertical-align: middle;
        padding-top: 0;
        padding-bottom: 0;
    }
</style>
@endpush
