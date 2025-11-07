@extends('layouts.admin')

@section('title', 'Penjualan')

@section('content')

{{-- ======= Header ======= --}}
<h3 class="fw-bold mb-4">Penjualan</h3>

{{-- ======= Toolbar ======= --}}
<div class="bg-white shadow-sm rounded-3 p-3 px-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">

    {{-- Search Bar --}}
    <div class="d-flex align-items-center flex-grow-1" style="max-width: 400px;">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-0">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-0" placeholder="Cari Produk" 
                style="box-shadow: none; background-color: transparent;">
        </div>
    </div>

    {{-- Filter Controls --}}
    <div class="d-flex flex-wrap align-items-center gap-3 text-muted fw-medium">
        <div class="d-flex align-items-center gap-1">
            <span>Sort By :</span>
            <select class="form-select form-select-sm border-0 text-muted" style="width: 120px;">
                <option>Default</option>
                <option>Nama (A-Z)</option>
                <option>Harga (Murah)</option>
                <option>Harga (Mahal)</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-1">
            <span>Group By :</span>
            <select class="form-select form-select-sm border-0 text-muted" style="width: 120px;">
                <option>Status</option>
                <option>Tersedia</option>
                <option>Habis</option>
            </select>
        </div>

        <button class="btn btn-sm btn-light border-0 shadow-sm">
            <i class="fa-solid fa-filter me-1"></i> Filter
        </button>
    </div>
</div>

{{-- ======= Grid Produk ======= --}}
<div id="gridView" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3 mb-5">
    @for ($i = 1; $i <= 20; $i++)
    <div class="col">
        <div class="card h-100 shadow-sm product-card">
            <img src="https://placehold.co/300x350/png" class="card-img-top" alt="Produk">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title fs-6 mb-1">Canon Eos 600D</h5>
                <p class="card-text fw-bold text-primary mb-3">Rp1.240.000</p>
                <button class="btn btn-primary w-100 mt-auto">Tambah Produk</button>
            </div>
        </div>
    </div>
    @endfor
</div>

{{-- ======= Pagination ======= --}}
<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Berikutnya</a>
            </li>
        </ul>
    </nav>
</div>

@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.8rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-img-top {
        height: 220px;
        object-fit: cover;
    }
    .input-group-text i {
        font-size: 1rem;
    }
</style>
@endpush
