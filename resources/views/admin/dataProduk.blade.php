@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Produk') {{-- Judul Halaman --}}

@push('page-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Produk</span>
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
        <option selected>Semua Kategori</option>
    </select>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
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
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Terakhir diubah</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 4; $i++)
                    <tr>
                    <td class="text-center">{{ $i }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://via.placeholder.com/60" class="rounded" style="width:60px; height:60px; object-fit:cover;">
                            <div>
                                <div class="fw-semibold">Canon Eos 600D</div>
                                <small class="text-muted">Kamera DSLR</small>
                            </div>
                        </div>
                    </td>

                    <td>C5DM4-001</td>
                    <td>Rp1.240.000</td>
                    <td>1 Unit</td>
                    <td>17/10/2025 11:30</td>

                    <td class="text-end">
                        <button class="btn btn-sm btn-light">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
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


</style>
@endpush
