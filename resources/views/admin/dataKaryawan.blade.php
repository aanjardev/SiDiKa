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
        <table class="table align-middle mb-0 table-product">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th class="text-center" style="width: 70px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 7; $i++)
                    <tr>
                    <td>{{ $i }}</td>
                    <td>15702050600{{ $i }}</td>

                    <td class="fw-semibold text-primary" style="cursor:pointer;">
                        Nama Karyawan {{ $i }}
                    </td>

                    <td>{{ $i % 3 == 0 ? 'Manager' : 'Staff Operasional' }}</td>

                    <td>
                        @if ($i % 2 == 0)
                        <span class="text-danger fw-semibold">Non Aktif</span>
                        @else
                        <span class="text-success fw-semibold">Aktif</span>
                        @endif
                    </td>

                    <td>karyawan{{ $i }}@gmail.com</td>
                    <td>08526{{ rand(300000,900000) }}</td>

                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="" title="Lihat">
                                <i class="fa-solid fa-eye" style="color: black;"></i>
                            </a>

                            <a href="" title="Edit">
                                <i class="fa-solid fa-pen-to-square" style="color:#0d6efd;"></i>
                            </a>

                            <form action="{{ route('admin.employees.destroy', $i) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    </tr>
                    @endfor
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