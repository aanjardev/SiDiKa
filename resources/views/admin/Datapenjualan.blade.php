@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
<a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Penjualan</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<form action="{{ route('admin.sales.index') }}" method="GET" id="filterForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                    name="search"
                    id="search-input"
                    class="form-control border-0 shadow-none bg-transparent"
                    placeholder="Cari ID Penjualan atau Nama Customer..."
                    value="{{ request('search') }}"
                    style="font-size: 0.95rem;"
                    autofocus>
            </div>

            <div class="d-flex align-items-center gap-2 pe-2">
                <select name="cabang"
                    id="filter-cabang"
                    class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                    style="cursor: pointer;">
                    <option value="">Semua Cabang</option>
                    @foreach ($semua_cabang as $cab)
                    <option value="{{ $cab->id }}" {{ request('cabang') == $cab->id ? 'selected' : '' }}>
                        {{ $cab->nama }}
                    </option>
                    @endforeach
                </select>

                <select name="sort"
                    id="filter-sort"
                    class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                    style="cursor: pointer;">
                    <option value="terbaru" {{ (request('sort', 'terbaru')) == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div id="sales-list-container">
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
                            <th style="width: 25%;">Item Terjual</th>
                            <th>Cabang</th>
                            <th>Total</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sales-table-body">
                        @include('admin.partials.sales_table_content', ['data_penjualan' => $data_penjualan])
                    </tbody>
                </table>
            </div>
        </div>

        <div id="pagination-links-container">
            @if ($data_penjualan->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $data_penjualan->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

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

<script src="{{ asset('js/admin-ajax-table.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const baseUrl = "{{ route('admin.sales.index') }}";

        TableAjax.init({
            formSelector: '#filterForm',
            containerSelector: '#sales-list-container',
            tableBodySelector: '#sales-table-body',
            paginationSelector: '#pagination-links-container',
            baseUrl: baseUrl,
            searchInputSelector: '#search-input',
            filterSelectors: ['#filter-cabang', '#filter-sort'],
            rowClick: {
                selector: '.sales-row',
                ignoreSelector: '.no-row-navigation',
                urlFrom: (row) => row.dataset.detailUrl,
            },
        });
    });
</script>
@endpush
