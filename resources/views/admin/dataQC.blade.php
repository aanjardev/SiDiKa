@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
    {{-- Tombol Arsip (Fitur dari Main) --}}
    <a href="{{ url('admin/quality-control/archived') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-archive fa-fw"></i>
        <span>Arsip Produk</span>
    </a>
@endpush

@push('styles')
<style>
    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        opacity: 0.8;
        text-decoration: underline !important;
    }
</style>
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
    // Pastikan route ini tersedia di view
    const urlIndex = '{{ route('admin.quality-control.index') }}';

    let isFetching = false;
    let searchTimeout;

    // Fetch function
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
                    tableBody.innerHTML = data;
                }
                if (data.pagination_html !== undefined && paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html;
                }
            } else {
                const text = await res.text();
                tableBody.innerHTML = text;
            }

            attachPaginationHandler();

        } catch (err) {
            console.error('Gagal memuat data:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5 text-muted">
                            <i class="fa-solid fa-triangle-exclamation fa-2x mb-3"></i>
                            <h5 class="mb-1">Gagal memuat data</h5>
                            <p class="mb-0">Silakan coba lagi.</p>
                        </div>
                    </td>
                </tr>`;
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

    // Event Listeners
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

    attachPaginationHandler();
});
</script>
@endpush

@section('content')

{{-- Filter dan Pencarian (Visual: HEAD, Logic: Main) --}}
<form action="{{ route('admin.quality-control.index') }}" method="GET" id="filterFormQc">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       id="search-input-qc"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari nama item, serial number atau kode pembelian..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
            <div class="d-flex align-items-center gap-2 pe-2">

                {{-- Filter Kategori --}}
                <select name="kategori" id="filter-kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="" selected>Semua Kategori</option>
                    @foreach ($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ ($kategori_filter ?? '') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Sort --}}
                <select name="sort" id="filter-sort-qc"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    {{-- Opsi tambahan dari HEAD jika logic backend mendukung --}}
                    {{-- <option>Progress Tertinggi</option> --}}
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div id="qc-list-container">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 100px;">ID Beli</th>
                            <th style="width: 25%;">Nama Item</th>
                            <th>Serial Number</th>
                            <th>SN Lensa</th>
                            <th>Kategori</th>
                            <th style="width: 20%">Kelengkapan QC</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    {{-- ID qc-table-body PENTING untuk script AJAX --}}
                    <tbody id="qc-table-body">
                        @forelse ($data_qc as $index => $item)
                        <tr class="clickable-row" data-detail-url="{{ route('admin.quality-control.edit', $item->id) }}">
                            <td class="text-center text-muted fw-bold">{{ ($data_qc->firstItem() ?? 0) + $index }}</td>

                            {{-- ID Pembelian --}}
                            <td>
                                <a href="{{ route('admin.purchases.show', $item->pembelian->id) }}" 
                                   class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small text-decoration-none clickable-code"
                                   onclick="event.stopPropagation();">
                                    #{{ $item->pembelian->kode_transaksi }}
                                </a>
                            </td>

                            {{-- Nama Item --}}
                            <td>
                                <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                    {{ $item->nama_item }}
                                </span>
                            </td>

                            {{-- Serial Number --}}
                            <td class="text-secondary font-monospace small">
                                {{ $item->serial_number ?? '-' }}
                            </td>

                            {{-- SN Lensa --}}
                            <td class="text-secondary font-monospace small">
                                {{ $item->serial_lens ?? '-' }}
                            </td>

                            {{-- Kategori --}}
                            <td class="text-dark">
                                {{ $item->kategori->nama_kategori ?? '-' }}
                            </td>

                            {{-- Progress QC --}}
                            <td>
                                @php $persen = round($item->persentase_lengkap); @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1 shadow-sm" style="height: 6px; border-radius: 10px; background-color: #f0f2f5;">
                                        <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}"
                                             role="progressbar"
                                             style="width: {{ $persen }}%; border-radius: 10px;"
                                             aria-valuenow="{{ $persen }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="small fw-bold text-muted" style="font-size: 0.8rem; min-width: 35px; text-align: right;">
                                        {{ $persen }}%
                                    </span>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center no-row-navigation">
                                <a href="{{ route('admin.quality-control.edit', $item->id) }}"
                                   class="btn btn-sm btn-primary shadow-sm px-3 rounded-3 fw-medium"
                                   style="font-size: 0.85rem;"
                                   title="Proses QC">
                                    <i class="fa-solid fa-clipboard-check me-1"></i> Proses
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="fa-solid fa-clipboard-list fa-3x mb-3 text-muted"></i>
                                    <h6 class="text-muted">Tidak Ada Item Menunggu QC</h6>
                                    <p class="small text-muted">Semua item dari transaksi 'Deal' akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Container ID Penting --}}
        <div id="qc-pagination-links-container">
            @if ($data_qc->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $data_qc->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
