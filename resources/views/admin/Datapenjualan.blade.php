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
                    style="font-size: 0.95rem;">
            </div>

            <div class="d-flex align-items-center gap-2 pe-2">
                <select name="kategori"
                    id="filter-kategori"
                    class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                    style="cursor: pointer;">
                    <option value="">Semua Kategori</option>
                    @foreach ($semua_kategori as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
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
                            <th style="width: 15%;">ID Transaksi</th>
                            <th style="width: 20%;">Customer</th>
                            <th>Tanggal</th>
                            <th style="width: 20%;">Item Terjual</th>
                            <th>Cabang</th>
                            <th>Total</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
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
    // Fungsi untuk delete penjualan (bisa dipanggil dari partial)
    function handleDeletePenjualan(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus data penjualan ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus data penjualan ini?')) {
                button.form.submit();
            }
        }
    }
    
    // Export ke window untuk bisa dipanggil dari partial
    window.handleDeletePenjualan = handleDeletePenjualan;

    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('filterForm');
        const searchInput = document.getElementById('search-input');
        const kategori = document.getElementById('filter-kategori');
        const sort = document.getElementById('filter-sort');
        const tableBody = document.getElementById('sales-table-body');
        const paginationContainer = document.getElementById('pagination-links-container');
        const baseUrl = "{{ route('admin.sales.index') }}";

        let typingTimeout = null;
        let isFetching = false;

        function formToParams() {
            const data = new FormData(form);
            return new URLSearchParams(data).toString();
        }

        function buildUrl() {
            return `${baseUrl}?${formToParams()}`;
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

                    const paginationHtml = data.pagination_html
                        ? `<div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">${data.pagination_html}</div>`
                        : '';
                    paginationContainer.innerHTML = paginationHtml;

                    attachPaginationEvents();
                })
                .finally(() => {
                    isFetching = false;
                });
        }

        function attachPaginationEvents() {
            paginationContainer.querySelectorAll("a").forEach(a => {
                a.addEventListener("click", function(e) {
                    e.preventDefault();
                    fetchSales(this.href);
                });
            });
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchSales(buildUrl());
        });

        searchInput.addEventListener('keyup', () => {
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => fetchSales(buildUrl()), 400);
        });

        kategori.addEventListener('change', () => fetchSales(buildUrl()));
        sort.addEventListener('change', () => fetchSales(buildUrl()));

        attachPaginationEvents();
    });
</script>
@endpush
