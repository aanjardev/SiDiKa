@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
    {{-- Tombol untuk melihat arsip produk (tidak layak jual) --}}
    <a href="{{ url('admin/quality-control/archived') }}" class="btn btn-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-archive fa-fw"></i>
        <span>Arsip Produk</span>
    </a>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('qc-list-container');
    const tableBody = document.getElementById('qc-table-body');
    const paginationContainer = document.getElementById('qc-pagination-links-container');
    const form = document.getElementById('filterFormQc');
    const searchInput = form.querySelector('input[name="search"]');
    const kategoriFilter = form.querySelector('select[name="kategori"]');
    const sortFilter = form.querySelector('select[name="sort"]');
    const urlIndex = '{{ route('admin.quality-control.index') }}';

    let isFetching = false;
    let searchTimeout;

    // No delete actions on QC list (archiving handled in process page)

    // Fetch function: try to accept JSON, otherwise fallback to HTML
    async function fetchQc(url) {
        if (!url) return;
        isFetching = true;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json, text/html' } });
            if (!res.ok) throw new Error('Network response was not ok. Status: ' + res.status);

            const ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') !== -1) {
                const data = await res.json();
                if (data.table_html !== undefined) {
                    tableBody.innerHTML = data.table_html;
                } else {
                    // maybe returned an array of items rendered server-side
                    tableBody.innerHTML = data;
                }
                if (data.pagination_html !== undefined && paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html;
                }
            } else {
                // treat as HTML fragment returned (replace tbody or entire container)
                const text = await res.text();
                // try to extract tbody and pagination if server returns full HTML
                // simple fallback: replace tbody innerHTML with returned text
                tableBody.innerHTML = text;
            }

            // re-attach handlers for pagination links
            attachPaginationHandler();

        } catch (err) {
            console.error('Gagal memuat data:', err);
            // if error, optionally render empty row
            tableBody.innerHTML = `\n                <tr class="tr-empty">\n                    <td colspan="7" class="p-0">\n                        <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">\n                            <i class="fa-solid fa-check-circle fa-2x text-muted mb-3"></i>\n                            <h5 class="mb-1">Gagal memuat data</h5>\n                            <p class="text-muted mb-0">Silakan coba lagi atau cek log server.</p>\n                        </div>\n                    </td>\n                </tr>\n            `;
        } finally {
            isFetching = false;
        }
    }

    function buildUrl(overrideUrl) {
        if (overrideUrl) return overrideUrl;
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim() !== '') params.set('search', searchInput.value.trim());
        if (kategoriFilter && kategoriFilter.value !== '') params.set('kategori', kategoriFilter.value);
        if (sortFilter && sortFilter.value !== '') params.set('sort', sortFilter.value);
        const qs = params.toString();
        return urlIndex + (qs ? ('?' + qs) : '');
    }

    // Pagination links: intercept clicks
    function attachPaginationHandler() {
        if (!paginationContainer) return;
        paginationContainer.querySelectorAll('a').forEach(a => {
            a.removeEventListener && a.removeEventListener('click', handlePaginationClick);
            a.addEventListener('click', handlePaginationClick);
        });
    }

    function handlePaginationClick(e) {
        const href = this.getAttribute('href');
        if (!href) return;
        e.preventDefault();
        fetchQc(href);
    }

    // --- Event Listeners ---
    // search input with debounce
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchQc(buildUrl());
            }, 450);
        });
    }

    if (kategoriFilter) {
        kategoriFilter.addEventListener('change', function() { fetchQc(buildUrl()); });
    }
    if (sortFilter) {
        sortFilter.addEventListener('change', function() { fetchQc(buildUrl()); });
    }

    // initial attach
    attachPaginationHandler();

});
</script>
@endpush

@section('content')

{{-- Search & Filter (mirip dataPembelian) --}}
<form action="{{ route('admin.quality-control.index') }}" method="GET" id="filterFormQc">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        {{-- Search Bar --}}
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text" style="background: #fff; border-right: 0;">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                {{-- Tambahkan ID & name untuk JS --}}
                <input type="text" name="search" id="search-input-qc" class="form-control" placeholder="Cari nama item, serial number atau kode pembelian..." style="border-left: 0; box-shadow: none;" value="{{ $search_term ?? '' }}">
            </div>
        </div>

        {{-- Filter Kategori (Nanti diisi dari controller) --}}
        <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" name="kategori" id="filter-kategori">
            <option value="">Semua Kategori</option>
            @foreach ($semua_kategori as $kat)
                <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
            @endforeach
        </select>

        {{-- Filter Urutkan --}}
        <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" name="sort" id="filter-sort-qc">
            <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
        </select>
    </div>
</form>


<div id="qc-list-container">
    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-product table-md">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Kode Beli</th>
                            <th>Nama Item</th>
                            <th>SN/SNL</th>
                            <th>Kategori</th>
                            <th style="width: 15%">Persentase</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="qc-table-body">
                        {{-- Use partial to render rows so controller and AJAX can reuse the same HTML --}}
                        @include('admin.partials.qc_table_rows', ['data_qc' => $data_qc])
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="qc-pagination-links-container">
        @if ($data_qc->hasPages())
            <div class="card-footer bg-white">
                {{ $data_qc->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- Tidak ada modal hapus di halaman QC; arsip/hapus dikelola di halaman proses --}}
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

    /* Letak isi sel di atas (top) seperti tabel normal */
    .table-product td {
        vertical-align: top !important;
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

    /* CSS UNTUK EMPTY STATE */
    .card.shadow-sm {
        min-height: 700px;
        display: flex;
        flex-direction: column;
    }
    .card.shadow-sm .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
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
</style>
@endpush
