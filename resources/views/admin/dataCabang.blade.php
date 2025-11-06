@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Cabang') {{-- Judul Halaman --}}

@section('content')

<!-- <div class="row mb-4">
    <div class="col-12 d-flex justify-content-end gap-3">
        {{-- Tombol Penjualan --}}
        <button class="btn btn-primary d-flex align-items-center gap-2">
            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4V20M20 12L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span>Produk</span>
        </button>
    </div>
</div> -->


{{-- Search & Filter --}}
<div class="card-body d-flex flex-wrap gap-3 align-items-center mb-4">
    <div class="flex-grow-1 ">

        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari nama cabang...">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm">
        <option selected>Semua Kategori</option>
    </select>

    <select class="form-select w-auto shadow-sm">
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
                    <th>Nama Cabang</th>
                    <th style="width:40%">Alamat</th>
                    <th>No. Telepon</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 4; $i++)
                    <tr>
                    <td class="text-center">{{ $i }}</td>

                    <td>Cabang Tlogomas</td>
                    <td>Jl. Lorem Ipsum</td>
                    <td>0812345678</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            {{-- Tombol Lihat --}}
                            <a href="" title="Lihat">
                                <i class="fa-solid fa-eye" style="color: black;"></i>
                            </a>

                            {{-- Tombol Edit --}}
                            <a href="" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" title="Hapus">
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

    .btn-icon {
        background: none;
        border: none;
        padding: 0;
        color: #dc3545;
        /* Merah lembut */
        cursor: pointer;
        font-size: 16px;
    }

    .btn-icon:hover {
        color: #bb2d3b;
        /* Merah sedikit lebih gelap pas hover */
    }
</style>
@endpush