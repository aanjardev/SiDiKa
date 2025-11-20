@extends('layouts.admin')

@section('title', 'Foto Produk')

@section('content')

<form action="{{ route('admin.products.photos') }}" method="GET" id="filterFormPhoto">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text" style="background: #fff; border-right: 0;">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text" name="search" id="search-input-photo" class="form-control" placeholder="Cari SKU atau Nama Produk..." style="border-left: 0; box-shadow: none;" value="{{ $search_term ?? '' }}">
            </div>
        </div>

        <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" name="kategori" id="filter-kategori">
            <option value="">Semua Kategori</option>
            @foreach($semua_kategori as $kat)
                <option value="{{ $kat->id }}" {{ (isset($selected_kategori) && $selected_kategori == $kat->id) ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
            @endforeach
        </select>
    </div>
</form>

<div id="photo-list-container">
    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-product table-md">
                <thead>
                    <tr>
                        <th style="width:60px" class="text-center">No</th>
                        <th>Kode SKU</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Stok</th>
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
            <div class="card-footer bg-white">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
    <style>
        .table {
            border-radius: 5px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-sm th, .table-sm td {
            padding: 0.75rem 0.75rem;
            font-size: 0.85rem;
        }

        .table-product tbody tr:nth-child(even) {
            background-color: #F8F9FC;
        }

        .table-product tbody tr:hover {
            background-color: #EFF3F9;
            transition: 0.2s;
        }
        .table-md > :not(caption) > * > * {
            padding: 0.75rem 0.75rem !important;
            font-size: 0.95rem;
        }

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
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
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
            color: #bb2d3b;
        }

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

        .table-product tbody tr:not(.tr-empty) td {
            vertical-align: top !important;
            padding: 0.75rem 0.75rem !important;
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

