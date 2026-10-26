@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('styles')
@vite('resources/css/admin/pages/transaction-tables.css')
<style>
    .purchase-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .purchase-row:hover {
        background-color: #f8f9fa;
    }

    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        opacity: 0.8;
        text-decoration: underline !important;
    }
    .purchase-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .purchase-filter-body {
            gap: 0.5rem;
        }
        .purchase-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .purchase-filter-card .purchase-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .purchase-filter-icon {
            margin-left: 0 !important;
        }
        .purchase-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .purchase-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .purchase-filter-card .purchase-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .purchase-filter-body {
            padding: 0.75rem !important;
        }
        .purchase-filter-input .form-control {
            font-size: 0.85rem;
        }
        .purchase-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .purchase-filter-controls .form-select {
            width: 100%;
        }
        .purchase-table-card {
            border-radius: 12px;
        }
    }
</style>
@endpush

@push('page-actions')
<a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Pembelian</span>
</a>
<button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#exportPurchasesModal">
    <i class="fa-solid fa-file-export"></i>
    <span>Export</span>
</button>
@endpush

@section('content')

{{-- Filter dan Pencarian (Visual: HEAD, Logic: Main) --}}
<form action="{{ route('admin.purchases.index') }}" method="GET" id="filterForm">
    <div class="card shadow-sm border-0 mb-4 purchase-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap purchase-filter-body">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 purchase-filter-input">
                <span class="text-muted ms-2 me-3 purchase-filter-icon">
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
           <div class="d-flex align-items-center gap-2 pe-2 purchase-filter-controls">

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
    <div class="card shadow-sm border-0 purchase-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0">
            <div class="table-responsive transaction-table-scroll" tabindex="0" role="region" aria-label="Tabel pembelian; geser horizontal untuk melihat kolom lainnya">
                <table class="table table-modern mb-0 transaction-table transaction-table--purchases">
                    <colgroup>
                        <col class="transaction-col-number">
                        <col class="transaction-col-code">
                        <col class="transaction-col-customer">
                        <col class="transaction-col-date">
                        <col class="transaction-col-branch">
                        <col class="transaction-col-items">
                        <col class="transaction-col-status">
                        <col class="transaction-col-amount">
                        <col class="transaction-col-actions">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Cabang</th>
                            <th>Item Dibeli</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Harga Deal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    {{-- ID Body dari Main untuk AJAX Replacement --}}
                    <tbody id="purchase-table-body">
                        @include('admin.partials.purchase_table_content', ['data_pembelian' => $data_pembelian])
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

{{-- Export Modal --}}
<div class="modal fade" id="exportPurchasesModal" tabindex="-1" aria-labelledby="exportPurchasesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="exportPurchasesForm" method="GET" action="{{ route('admin.purchases.export.pdf') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportPurchasesModalLabel">Export Pembelian Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Periode</label>
                        <div class="d-flex gap-2">
                            @php
                                $currentYear = now()->year;
                                $currentMonth = (int) now()->format('m');
                            @endphp
                            <select id="export-purchases-year" class="form-select" required>
                                @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <select id="export-purchases-month" class="form-select" required>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $month === $currentMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <input type="hidden" name="month" id="export-purchases-period" value="{{ now()->format('Y-m') }}">
                    </div>
                    <div class="mb-3">
                        <label for="export-purchases-cabang" class="form-label">Cabang</label>
                        <select id="export-purchases-cabang" name="cabang" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach ($semua_cabang ?? [] as $cab)
                                <option value="{{ $cab->id }}">{{ $cab->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="status" id="export-purchases-status" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-danger" data-export-type="pdf">
                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                    </button>
                    <button type="submit" class="btn btn-outline-success" data-export-type="excel">
                        <i class="fa-solid fa-file-excel me-1"></i> Excel
                    </button>
                </div>
            </form>
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
        const urlIndex = "{{ route('admin.purchases.index') }}";

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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const exportForm = document.getElementById('exportPurchasesForm');
        const cabangInput = document.getElementById('export-purchases-cabang');
        const statusInput = document.getElementById('export-purchases-status');
        const cabangSelect = document.getElementById('filter-cabang');
        const statusSelect = document.getElementById('filter-status');
        const yearSelect = document.getElementById('export-purchases-year');
        const monthSelect = document.getElementById('export-purchases-month');
        const periodInput = document.getElementById('export-purchases-period');

        if (cabangInput && cabangSelect) {
            cabangInput.value = cabangSelect.value || '';
            cabangSelect.addEventListener('change', function() {
                cabangInput.value = this.value || '';
            });
        }

        if (statusInput && statusSelect) {
            statusInput.value = statusSelect.value || 'semua';
            statusSelect.addEventListener('change', function() {
                statusInput.value = this.value || 'semua';
            });
        }

        function syncPeriod() {
            if (!periodInput || !yearSelect || !monthSelect) {
                return;
            }
            periodInput.value = `${yearSelect.value}-${monthSelect.value}`;
        }

        if (yearSelect && monthSelect) {
            syncPeriod();
            yearSelect.addEventListener('change', syncPeriod);
            monthSelect.addEventListener('change', syncPeriod);
        }

        exportForm.addEventListener('submit', function(event) {
            const target = event.submitter;
            if (!target) {
                return;
            }
            if (target.dataset.exportType === 'excel') {
                exportForm.action = "{{ route('admin.purchases.export.excel') }}";
            } else {
                exportForm.action = "{{ route('admin.purchases.export.pdf') }}";
            }
            const modalElement = document.getElementById('exportPurchasesModal');
            if (modalElement && window.bootstrap) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement) || new window.bootstrap.Modal(modalElement);
                modalInstance.hide();
            }
        });
    });
</script>
@endpush
