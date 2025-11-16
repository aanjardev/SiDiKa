@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')

@push('page-actions')
<a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah User</span>
</a>
@endpush

@section('content')

{{-- Search & Filter --}}
<div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
    <div class="flex-grow-1">
        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari berdasarkan nama">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
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
        <table class="table align-middle mb-0 table-product">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Jenis Akses</th>
                    <th>Status</th>
                    <th class="text-center" style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_data as $k)
                <tr>
                    <td>{{ $k->id }}</td>

                    <td class="fw-semibold text-primary" style="cursor:pointer;">
                        {{ $k->name }}
                    </td>

                    <td>{{ $k->email }}</td>
                    <td>{{ $k->role }}</td>
                    <td>
                        <span class="{{ $k->status == 'aktif' ? 'text-success' : 'text-danger' }} fw-semibold">
                            {{ ucfirst($k->status) }}
                        </span>
                    </td>

                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Edit -->
                            <a href="{{ route('admin.permissions.edit', $k->id) }}" title="Edit">
                                <i class="fa-solid fa-pen-to-square" style="color:#0d6efd;"></i>
                            </a>

                            <!-- Delete -->
                            <form action="{{ route('admin.permissions.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- Pagination --}}
<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination mb-0">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul>
    </nav>
</div>

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