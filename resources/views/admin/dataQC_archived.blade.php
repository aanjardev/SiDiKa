@extends('layouts.admin')

@section('title', 'Arsip Produk QC')

@push('page-actions')
    <a href="{{ route('admin.quality-control.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left fa-fw"></i>
        <span>Kembali ke QC</span>
    </a>
@endpush

@section('content')

<div class="card shadow-sm">
    <div class="card-body p-0 table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-product table-md">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kode Beli</th>
                        <th>Nama Item</th>
                        <th>SN/SNL</th>
                        <th>Kategori</th>
                        <th style="width: 15%">Persentase</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.partials.qc_table_rows', [
                        'data_qc' => $data_qc,
                        'empty_heading' => 'Tidak Ada Item Diarsipkan',
                        'empty_text' => 'Belum ada item yang diarsipkan (tidak layak jual).',
                        'show_restore' => true,
                    ])
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_qc->hasPages())
        <div class="card-footer bg-white">
            {{ $data_qc->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
        let formToRestore = null;
        const modalHtml = `
        <div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Restore</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin mengembalikan item ini dari arsip? Item akan ditandai sebagai "Lolos QC".</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="confirmRestoreBtn" class="btn btn-success">Restore</button>
                    </div>
                </div>
            </div>
        </div>`;

        // append modal to body
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
                        confirmRestoreBtn.disabled = true;
                        formToRestore.submit();
                });
        }
});
</script>
@endpush
