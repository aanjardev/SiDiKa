@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Produk') {{-- Judul Halaman --}}

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
            <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama atau SKU...">
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