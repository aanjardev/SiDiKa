@extends('layouts.admin')

@section('title', 'Data Produk')

@push('page-actions')
<a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Produk</span>
</a>
@endpush

@section('content')

{{-- Search & Filter (Resolved: Visual HEAD + Logic Main) --}}
<form method="GET" action="{{ route('admin.products.index') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari produk berdasarkan nama atau SKU"
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;"
                       autofocus>
            </div>

            {{-- Bagian Kanan: Dropdown Filter --}}
            <div class="d-flex align-items-center gap-2 pe-2">

                {{-- Dropdown Kategori --}}
                <select name="kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_kategori ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($semua_kategori ?? [] as $kat)
                        <option value="{{ $kat->id }}" {{ ($selected_kategori ?? 'all') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Dropdown Sort --}}
                <select name="sort_by"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ ($sort_by ?? 'updated_at') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                </select>

                {{-- Hidden Input --}}
                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 35%;">Produk</th>
                        <th>SKU</th>
                        <th>Harga</th>
                        <th class="text-center">Stok</th>
                        <th>Last Update</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.products.show', $product->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($products->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                {{-- Bagian Gambar --}}
                                <div class="flex-shrink-0 position-relative">
                                    @if ($product->gambarUtama)
                                    <img src="{{ $product->gambarUtama->url }}" loading="lazy"
                                         alt="Img"
                                         class="rounded-3 shadow-sm"
                                         style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                    {{-- Placeholder --}}
                                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-secondary fw-bold"
                                         style="width: 45px; height: 45px; font-size: 0.7rem;">
                                        Img
                                    </div>
                                    @endif
                                </div>

                                {{-- Bagian Teks --}}
                                <div class="flex-grow-1" style="min-width: 200px; max-width: 320px;">
                                    <span class="text-dark fw-semibold d-block"
                                        style="font-size: 0.95rem !important; 
                                            line-height: 1.4 !important; 
                                            display: -webkit-box !important; 
                                            -webkit-line-clamp: 2 !important; 
                                            -webkit-box-orient: vertical !important; 
                                            overflow: hidden !important; 
                                            text-overflow: ellipsis !important; 
                                            word-wrap: break-word !important;">
                                        {{ $product->nama_produk }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <a href="{{ route('admin.products.show', $product->id) }}" 
                               class="fw-medium text-primary text-decoration-none clickable-code"
                               onclick="event.stopPropagation();">
                                {{ $product->kode_sku ?? '-' }}
                            </a>
                        </td>

                        <td class="fw-bold text-dark text-nowrap">
                            Rp{{ number_format($product->harga_jual, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            @if($product->stok_produk > 5)
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size: 0.9rem; line-height: 1">
                                {{ $product->stok_produk }}
                            </span>
                            @elseif($product->stok_produk > 0)
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size: 0.9rem; line-height: 1">
                                {{ $product->stok_produk }}
                            </span>
                            @else
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.9rem; line-height: 1">
                                Habis
                            </span>
                            @endif
                        </td>

                        <td class="text-muted small text-nowrap">
                            {{ $product->updated_at->format('d M Y') }} <br>
                            <span class="opacity-75">{{ $product->updated_at->format('H:i') }}</span>
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn-action btn-action-delete"
                                            title="Hapus"
                                            onclick="handleDeleteProduk(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-box-open fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data produk</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($products->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        text-decoration: underline !important;
        opacity: 0.8;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi Delete menggunakan sistem alert (nama berbeda untuk menghindari konflik)
    function handleDeleteProduk(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus produk ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                button.form.submit();
            }
        }
    }
    
    // Export ke window
    window.handleDeleteProduk = handleDeleteProduk;

    // Fungsi Auto Search (Debounce)
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;

        if (searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500); // Submit setelah 500ms idle
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    searchForm.submit();
                }
            });
        }
    });
</script>
@endpush
