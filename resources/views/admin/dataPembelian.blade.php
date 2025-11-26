@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('styles')
<style>
    .purchase-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .purchase-row:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('page-actions')
<a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Pembelian</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian (Visual: HEAD, Logic: Main) --}}
<form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       id="search-input"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari Kode Pembelian atau Nama Customer..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
           <div class="d-flex align-items-center gap-2 pe-2">

                {{-- Filter Status --}}
                <select name="status" id="filter-status"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="semua" {{ ($status_filter ?? 'semua') == 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="draft" {{ ($status_filter ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="deal" {{ ($status_filter ?? '') == 'deal' ? 'selected' : '' }}>Deal</option>
                    <option value="tidak_deal" {{ ($status_filter ?? '') == 'tidak_deal' ? 'selected' : '' }}>Tidak Deal</option>
                </select>

                {{-- Filter Urutkan --}}
                <select name="sort" id="filter-sort"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>

                {{-- Filter Cabang --}}
                <select name="cabang" id="filter-cabang"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="">Semua Cabang</option>
                    @foreach($semua_cabang ?? [] as $cab)
                        <option value="{{ $cab->id }}" {{ ($filter_cabang ?? '') == $cab->id ? 'selected' : '' }}>
                            {{ $cab->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card (Wrapper ID dari Main untuk AJAX) --}}
<div id="purchase-list-container">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th style="width: 25%;">Item Dibeli</th>
                            <th class="text-center">Status</th>
                            <th>Harga Deal</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    {{-- ID Body dari Main untuk AJAX Replacement --}}
                    <tbody id="purchase-table-body">
                    @include('admin.partials.purchase_table_content', ['data_pembelian' => $data_pembelian])
                </table>
            </div>
        </div>

        {{-- Pagination Container (ID dari Main untuk AJAX) --}}
        <div id="pagination-links-container">
            @if ($data_pembelian->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $data_pembelian->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-ajax-table.js') }}"></script>
<script>
    function confirmDelete(button) {
        confirmDeleteRecord(button, 'Apakah Anda yakin ingin menghapus data pembelian ini?');
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlIndex = '{{ route('admin.purchases.index') }}';

        TableAjax.init({
            formSelector: '#filterForm',
            containerSelector: '#purchase-list-container',
            tableBodySelector: '#purchase-table-body',
            paginationSelector: '#pagination-links-container',
            baseUrl: urlIndex,
            searchInputSelector: '#search-input',
            filterSelectors: ['#filter-status', '#filter-sort', '#filter-cabang'],
            rowClick: {
                selector: '.purchase-row',
                ignoreSelector: '.no-row-navigation',
                urlFrom: (row) => row.dataset.detailUrl,
            },
        });
    });
</script>
@endpush
