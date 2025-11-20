@extends('layouts.admin')

@section('title', 'Data Kategori')

@push('page-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Kategori</span>
    </a>
@endpush

@section('content')

{{-- Filter dan Pencarian (Style: Satu Card Putih Clean) --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Ikon Filter & Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i> 
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent" 
                   placeholder="Cari kategori..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Opsi Tambahan (Opsional, untuk menyeimbangkan layout) --}}
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
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-hover">
            {{-- Header Abu-abu Terang --}}
            <thead class="bg-light"> 
                <tr class="text-dark fw-bold" style="border-bottom: 2px solid #eee;">
                    <th class="text-center py-3" style="width: 5%;">No</th>
                    <th class="py-3">Nama Kategori</th>
                    <th class="text-center py-3" style="width: 120px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        
                        {{-- Nama Kategori --}}
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                {{ $category->nama_kategori }}
                            </span>
                        </td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                   class="btn btn-sm btn-light text-primary border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="btn btn-sm btn-light text-danger border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                            style="width: 32px; height: 32px;" 
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
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada kategori</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination (Jika ada) --}}
    {{-- @if ($categories instanceof \Illuminate\Pagination\LengthAwarePaginator && $categories->hasPages()) --}}
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
        {{-- {{ $categories->links('pagination::bootstrap-5') }} --}}
    </div>
    {{-- @endif --}}
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