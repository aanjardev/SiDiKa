@extends('layouts.admin')

@section('title', 'Data Karyawan')

@push('page-actions')
<a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Karyawan</span>
</a>
@endpush

@section('content')

{{-- Search & Filter --}}
<div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
    <div class="flex-grow-1 ">

        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama atau SKU...">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">>
        <option selected>Semua Jabatan</option>
    </select>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option selected>Status</option>
        <option>Aktif</option>
        <option>Non Aktif</option>
    </select>
</div>


{{-- Table --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nomor Telepon</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

                @forelse ($employees as $index => $employee)
                    <tr>
                        <td>{{ $employees->firstItem() + $index }}</td>
                        <td>{{ $employee->nik }}</td>

                        <td>
                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="fw-semibold text-primary text-decoration-none" style="cursor:pointer;">
                                {{ $employee->nama_lengkap }}
                            </a>
                        </td>

                        <td>{{ $employee->jabatan }}</td>

                        <td>
                            @if ($employee->status === 'aktif')
                                <span class="text-success fw-semibold">Aktif</span>
                            @else
                                <span class="text-danger fw-semibold">Non Aktif</span>
                            @endif
                        </td>

                        <td>{{ $employee->user->email ?? '-' }}</td>
                        <td>{{ $employee->nomor_telepon }}</td>

                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" title="Edit">
                                    <i class="fa-solid fa-pen-to-square" style="color:#0d6efd;"></i>
                                </a>

                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-delete" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <p class="text-muted mb-0">Tidak ada data karyawan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- Pagination --}}
@if ($employees->hasPages())
<div class="d-flex justify-content-center mt-4">
    <nav>
        {{ $employees->links() }}
    </nav>
</div>
@endif

@endsection

@push('styles')
<style>
    .table-product tbody tr:nth-child(even) {
        background-color: #F8F9FC;
    }

    .table-product tbody tr:hover {
        background-color: #EFF3F9;
        transition: 0.2s;
    }

    .btn-icon {
        background: none;
        border: none;
        padding: 0;
        color: #dc3545;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-icon:hover {
        color: #bb2d3b;
    }
</style>
@endpush
