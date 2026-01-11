@extends('layouts.admin')

@section('title', 'Data Pelanggan')


@push('page-actions')

@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="search"]');
    if (input) {
        input.focus();
        const length = input.value.length;
        input.setSelectionRange(length, length); // kursor ke akhir
    }
});
</script>
@endpush


@section('content')

{{-- Search & Filter  --}}
<form method="GET" action="{{ route('admin.customers.index') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 customer-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap customer-filter-body">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 customer-filter-input">
                <span class="text-muted ms-2 me-3 customer-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari pelanggan berdasarkan nama, telepon, atau NIK"
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown Sort --}}
            <div class="d-flex align-items-center gap-2 pe-2 customer-filter-controls">
                <select name="sort_by"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ ($sort_by ?? 'updated_at') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                </select>

                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0 customer-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive customer-table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th>Kode</th>
                        <th style="width: 15%;">Nama Pelanggan</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th style="width: 20%;">Alamat</th>
                        <th>NIK</th>
                        <th class="text-center">Total Transaksi</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_pelanggan as $index => $pelanggan)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.customers.show', $pelanggan->id) }}" style="cursor: pointer;">
                        <td class="text-center text-muted fw-bold">{{ ($data_pelanggan->firstItem() ?? 0) + $index }}</td>
                        <td>
                            <span class="fw-medium text-secondary font-monospace">
                                {{ $pelanggan->kode_customer ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                {{ $pelanggan->nama }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">
                                @if(in_array(strtolower($pelanggan->jenis_kelamin), ['laki-laki', 'l']))
                                    Laki-laki
                                @elseif(in_array(strtolower($pelanggan->jenis_kelamin), ['perempuan', 'p']))
                                    Perempuan
                                @else
                                    {{ $pelanggan->jenis_kelamin }}
                                @endif
                            </span>
                        </td>

                        <td>
                            <span class="fw-medium text-secondary phone-display" data-raw="{{ $pelanggan->no_telp }}">
                            </span>
                        </td>

                        <td>
                            <span class="text-muted small d-block"
                                  style="line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $pelanggan->alamat ?? '-' }}
                            </span>
                        </td>

                        <td>
                            <span class="text-muted small d-block"
                                  style="line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $pelanggan->identitas ?? '-' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $totalTransaksi = ($pelanggan->total_penjualan ?? 0) + ($pelanggan->total_pembelian ?? 0);
                            @endphp
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                {{ $totalTransaksi }} transaksi
                            </span>
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.customers.edit', $pelanggan->id) }}"
                                   class="btn-action btn-action-edit"
                                   title="Edit"
                                   onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-users fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data pelanggan</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_pelanggan->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_pelanggan->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        text-decoration: underline !important;
        opacity: 0.8;
    }
    .customer-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .customer-table-responsive .table {
        width: 100%;
        min-width: 100%;
    }
    .customer-table-responsive .table th,
    .customer-table-responsive .table td {
        white-space: nowrap;
    }
    .customer-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .customer-filter-body {
            gap: 0.5rem;
        }
        .customer-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .customer-filter-card .customer-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .customer-filter-icon {
            margin-left: 0 !important;
        }
        .customer-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .customer-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .customer-filter-card .customer-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .customer-filter-body {
            padding: 0.75rem !important;
        }
        .customer-filter-input .form-control {
            font-size: 0.85rem;
        }
        .customer-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .customer-filter-controls .form-select {
            width: 100%;
        }
        .customer-table-card {
            border-radius: 12px;
        }
        .customer-table-responsive {
            padding: 0 0.75rem;
        }
        .customer-table-responsive .table {
            min-width: 900px;
        }
    }
</style>
@endpush
