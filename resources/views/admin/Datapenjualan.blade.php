@extends('layouts.admin')

@section('title', 'Penjualan')

@section('content')

{{-- ======= Search & Filter (Style Baru) ======= --}}
<div class="d-flex flex-wrap gap-2 align-items-center mb-4">

    {{-- Search Bar (Dari style baru Anda) --}}
    <div class="flex-grow-1">
        <div class="input-group shadow-sm">
            <span class="input-group-text" id="search-addon" style="background: #fff; border-right: 0;">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama atau SKU..." aria-label="Cari produk" aria-describedby="search-addon" style="border-left: 0; box-shadow: none;">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option selected>Semua Kategori</option>
          @foreach ($kategori as $kat)
              <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
          @endforeach
    </select>

    {{-- Filter Sort By (Gabungan dari style lama dan baru) --}}
    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option value="terbaru" selected>Terakhir diubah</option>
        <option value="nama_asc">Nama (A-Z)</option>
        <option value="harga_asc">Harga Termurah</option>
        <option value="harga_desc">Harga Termahal</option>
    </select>

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
