@extends('layouts.admin')

@section('title', 'Data Karyawan')

@push('page-actions')
    <a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Karyawan</span>
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
                   placeholder="Cari karyawan berdasarkan nama..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown Filter --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            {{-- Dropdown Jabatan --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Semua Jabatan</option>
                {{-- Loop jabatan disini jika dinamis --}}
                <option value="Manager">Manager</option>
                <option value="Teknisi">Teknisi</option>
                <option value="Staff">Staff Administrasi</option>
            </select>

            {{-- Dropdown Status --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Non Aktif</option>
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
                    <th class="py-3">Nama Karyawan</th>
                    <th class="py-3">Nomor Telepon</th>
                    <th class="py-3">Jabatan</th>
                    <th class="py-3">Status</th>
                    <th class="text-center py-3" style="width: 120px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $employee)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="text-center">{{ $employees->firstItem() + $index }}</td>
                        
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <a href="{{ route('admin.employees.show', $employee->id) }}" class="fw-bold text-dark text-decoration-none" style="font-size: 0.95rem;">
                                        {{ $employee->nama_lengkap }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <td class="text-muted">{{ $employee->nomor_telepon }}</td>
                        <td class="fw-medium text-dark">{{ $employee->jabatan }}</td>

                        {{-- Status Badge --}}
                        <td>
                            @if ($employee->status === 'aktif')
                                <span class="text-success fw-semibold">Aktif</span>
                            @else
                                <span class="text-danger fw-semibold">Non Aktif</span>
                            @endif
                        </td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" 
                                   class="btn btn-sm btn-light text-primary border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-users-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Data Karyawan</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination di Footer Card --}}
    @if ($employees->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush