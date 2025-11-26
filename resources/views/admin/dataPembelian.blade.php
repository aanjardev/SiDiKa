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
                            <th>Kode Transaksi</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th style="width: 20%;">Item Dibeli</th>
                            <th class="text-center">Status</th>
                            <th>Harga Deal</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    {{-- ID Body dari Main untuk AJAX Replacement --}}
                    <tbody id="purchase-table-body">
                        @forelse ($data_pembelian as $index => $pembelian)
                        <tr class="purchase-row" data-detail-url="{{ route('admin.purchases.show', $pembelian->id) }}">
                            <td class="text-center text-muted fw-bold">{{ ($data_pembelian->firstItem() ?? 0) + $index }}</td>

                            {{-- Kode Transaksi --}}
                            <td>
                                <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                    {{ $pembelian->kode_transaksi ?? '#' . $pembelian->id }}
                                </span>
                            </td>

                            {{-- Customer --}}
                            <td>
                                <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                    {{ $pembelian->customer->nama ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Tanggal --}}
                            <td class="text-muted small">
                                <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
                                <br>
                                <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
                            </td>

                            {{-- Cabang --}}
                            <td class="text-dark fw-medium">
                                {{ $pembelian->perusahaan_cabang->nama ?? '-' }}
                            </td>

                            {{-- Item Dibeli --}}
                            <td>
                                <span class="text-secondary small d-block"
                                      title="{{ $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ') }}"
                                      style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    @php
                                        $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                                        echo $itemNames ?: '-';
                                    @endphp
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                @if($pembelian->status_pembelian == 'deal')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Deal</span>
                                @elseif($pembelian->status_pembelian == 'tidak_deal')
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">Batal</span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Draft</span>
                                @endif
                            </td>

                            {{-- Harga Deal --}}
                            <td class="fw-bold text-dark">
                                Rp{{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center" style="width:120px">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}"
                                        class="btn-action btn-action-edit no-row-navigation"
                                        title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-action btn-action-delete no-row-navigation"
                                                title="Hapus"
                                                onclick="handleDeletePembelian(this)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="tr-empty">
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-muted"></i>
                                    <h6 class="text-muted">Belum ada data pembelian</h6>
                                    <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.purchases.create') }}">transaksi pembelian</a> baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
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
{{-- Script Hapus Sederhana (Visual HEAD) --}}
<script>
    function handleDeletePembelian(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus data pembelian ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus data pembelian ini?')) {
                button.form.submit();
            }
        }
    }

    // Fungsi helper untuk delete di partial dan controller
    function handleDeletePembelian(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus data pembelian ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus data pembelian ini?')) {
                button.form.submit();
            }
        }
    }
    
    // Alias untuk kompatibilitas dengan partial
    function confirmDeletePurchase(button) {
        handleDeletePembelian(button);
    }
    
    // Export ke window
    window.handleDeletePembelian = handleDeletePembelian;
    window.confirmDeletePurchase = confirmDeletePurchase;
</script>

{{-- Script AJAX Search (Logic Main) --}}
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

        function fetchPurchases(url) {
            if (isFetching) return;
            isFetching = true;

            // Efek Loading Visual
            container.style.opacity = '0.6';
            container.style.transition = 'opacity 0.3s';

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update Tabel & Pagination
                tableBody.innerHTML = data.table_html || '';
                paginationContainer.innerHTML = data.pagination_html ?
                    `<div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">${data.pagination_html}</div>` : '';

                // Update URL Browser
                window.history.pushState(null, null, url);

                // Re-attach event listeners untuk pagination baru
                attachPaginationListeners();
            })
            .catch(error => {
                console.error('Fetch error:', error);
            })
            .finally(() => {
                isFetching = false;
                container.style.opacity = '1';
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

        // Event Listeners
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchPurchases(buildUrl());
            }, 500);
        });

        statusFilter.addEventListener('change', function() {
            fetchPurchases(buildUrl());
        });

        sortFilter.addEventListener('change', function() {
            fetchPurchases(buildUrl());
        });

        function attachPaginationListeners() {
            paginationContainer.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchPurchases(this.href);
                });
            });
        }

        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.no-row-navigation')) return;
            const row = e.target.closest('.purchase-row');
            if (!row) return;
            const url = row.dataset.detailUrl;
            if (url) {
                window.location.href = url;
            }
        });

        // Init Pagination Listener
        attachPaginationListeners();

        // Prevent Form Submit Refresh
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchPurchases(buildUrl());
        });
    });
</script>
@endpush
