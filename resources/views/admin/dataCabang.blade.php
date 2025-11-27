@extends('layouts.admin')

@section('title', 'Data Cabang')

@push('page-actions')
<a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Cabang</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<form method="GET" action="{{ route('admin.branches.index') }}" id="searchForm">
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
                       placeholder="Cari cabang berdasarkan nama atau alamat..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown Sort --}}
            <div class="d-flex align-items-center gap-2 pe-2">
                <select name="sort_by" 
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" 
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ ($sort_by ?? 'updated_at') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                </select>
                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Cabang</th>
                        <th style="width: 35%;">Alamat</th>
                        <th>Nomor Telepon</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_cabang as $index => $cabang)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.branches.show', $cabang->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($data_cabang->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $cabang->nama }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if ($cabang->link_maps)
                                <a href="{{ $cabang->link_maps }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-decoration-none text-muted small d-flex align-items-start gap-2"
                                   title="Lihat di Google Maps">
                                    <i class="fa-solid fa-location-dot text-danger mt-1"></i>
                                    <span style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $cabang->alamat }}
                                    </span>
                                </a>
                            @else
                                <div class="text-muted small d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-location-dot text-secondary mt-1"></i>
                                    <span style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $cabang->alamat }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td class="fw-medium text-dark text-nowrap">
                            {{ $cabang->nomor_telepon }}
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.branches.edit', $cabang->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.branches.destroy', $cabang->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-action btn-action-delete"
                                        title="Hapus"
                                        onclick="handleDeleteCabang(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-shop fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data cabang</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_cabang->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_cabang->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Gunakan nama yang berbeda untuk menghindari konflik dengan window.confirmDelete
    function handleDeleteCabang(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus data cabang ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            // Fallback jika alert.js belum load
            if (confirm('Apakah Anda yakin ingin menghapus data cabang ini?')) {
                button.form.submit();
            }
        }
    }
    
    // Export ke window untuk bisa dipanggil dari mana saja
    window.handleDeleteCabang = handleDeleteCabang;

    // Auto Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;

        if (searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    searchForm.submit();
                }
            });
        }
    });
</script>
@endpush
