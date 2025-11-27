@extends('layouts.admin')

@section('title', 'Foto Produk')

@section('content')

{{-- FILTER & SEARCH (diseragamkan dengan dataPembelian) --}}
<form action="{{ route('admin.products.photos') }}" method="GET" id="filterFormPhoto">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Search SKU / Nama Produk --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       id="search-input-photo"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari SKU atau Nama Produk..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;"
                       autofocus>
            </div>

            {{-- Filter Kategori --}}
            <div class="d-flex align-items-center gap-2 pe-2 mt-2 mt-md-0">
                <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        name="kategori" id="filter-kategori" style="cursor: pointer;">
                    <option value="">Semua Kategori</option>
                    @foreach($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ (isset($selected_kategori) && $selected_kategori == $kat->id) ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>

{{-- LIST TABEL FOTO PRODUK (dibungkus card seperti dataPembelian) --}}
<div id="photo-list-container">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                <table class="table table-modern mb-0 table-product table-md">
                <thead>
                    <tr>
                        <th style="width:60px" class="text-center">No</th>
                        <th>Kode SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Stok</th>
                        <th>Harga Jual</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="photo-products-tbody">
                    @include('admin.partials.photo_product_rows', ['products' => $products])
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div id="pagination-links-container">
        @if ($products->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
    <style>
        /* Seragam dengan halaman data lain, biarkan .table-modern dari CSS global yang bekerja */

        .table-product tbody tr.tr-empty td .empty-message {
            min-height: 250px;
            width: 100%;
        }

        .table-product tbody tr:not(.tr-empty) td {
            vertical-align: top !important;
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterFormPhoto') || document.querySelector('form[action*="products/photos"]');
    const tbody = document.getElementById('photo-products-tbody');
    const paginationContainer = document.getElementById('pagination-links-container');

    function buildUrl(params) {
        const base = '{{ route('admin.products.photos') }}';
        const query = new URLSearchParams(params).toString();
        return base + (query ? ('?' + query) : '');
    }

    let timer = null;
    function fetchAndRender() {
        const formData = new FormData(form);
        const params = {};
        for (const [k,v] of formData.entries()) {
            if (v !== '') params[k] = v;
        }
        const url = buildUrl(params);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok. Status: ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data.table_html !== undefined) {
                    tbody.innerHTML = data.table_html;
                } else {
                    tbody.innerHTML = '';
                }
                paginationContainer.innerHTML = data.pagination_html ?
                    `<div class="card-footer bg-white">${data.pagination_html}</div>` : '';
                attachPaginationLinks();
            }).catch(err => {
                console.error('Failed to fetch photo products', err);
                // Render empty message dengan style yang sama
                tbody.innerHTML = `
                    <tr class="tr-empty">
                        <td colspan="7" class="p-0">
                            <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">
                                <i class="fa-solid fa-exclamation-triangle fa-2x text-muted mb-3"></i>
                                <h5 class="mb-1">Gagal memuat data</h5>
                                <p class="text-muted mb-0">Silakan coba lagi atau cek log server.</p>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }

    function debounceFetch() {
        if (timer) clearTimeout(timer);
        timer = setTimeout(fetchAndRender, 350);
    }

    form.addEventListener('input', debounceFetch);
    form.addEventListener('change', debounceFetch);

    function attachPaginationLinks() {
        const links = paginationContainer.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (!href) return;
                fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => {
                        if (!r.ok) throw new Error('Network response was not ok. Status: ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        tbody.innerHTML = data.table_html || '';
                        paginationContainer.innerHTML = data.pagination_html ?
                            `<div class="card-footer bg-white">${data.pagination_html}</div>` : '';
                        attachPaginationLinks();
                    }).catch(err => {
                        console.error(err);
                        // Render empty message dengan style yang sama
                        tbody.innerHTML = `
                            <tr class="tr-empty">
                                <td colspan="7" class="p-0">
                                    <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">
                                        <i class="fa-solid fa-exclamation-triangle fa-2x text-muted mb-3"></i>
                                        <h5 class="mb-1">Gagal memuat data</h5>
                                        <p class="text-muted mb-0">Silakan coba lagi atau cek log server.</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
            });
        });
    }

    // initial attach
    attachPaginationLinks();
});
</script>
@endpush

