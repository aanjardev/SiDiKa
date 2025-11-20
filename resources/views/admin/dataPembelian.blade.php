@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('page-actions')
    <a href="{{route('admin.purchases.create')}}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Pembelian</span>
    </a>
@endpush

@section('content')

{{-- FORM FILTER DENGAN METHOD GET (Form utama, hanya satu) --}}
<form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        {{-- Search Bar --}}
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text" style="background: #fff; border-right: 0;">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                {{-- Tambahkan ID untuk JS --}}
                <input type="text" name="search" id="search-input" class="form-control" placeholder="Cari Kode Pembelian (ID) atau Nama Customer..." style="border-left: 0; box-shadow: none;" value="{{ $search_term ?? '' }}">
            </div>
        </div>
        {{-- Filter Status --}}
        <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" name="status" id="filter-status">
            <option value="semua" {{ ($status_filter ?? 'semua') == 'semua' ? 'selected' : '' }}>Semua Status</option>
            <option value="draft" {{ ($status_filter ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="deal" {{ ($status_filter ?? '') == 'deal' ? 'selected' : '' }}>Deal</option>
            <option value="tidak_deal" {{ ($status_filter ?? '') == 'tidak_deal' ? 'selected' : '' }}>Tidak Deal</option>
        </select>
        {{-- Filter Urutkan --}}
        <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" name="sort" id="filter-sort">
            <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
            <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
        </select>
        {{-- Hilangkan tombol submit tersembunyi, JS akan menangani submit --}}
    </div>
</form>


{{-- CONTAINER UTAMA YANG AKAN DI-UPDATE AJAX --}}
<div id="purchase-list-container">
    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                {{-- Tambahkan CLASS table-sm (bukan table-md) untuk font kecil --}}
                <table class="table align-middle mb-0 table-product table-sm">
                    <thead class="table-light">
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
                <tbody id="purchase-table-body">
                    {{-- Ganti dengan include partial view yang HANYA berisi rows <tr> --}}
                    @forelse ($data_pembelian as $pembelian)
                        <tr>
                            <td class="text-center">#{{ $pembelian->id }}</td>
                            <td>{{ $pembelian->customer->nama ?? 'N/A' }}</td>
                            <td>{{ $pembelian->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $pembelian->perusahaan_cabang->nama ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                                    echo \Illuminate\Support\Str::limit($itemNames, 40, '...');
                                @endphp
                            </td>
                            <td>
                                @if($pembelian->status_pembelian == 'deal')
                                    <span class="badge bg-success-subtle text-success-emphasis">Deal</span>
                                @elseif($pembelian->status_pembelian == 'tidak_deal')
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Tidak Deal</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($pembelian->harga_deal)
                                    Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- AKTIFKAN TOMBOL DETAIL (SHOW) --}}
                                    <a href="{{ route('admin.purchases.show', $pembelian->id) }}" title="Lihat Detail Transaksi">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    {{-- AKTIFKAN TOMBOL EDIT --}}
                                    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    {{-- AKTIFKAN TOMBOL DELETE --}}
                                    <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="tr-empty">
                            {{-- Gunakan Flexbox di <td> untuk perataan vertikal dan horizontal. --}}
                            <td colspan="8" class="text-center">
                                {{-- Tambahkan d-flex, align-items-center, dan justify-content-center di sini. Min-height akan diatur di CSS. --}}
                                <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-content">
                                    <i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Pembelian</h5>
                                    <p class="text-muted mb-0">Silakan <a href="{{ route('admin.purchases.create') }}">tambah transaksi pembelian</a> baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- KONTEN PAGINATION (Di luar card jika menggunakan partial view terpisah) --}}
    @if ($data_pembelian->hasPages())
        <div class="card-footer bg-white">
            {{ $data_pembelian->links('pagination::bootstrap-5') }}
        </div>
    @endif

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

        /* Tambahkan style untuk table-sm agar lebih kecil lagi */
        .table-sm th, .table-sm td {
            padding: 0.3rem 0.5rem; /* Kurangi padding */
            font-size: 0.85rem; /* Kecilkan ukuran font */
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
        }

        button.btn-icon,
        .table-product button.btn-icon,
        form .btn-icon {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            color: #dc3545 !important;
            /* merah */
            cursor: pointer !important;
            font-size: 16px !important;
            line-height: 1 !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Pastikan ikon mewarisi warna, dan svg FA menggunakan fill:currentColor */
        .btn-icon i,
        .btn-icon svg,
        .btn-icon .fa-solid {
            color: inherit !important;
            fill: currentColor !important;
            stroke: currentColor !important;
        }

        /* Hilangkan efek fokus/active yang mungkin ditambahkan global */
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

        .table-wrapper {
            min-height: 700px;
            display: flex; /* Tambahkan display flex di sini */
            flex-direction: column; /* Atur arah flex */
        }

        .table-responsive {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .table-product {
            flex-grow: 1;
        }

        .table-product tbody {
            height: 100%;
        }

        .table-product tr.tr-empty {
            height: 100%;
        }

        .table-product tr.tr-empty td {
            height: 100%;
            padding: 0 !important;
        }

        .table-product tr.tr-empty td .empty-content {
            height: 100%; /* Pastikan div mengisi penuh tinggi td */
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const container = document.getElementById('purchase-list-container');
        const form = document.getElementById('filterForm');
        const searchInput = form.querySelector('input[name="search"]');
        const statusFilter = form.querySelector('select[name="status"]');
        const sortFilter = form.querySelector('select[name="sort"]');
        const urlIndex = '{{ route('admin.purchases.index') }}';
        let isFetching = false;
        let searchTimeout;

        function fetchPurchases(url) {
            if (isFetching) return;
            isFetching = true;

            // Tambahkan kelas loading visual ke container
            container.style.opacity = '0.5';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Tanda AJAX
                    'Accept': 'text/html', // Minta HTML (Fragment)
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok. Status: ' + response.status);
                }
                return response.text();
            })
            .then(html => {
                // Ganti konten container dengan HTML respons
                // Ini mengasumsikan response AJAX berisi seluruh konten <tbody> dan pagination.
                container.innerHTML = `
                    <div class="card shadow-sm">
                        <div class="card-body p-0 table-wrapper">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 table-product table-sm">
                                    <thead class="table-light">
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
                                    ${html}
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                // Perbarui URL browser (tanpa reload)
                window.history.pushState(null, null, url);

            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Gagal memuat data: ' + error.message);
            })
            .finally(() => {
                isFetching = false;
                container.style.opacity = '1'; // Hapus loading visual
                attachPaginationListeners(); // Pasang kembali event listener untuk pagination
            });
        }

        function buildUrl() {
            const params = new URLSearchParams();
            const search = searchInput.value;
            const status = statusFilter.value;
            const sort = sortFilter.value;

            if (search) params.append('search', search);
            if (status && status !== 'semua') params.append('status', status);
            if (sort) params.append('sort', sort);

            return `${urlIndex}?${params.toString()}`;
        }

        // --- Event Listeners ---

        // 1. Search Input (dengan debounce/timeout)
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchPurchases(buildUrl());
            }, 500); // Tunggu 500ms setelah user berhenti mengetik
        });

        // 2. Filter Dropdown (Status & Sort)
        statusFilter.addEventListener('change', function() {
            fetchPurchases(buildUrl());
        });

        sortFilter.addEventListener('change', function() {
            fetchPurchases(buildUrl());
        });

        // 3. Pagination Links (Delegation)
        function attachPaginationListeners() {
            // Pasang event listener ke link pagination di dalam container
            container.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPurchases(this.href);
                });
            });
        }

        // Panggil pertama kali untuk memastikan pagination berfungsi saat load awal
        attachPaginationListeners();

        // Mencegah form submit default saat enter di search bar
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchPurchases(buildUrl());
        });
    });
</script>
@endpush
