@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
<a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Penjualan</span>
</a>
<button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#exportSalesModal">
    <i class="fa-solid fa-file-export"></i>
    <span>Export</span>
</button>
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

<div class="modal fade" id="exportSalesModal" tabindex="-1" aria-labelledby="exportSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="exportSalesForm" method="GET" action="{{ route('admin.sales.export.pdf') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportSalesModalLabel">Export Penjualan Bulanan</h5>
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
                            <select id="export-sales-year" class="form-select" required>
                                @for ($year = $currentYear; $year >= $currentYear - 5; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <select id="export-sales-month" class="form-select" required>
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $month === $currentMonth ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(null, $month, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <input type="hidden" name="month" id="export-sales-period" value="{{ now()->format('Y-m') }}">
                    </div>
                    <div class="mb-3">
                        <label for="export-sales-cabang" class="form-label">Cabang</label>
                        <select id="export-sales-cabang" name="cabang" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach ($semua_cabang as $cab)
                                <option value="{{ $cab->id }}">{{ $cab->nama }}</option>
                            @endforeach
                        </select>
                    </div>
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const exportForm = document.getElementById('exportSalesForm');
        const cabangInput = document.getElementById('export-sales-cabang');
        const cabangSelect = document.getElementById('filter-cabang');
        const yearSelect = document.getElementById('export-sales-year');
        const monthSelect = document.getElementById('export-sales-month');
        const periodInput = document.getElementById('export-sales-period');

        if (cabangInput && cabangSelect) {
            cabangInput.value = cabangSelect.value || '';
            cabangSelect.addEventListener('change', function() {
                cabangInput.value = this.value || '';
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
                exportForm.action = "{{ route('admin.sales.export.excel') }}";
            } else {
                exportForm.action = "{{ route('admin.sales.export.pdf') }}";
            }
            const modalElement = document.getElementById('exportSalesModal');
            if (modalElement && window.bootstrap) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalElement) || new window.bootstrap.Modal(modalElement);
                modalInstance.hide();
            }
        });
    });
</script>
@endpush
