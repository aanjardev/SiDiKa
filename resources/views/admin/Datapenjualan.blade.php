@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
<a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Penjualan</span>
</a>
@endpush

@section('content')

<form action="{{ route('admin.sales.index') }}" method="GET" id="filterForm">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">

        {{-- SEARCH --}}
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text" style="background: #fff; border-right: 0;">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text" name="search" id="search-input"
                    class="form-control"
                    placeholder="Cari Kode Penjualan atau Nama Customer…"
                    style="border-left:0; box-shadow:none;"
                    value="{{ request('search') }}">
            </div>
        </div>

        {{-- FILTER KATEGORI --}}
        <select name="kategori" id="filter-kategori"
            class="form-select w-auto shadow-sm"
            style="height: calc(2.5rem + 10px);">

            <option value="">Semua Kategori</option>

            @foreach ($semua_kategori as $kat)
            <option value="{{ $kat->id }}"
                {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                {{ $kat->nama_kategori }}
            </option>
            @endforeach
        </select>

        {{-- SORT --}}
        <select name="sort" id="filter-sort"
            class="form-select w-auto shadow-sm"
            style="height: calc(2.5rem + 10px);">

            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>Terlama</option>
        </select>

    </div>
</form>

<div id="sales-list-container">

    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">

                <table class="table align-middle mb-0 table-product">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th style="width: 25%">Item Terjual</th>
                            <th>Cabang</th>
                            <th>Total</th>
                            <th class="text-center">Aksi</th>
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
            <div class="card-footer bg-white">
                {{ $data_penjualan->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const form = document.getElementById('filterForm');
        const searchInput = document.getElementById('search-input');
        const kategori = document.getElementById('filter-kategori');
        const sort = document.getElementById('filter-sort');

        const tableBody = document.getElementById('sales-table-body');
        const paginationContainer = document.getElementById('pagination-links-container');

        const baseUrl = "{{ route('admin.sales.index') }}";

        function formToParams() {
            const data = new FormData(form);
            return new URLSearchParams(data).toString();
        }

        function buildUrl() {
            return `${baseUrl}?${formToParams()}`;
        }

        function fetchSales(url) {
            fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = data.table_html;
                    paginationContainer.innerHTML = data.pagination_html;
                    attachPaginationEvents();
                });
        }

        let timeout = null;
        searchInput.addEventListener('keyup', () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => fetchSales(buildUrl()), 400);
        });

        kategori.addEventListener('change', () => fetchSales(buildUrl()));
        sort.addEventListener('change', () => fetchSales(buildUrl()));

        function attachPaginationEvents() {
            paginationContainer.querySelectorAll("a").forEach(a => {
                a.addEventListener("click", function(e) {
                    e.preventDefault();
                    fetchSales(this.href);
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush


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

    .table-product tbody tr.align-middle>td {
        vertical-align: middle;
    }

    /* Style untuk Tombol Hapus (btn-icon) */
    button.btn-icon,
    .table-product button.btn-icon,
    form .btn-icon {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        color: #dc3545 !important;
        cursor: pointer !important;
        font-size: 16px !important;
        line-height: 1 !important;
        appearance: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    .btn-icon i,
    .btn-icon svg,
    .btn-icon .fa-solid {
        color: inherit !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }

    button.btn-icon:focus,
    button.btn-icon:active,
    .btn-icon:focus,
    .btn-icon:active {
        outline: none !important;
        box-shadow: none !important;
    }

    .btn-icon:hover {
        color: #bb2d3b !important;
    }

    /* CSS UNTUK TINGGI TABEL FIX & EMPTY STATE */
    .table-wrapper {
        /* Tabel mengambil tinggi natural sesuai konten */
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .table-responsive {
        /* Normal table responsive behavior */
        flex: 1;
    }

    .table-product {
        /* Normal table behavior */
        margin-bottom: 0;
    }

    .table-product tr.tr-empty {
        /* Empty state row */
        display: table-row;
    }

    .table-product tr.tr-empty td {
        vertical-align: middle;
        padding-top: 0;
        padding-bottom: 0;
    }

    .table-product tr.tr-empty td .empty-message {
        min-height: 400px;
        width: 100%;
    }

    .tr-empty td {
        border: none;
    }

    .table-product tbody tr:not(.tr-empty) td {
        vertical-align: top !important;
        padding: 0.75rem 0.75rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const form = document.getElementById('filterForm');
        const search = document.getElementById('search-input');
        const kategori = document.getElementById('filter-kategori');
        const sort = document.getElementById('filter-sort');

        const tableBody = document.getElementById('sales-table-body');
        const paginationContainer = document.getElementById('pagination-links-container');

        const baseUrl = "{{ route('admin.sales.index') }}";

        let typingTimeout = null;
        let isFetching = false;

        function buildUrl() {
            const params = new URLSearchParams(form.serialize());
            return `${baseUrl}?${params.toString()}`;
        }

        function fetchSales(url) {
            if (isFetching) return;
            isFetching = true;

            fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    tableBody.innerHTML = data.table_html;
                    paginationContainer.innerHTML = data.pagination_html;

                    attachPaginationEvents();
                })
                .finally(() => {
                    isFetching = false;
                });
        }

        search.addEventListener('keyup', function() {
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                fetchSales(buildUrl());
            }, 400);
        });

        kategori.addEventListener('change', () => fetchSales(buildUrl()));
        sort.addEventListener('change', () => fetchSales(buildUrl()));

        function attachPaginationEvents() {
            paginationContainer.querySelectorAll("a").forEach(a => {
                a.addEventListener("click", function(e) {
                    e.preventDefault();
                    fetchSales(this.href);
                });
            });
        }

        attachPaginationEvents();
    });
</script>
@endpush