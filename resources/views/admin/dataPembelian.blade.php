@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('page-actions')
    <a href="{{route('admin.purchases.create')}}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Pembelian</span>
    </a>
@endpush

@section('content')

<form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm">
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        {{-- Search Bar --}}
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text" style="background: #fff; border-right: 0;">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                {{-- Tambahkan ID untuk JS --}}
                <input type="text" name="search" id="search-input" class="form-control" placeholder="Cari Kode Pembelian atau Nama Customer..." style="border-left: 0; box-shadow: none;" value="{{ $search_term ?? '' }}">
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
    </div>
</form>


<div id="purchase-list-container">
    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-product table-md">
                    <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kode</th>
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
                    {{-- @include('admin.partials.purchase_table_content', $data_pembelian) --}}
                    @forelse ($data_pembelian as $pembelian)
                        <tr>
                            <td class="text-center" style="width: 60px;">{{ $loop->iteration }}</td>
                            <td>{{ $pembelian->kode_transaksi }}</td>
                            <td>{{ $pembelian->customer->nama ?? '-' }}</td>
                            <td>{{ $pembelian->created_at->format('d M Y, H:i') }}</td>
                            <td>{{ $pembelian->perusahaan_cabang->nama ?? '-' }}</td>
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
                                    <a href="{{ route('admin.purchases.show', $pembelian->id) }}" title="Lihat Detail Transaksi">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete" data-id="{{ $pembelian->id }}" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="tr-empty">
                            <td colspan="9" class="p-0">
                                <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 700px; width: 100%;">
                                    <i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Pembelian</h5>
                                    {{-- <p class="text-muted mb-0">Silakan <a href="{{ route('admin.purchases.create') }}">tambah transaksi pembelian</a> baru.</p> --}}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
            </div>
        </div>
    </div>

    <div id="pagination-links-container">
        @if ($data_pembelian->hasPages())
            <div class="card-footer bg-white">
                {{ $data_pembelian->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p>Yakin ingin menghapus data pembelian ini? Aksi ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
      </div>
    </div>
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

        /* Tambahkan style untuk table-sm agar lebih kecil lagi */
        .table-sm th, .table-sm td {
            padding: 0.75rem 0.75rem; /* Kurangi padding */
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
            font-size: 0.95rem;
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
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .table-responsive {
            flex-grow: 1;
            position: relative;
        }

        .table-product {
            width: 100%;
            margin-bottom: 0 !important;
        }

        .table-product tbody {
            position: relative;
            min-height: 700px;
        }

        .table-product tr.tr-empty {
            position: relative;
            background-color: #fff;
        }

        .table-product tr.tr-empty td {
            height: auto;
            padding: 0 !important;
        }

        /* .table-product tr.tr-empty td .empty-content {
            height: 100%; /
        } */

        .table-product tr.tr-empty td .empty-message {
            height: 100%;
            text-align: center;
        }

        .tr-empty td {
            border: none
        }

        .empty-message {
            min-height: 700px;
            width: 100%;
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
        const container = document.getElementById('purchase-list-container');
        const tableBody = document.getElementById('purchase-table-body');
        const paginationContainer = document.getElementById('pagination-links-container');
        const form = document.getElementById('filterForm');
        const searchInput = form.querySelector('input[name="search"]');
        const statusFilter = form.querySelector('select[name="status"]');
        const sortFilter = form.querySelector('select[name="sort"]');
        const urlIndex = '{{ route('admin.purchases.index') }}';
        let isFetching = false;
        let searchTimeout;
        let formToDelete = null;
        const confirmModalEl = document.getElementById('confirmDeleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        // Pastikan bootstrap modal tersedia
        const bsModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;

        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
            e.preventDefault();
            formToDelete = this;
            if (bsModal) bsModal.show();
            });
        });

        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function () {
            if (formToDelete) {
                // optional: disable button agar tidak double submit
                confirmDeleteBtn.disabled = true;
                formToDelete.submit();
            }
            });
        }

        function fetchPurchases(url) {
            if (isFetching) return;
            isFetching = true;

            // Tambahkan kelas loading visual ke container
            container.style.opacity = '0.5';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Tanda AJAX
                    'Accept': 'application/json', // Minta JSON, karena controller mengembalikan JSON
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok. Status: ' + response.status);
                }
                return response.json(); // Ambil respons sebagai JSON
            })
            .then data => {
                // Hapus konten <tbody> dan pagination lama
                tableBody.innerHTML = data.table_html;

                // Pastikan paginationContainer diperbarui dengan benar
                paginationContainer.innerHTML = data.pagination_html ?
                    `<div class="card-footer bg-white">${data.pagination_html}</div>` : '';

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
            paginationContainer.querySelectorAll('.pagination a').forEach(link => {
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
