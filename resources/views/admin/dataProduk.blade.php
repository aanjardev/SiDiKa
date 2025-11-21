@extends('layouts.admin')

@section('title', 'Data Produk')

@push('page-actions')
<a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Produk</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent"
                placeholder="Cari produk berdasarkan nama atau SKU"
                style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Semua Kategori</option>
            </select>
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Urutkan: Terakhir diubah</option>
                <option value="az">Nama (A-Z)</option>
                <option value="za">Nama (Z-A)</option>
            </select>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden; min-height: 700px;">
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
                    <tr> 
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

                                {{-- Bagian Teks (Dibatasi Lebarnya & Line Clamp) --}}
                                <div class="flex-grow-1" style="min-width: 200px; max-width: 320px;">
                                    <span class="text-dark fw-semibold d-block" 
                                          style="font-size: 0.95rem; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $product->nama_produk }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <span class="fw-medium text-secondary">
                                {{ $product->kode_sku ?? '-' }}
                            </span>
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

                        <td class="text-center">
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
                                        onclick="confirmDelete(this)">
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

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush