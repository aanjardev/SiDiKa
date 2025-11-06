@extends('layouts.admin')

@section('title', 'Data Kategori')

@push('page-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Kategori</span>
    </a>
@endpush

@section('content')

{{-- Search & Button --}}
<div class="card-body d-flex flex-wrap gap-3 align-items-center mb-4 p-0">
    <div class="flex-grow-1 ">
        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari kategori...">
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-product">
            <thead class="table-light">
                <tr>
                    <th style="width:5%">No</th>
                    <th>Nama Kategori</th>
                    <th class="text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $dummyKategori = [
                        ['nama' => 'kamera dslr'],
                        ['nama' => 'kamera mirrorless'],
                        ['nama' => 'kamera pocket/instan'],
                        ['nama' => 'lensa dslr'],
                        ['nama' => 'lensa mirrorless'],
                        ['nama' => 'aksesoris'],
                        ['nama' => 'lain'],
                    ];
                @endphp

                @foreach ($dummyKategori as $i => $kat)
                <tr>
                    <td class="text-center">{{ $i+1 }}</td>
                    <td class="fw-semibold">{{ ucfirst($kat['nama']) }}</td>

                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="">
                                <i class="fa-solid fa-pen-to-square" style="color:#0d6efd;"></i>
                            </a>

                            <button class="btn-icon" onclick="alert('hapus dummy?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
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
        cursor: pointer;
        color: #dc3545;
        font-size: 16px;
    }
    .btn-icon:hover {
        color: #bb2d3b;
    }
</style>
@endpush
