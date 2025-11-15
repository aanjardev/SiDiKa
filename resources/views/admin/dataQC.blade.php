@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
    {{-- Halaman QC tidak perlu tombol "Tambah" --}}
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
            <input type="text" class="form-control" placeholder="Cari nama item atau Serial Number..." style="border-left: 0; box-shadow: none;">
        </div>
    </div>

    {{-- Filter Kategori (Nanti diisi dari controller) --}}
    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option value="" selected>Semua Kategori</option>
        {{-- @foreach ($semua_kategori as $kat)
            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
        @endforeach --}}
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

                {{-- ======================================================= --}}
                {{-- PERUBAHAN: Head Tabel (thead) sesuai permintaan --}}
                {{-- ======================================================= --}}
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 100px;">ID Beli</th>
                        <th style="width: 30%;">Nama Item</th>
                        <th>Serial Number</th>
                        <th>Serial Number Lensa</th>
                        <th>Kategori</th>
                        <th style="width: 15%">Persentase</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_qc as $item)
                        <tr>
                            <td class="text-center">#{{ $item->pembelian_id }}</td>
                            <td>{{ $item->nama_item }}</td>
                            <td>{{ $item->serial_number ?? 'N/A' }}</td>
                            <td>{{ $item->serial_lens ?? 'N/A' }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? 'N/A' }}</td>
                            <td>
                                {{--
                                  PERUBAHAN: Menampilkan "Persentase"
                                  (Menggunakan accessor 'persentase_lengkap' dari Model)
                                --}}
                                @php $persen = round($item->persentase_lengkap); @endphp
                                <div class="progress" role="progressbar" style="height: 12px;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: {{ $persen }}%"></div>
                                </div>
                                <span class="small text-muted">{{ $persen }}% Lengkap</span>
                            </td>
                            <td class="text-center">
                                {{-- Tombol Aksi "Proses QC" (Link ke route 'edit') --}}
                                <a href="{{ route('admin.qc.edit', $item->id) }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1 px-2 mx-auto" style="width: fit-content;">
                                    <i class="fa-solid fa-clipboard-check fa-fw"></i>
                                    <span>Proses</span>
                                </a>
                            </td>
                        </tr>

                    {{-- Bagian @empty state --}}
                    @empty
                        <tr class="tr-empty">
                            <td colspan="7" class="text-center"> {{-- Colspan 7 (sesuai jumlah <th>) --}}
                                <div>
                                    <i class="fa-solid fa-check-circle fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Item Menunggu QC</h5>
                                    <p class="text-muted mb-0">Semua item dari transaksi 'Deal' akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination (Dinamis dari Controller) --}}
    @if ($data_qc->hasPages())
        <div class="card-footer bg-white">
            {{ $data_qc->links('pagination::bootstrap-5') }}
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
