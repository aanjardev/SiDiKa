@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Pelanggan') {{-- Judul Halaman --}}

@section('content')


{{-- Search & Filter --}}
<div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
    <div class="flex-grow-1 ">

        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari pelanggan berdasarkan nama atau nomor telepon...">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 8px);">>
        <option selected>Semua Kategori</option>
    </select>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 8px);">
        <option selected>Terakhir diubah</option>
    </select>
</div>

{{-- Table --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-product">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th>Nama</th>
                    <th style="width:40%">Alamat</th>
                    <th>No. Telepon</th>
                    <th>NIK</th>
                                        <!-- <th style="width: 50px;"></th> -->

                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 4; $i++)
                    <tr>
                    <td class="text-center">{{ $i }}</td>

                    <td>Aan Anjar</td>
                    <td>Jl. Lorem Ipsum</td>
                    <td>0812345678</td>
                    <td>24130123945850</td>

                    <!-- <td class="text-end">
                        <button class="btn btn-sm btn-light">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </td> -->
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
    /* Warna baris genap */
    .table-product tbody tr:nth-child(even) {
        background-color: #F8F9FC;
        /* abu muda lembut */
    }

    /* Hover biar smooth */
    .table-product tbody tr:hover {
        background-color: #EFF3F9;
        transition: 0.2s;
    }


</style>
@endpush
