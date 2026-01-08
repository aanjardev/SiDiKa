@extends('layouts.admin')

@section('title', 'Arsip Produk QC')

@push('page-actions')
    <a href="{{ route('admin.quality-control.index') }}" class="btn btn-secondary border btn-sm d-flex align-items-center gap-2 fw-medium">
        <i class="fas fa-arrow-left fa-fw"></i>
        <span>Kembali ke QC</span>
    </a>
@endpush

@section('content')

{{-- Search & Filter --}}
<form method="GET" action="{{ route('admin.quality-control.archived') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari berdasarkan nama item atau serial number"
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;"
                       autofocus>
            </div>

            {{-- Bagian Kanan: Dropdown Filter --}}
            <div class="d-flex align-items-center gap-2 pe-2">

                {{-- Dropdown Kategori --}}
                <select name="kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_kategori ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($semua_kategori ?? [] as $kat)
                        <option value="{{ $kat->id }}" {{ ($selected_kategori ?? 'all') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Dropdown Sort --}}
                <select name="sort_by"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama_item" {{ ($sort_by ?? 'updated_at') == 'nama_item' ? 'selected' : '' }}>Nama Item (A-Z)</option>
                    <option value="nama_item_desc" {{ ($sort_by ?? 'updated_at') == 'nama_item_desc' ? 'selected' : '' }}>Nama Item (Z-A)</option>
                    <option value="pembelian_id" {{ ($sort_by ?? 'updated_at') == 'pembelian_id' ? 'selected' : '' }}>ID Pembelian</option>
                </select>

                {{-- Hidden Input --}}
                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 120px;">Kode Beli</th>
                        <th style="width: 25%;">Nama Item</th>
                        <th>Serial Number</th>
                        <th>Kategori</th>
                        <th class="text-center" style="width: 15%">Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_qc as $index => $item)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($data_qc->firstItem() ?? 0) + $index }}</td>

                        {{-- Kode Transaksi Pembelian --}}
                        <td>
                            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                {{ $item->pembelian->kode_transaksi ?? ('#' . $item->pembelian_id) }}
                            </span>
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

                        {{-- Kategori --}}
                        <td class="text-dark">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>

                        {{-- Status (Diarsipkan) --}}
                        <td class="text-center">
                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle">
                                Diarsipkan
                            </span>
                        </td>

                        {{-- Aksi (Restore) --}}
                        <td class="text-center">
                            <form action="{{ route('admin.quality-control.restore', $item->id) }}" method="POST" class="d-inline restore-form">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm btn-success text-white shadow-sm px-3 rounded-3"
                                        style="font-size: 0.9rem;"
                                        title="Kembalikan ke Antrian QC">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-box-archive fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">
                                    @if($search_term ?? false)
                                        Tidak ada hasil untuk "{{ $search_term }}"
                                    @else
                                        Belum Ada Item Diarsipkan
                                    @endif
                                </h6>
                                <p class="small text-muted">
                                    @if($search_term ?? false)
                                        Coba gunakan kata kunci lain
                                    @else
                                        Item yang ditandai "Tidak Layak Jual" akan muncul di sini.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_qc->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_qc->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500); // Submit setelah 500ms idle
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                searchForm.submit();
            }
        });
    }

    let formToRestore = null;

    const modalHtml = `
    <div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 10px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-rotate-left me-2 text-success"></i>Konfirmasi Restore
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-secondary">
                    <p class="mb-0">Yakin ingin mengembalikan item ini dari arsip? item akan kembali ke daftar antrian.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light border px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmRestoreBtn" class="btn btn-success px-4 fw-medium">
                        Ya, Restore
                    </button>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const confirmModalEl = document.getElementById('confirmRestoreModal');
    const confirmRestoreBtn = document.getElementById('confirmRestoreBtn');
    const bsModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;

    function handleRestoreSubmit(e) {
        e.preventDefault();
        formToRestore = this;
        if (bsModal) bsModal.show();
    }

    document.querySelectorAll('.restore-form').forEach(f => {
        f.addEventListener('submit', handleRestoreSubmit);
    });

    if (confirmRestoreBtn) {
        confirmRestoreBtn.addEventListener('click', function() {
            if (!formToRestore) return;

            // Disable tombol agar tidak double submit
            confirmRestoreBtn.disabled = true;
            confirmRestoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

            formToRestore.submit();
        });
    }
});
</script>
@endpush
