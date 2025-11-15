@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('page-actions')
    <a href="#" {{-- Nanti: route('admin.purchases.create') --}} class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Pembelian</span>
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
            <input type="text" class="form-control" placeholder="Cari Kode Pembelian (ID) atau Nama Customer..." style="border-left: 0; box-shadow: none;">
        </div>
    </div>
    {{-- Filter Status (Sesuai migrasi baru) --}}
    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option value="" selected>Semua Status</option>
        <option value="draft">Draft</option>
        <option value="deal">Deal</option>
        <option value="tidak_deal">Tidak Deal</option>
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
                    {{-- Kolom Tabel (thead) --}}
                    <tr>
                        <th class="text-center" style="width: 60px;">ID</th>
                        <th>Customer</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th style="width: 25%">Item Dibeli</th>
                        <th>Status</th>
                        <th>Harga Deal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{--
                      Gunakan @forelse dengan array kosong [] untuk simulasi "Tidak Ada Data"
                      Nanti, ganti [] dengan $data_pembelian (dari Controller)
                    --}}
                    @forelse ($data_pembelian as $pembelian)
                        <tr>
                            <td class="text-center">#{{ $pembelian->id }}</td>
                            <td>{{ $pembelian->customer->nama ?? 'N/A' }}</td>
                            <td>{{ $pembelian->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $pembelian->perusahaan_cabang->nama ?? 'N/A' }}</td>
                            <td>
                                @php
                                    // 1. Ambil semua nama item dari relasi yg sudah di-load
                                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');

                                    // 2. Batasi panjangnya ke 40 karakter
                                    echo \Illuminate\Support\Str::limit($itemNames, 40, '...');
                                @endphp
                            </td>
                            <td>
                                {{-- Logika Status --}}
                                @if($pembelian->status_pembelian == 'deal')
                                    <span class="badge bg-success-subtle text-success-emphasis">Deal</span>
                                @elseif($pembelian->status_pembelian == 'tidak_deal')
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Tidak Deal</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Draft</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" {{-- route('admin.purchases.show', $pembelian->id) --}} title="Lihat Detail Transaksi">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    <a href="#" {{-- route('admin.purchases.edit', $pembelian->id) --}} title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="#" {{-- route('admin.purchases.destroy', $pembelian->id) --}} method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    {{-- Ini bagian @empty yang akan muncul jika $data_pembelian kosong --}}
                    @empty
                        <tr class="tr-empty">
                            <td colspan="8" class="text-center"> {{-- Colspan 8 --}}
                                <div>
                                    <i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Pembelian</h5>
                                    <p class="text-muted mb-0">Silakan <a href="#">tambah transaksi pembelian</a> baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_pembelian->hasPages())
        <div class="card-footer bg-white">
            {{-- Ini akan otomatis menampilkan link pagination (1, 2, 3, Next, Prev) --}}
            {{ $data_pembelian->links('pagination::bootstrap-5') }}
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
        /* Ini akan membuat <tr> mengisi sisa ruang */
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
