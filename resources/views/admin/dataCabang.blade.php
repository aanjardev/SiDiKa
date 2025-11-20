@extends('layouts.admin')

@section('title', 'Data Cabang')

@push('page-actions')
    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Cabang</span>
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
                   placeholder="Cari cabang berdasarkan nama atau alamat..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown (Opsional, untuk konsistensi layout) --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Urutkan: Terbaru</option>
                <option value="az">Nama (A-Z)</option>
                <option value="za">Nama (Z-A)</option>
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
                    <th class="py-3">Nama Cabang</th>
                    <th class="py-3" style="width: 40%;">Alamat</th>
                    <th class="py-3">Nomor Telepon</th>
                    <th class="text-center py-3" style="width: 120px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($data_cabang as $cabang)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        
                        {{-- Nama Cabang --}}
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                {{ $cabang->nama }}
                            </span>
                        </td>

                        {{-- Alamat dengan Link Maps --}}
                        <td>
                            @if ($cabang->link_maps)
                                <a href="{{ $cabang->link_maps }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="text-decoration-none text-secondary d-flex align-items-start gap-2"
                                   title="Lihat di Google Maps">
                                    <i class="fa-solid fa-location-dot text-danger mt-1"></i>
                                    <span>{{ $cabang->alamat }}</span>
                                </a>
                            @else
                                <div class="text-secondary d-flex align-items-start gap-2">
                                    <i class="fa-solid fa-map-pin text-muted mt-1"></i>
                                    <span>{{ $cabang->alamat }}</span>
                                </div>
                            @endif
                        </td>

                        <td class="text-muted">{{ $cabang->nomor_telepon }}</td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.branches.edit', $cabang->id) }}" 
                                   class="btn btn-sm btn-light text-primary border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.branches.destroy', $cabang->id) }}" method="POST" class="d-inline">
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
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-store fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Data Cabang</h5>
                                <p class="text-muted small mb-0">Silakan tambah cabang baru untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if ($data_cabang->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
            {{ $data_cabang->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus data cabang ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush