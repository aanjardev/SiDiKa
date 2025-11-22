@extends('layouts.admin')

@section('title', 'Data Kategori')

@push('page-actions')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Kategori</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent"
                placeholder="Cari kategori..."
                style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Urutkan: A-Z</option>
                <option value="za">Urutkan: Z-A</option>
                <option value="newest">Terbaru</option>
            </select>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th>Nama Kategori</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($categories->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                        {{ $category->nama_kategori }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
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
                        <td colspan="3" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada kategori</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($categories, 'hasPages') && $categories->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush