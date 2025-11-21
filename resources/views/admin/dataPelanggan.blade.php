@extends('layouts.admin')

@section('title', 'Data Pelanggan')

@push('page-actions')
<a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Pelanggan</span>
</a>
@endpush

@section('content')

{{-- Search & Filter (Resolved: Visual HEAD + Logic Main) --}}
<form method="GET" action="{{ route('admin.customers.index') }}" id="searchForm">
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
                       placeholder="Cari pelanggan berdasarkan nama, telepon, atau NIK"
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
                
                {{-- Hidden input untuk menjaga sort order jika diperlukan logika lanjutan --}}
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
                        <th style="width: 25%;">Nama Pelanggan</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th style="width: 30%;">Alamat</th>
                        <th>NIK</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_pelanggan as $index => $pelanggan)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($data_pelanggan->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                {{ $pelanggan->nama }}
                            </span>
                        </td>

                        <td>
                            @if(in_array(strtolower($pelanggan->jenis_kelamin), ['laki-laki', 'l']))
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary" style="font-size: 0.85rem; line-height: 1">
                                    Laki-laki
                                </span>
                            @elseif(in_array(strtolower($pelanggan->jenis_kelamin), ['perempuan', 'p']))
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.85rem; line-height: 1">
                                    Perempuan
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.85rem; line-height: 1">
                                    {{ $pelanggan->jenis_kelamin }}
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="fw-medium text-secondary">
                                {{ $pelanggan->no_telp }}
                            </span>
                        </td>

                        <td>
                            <span class="text-muted small d-block" 
                                  style="line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $pelanggan->alamat }}
                            </span>
                        </td>

                        <td>
                            <span class="fw-medium text-dark">
                                {{ $pelanggan->identitas }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Lihat (Optional, ada di Main tapi tidak di styling baru, saya tambahkan icon mata) --}}
                                @if(Route::has('admin.customers.show'))
                                <a href="{{ route('admin.customers.show', $pelanggan->id) }}" 
                                   class="btn-action text-info"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @endif

                                {{-- Tombol Edit --}}
                                <a href="{{ route('admin.customers.edit', $pelanggan->id) }}" 
                                   class="btn-action btn-action-edit"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.customers.destroy', $pelanggan->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn-action btn-action-delete"
                                            title="Hapus"
                                            onclick="confirmDelete(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-users fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data pelanggan</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_pelanggan->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_pelanggan->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Fungsi Delete (Standard)
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?')) {
            button.form.submit();
        }
    }

    // Fungsi Auto Search (Dari Branch Main)
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
    });
</script>
@endpush