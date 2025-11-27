@extends('layouts.admin')

@section('title', 'Katalog Penjualan')

@push('page-actions')
    @php
        $backRoute = route('admin.sales.index');
        if(isset($penjualan)) {
            $backRoute = route('admin.sales.show', $penjualan->id);
        }
    @endphp

    <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endpush

@section('content')

{{-- ======= Search & Filter ======= --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent" 
                placeholder="Cari produk berdasarkan nama atau SKU..."
                style="font-size: 0.95rem;"
                autofocus>
                
        </div>

        {{-- Dropdown Filter --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Semua Kategori</option>
                @foreach ($kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>

            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option value="terbaru" selected>Terbaru</option>
                <option value="nama_asc">Nama (A-Z)</option>
                <option value="harga_asc">Harga Terendah</option>
            </select>
        </div>
    </div>
</div>

{{-- ======= Grid Produk ======= --}}
<div id="gridView" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3 mb-5">
    @forelse ($products as $product)
        <div class="col">
            <div class="card h-100 shadow-sm border-0 product-card" 
                 style="border-radius: 12px; overflow: hidden;"
                 data-product-id="{{ $product->id }}"
                 data-price="{{ $product->harga_jual ?? 0 }}"
                 data-stock="{{ $product->stok_produk ?? 0 }}">
                
                {{-- Gambar Produk (Rasio 4:3 agar tidak terlalu tinggi) --}}
                <div class="position-relative" style="padding-top: 75%; background-color: #f8f9fa;">
                    @php
                        $image = $product->gambarUtama ? $product->gambarUtama->url : asset('images/placeholder.jpg');
                    @endphp
                    <img src="{{ $image }}" 
                         class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" 
                         alt="{{ $product->nama_produk }}" loading="lazy">
                    
                    {{-- Badge Stok (Opsional, jika ingin overlay) --}}
                    @if(($product->stok_produk ?? 0) <= 0)
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center">
                            <span class="badge bg-dark text-white px-3 py-2">Stok Habis</span>
                        </div>
                    @endif
                </div>

                <div class="card-body p-3 d-flex flex-column">
                    {{-- Meta: SKU & Kategori --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $product->kode_sku }}</small>
                        <span class="badge bg-light text-secondary border border-light-subtle text-warp text-end" style="font-size: 0.7rem;">
                            {{ $product->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>

                    {{-- Nama Produk --}}
                    <h6 class="card-title fw-semibold text-dark mb-1 text-truncate-2" style="font-size: 0.95rem; min-height: 2.4em; line-height: 1.2em;">
                        {{ $product->nama_produk }}
                    </h6>

                    {{-- Harga --}}
                    <p class="card-text fw-bold text-primary mb-3" style="font-size: 1.1rem;">
                        Rp{{ number_format($product->harga_jual, 0, ',', '.') }}
                    </p>

                    {{-- Aksi & Stok --}}
                    <div class="mt-auto d-flex align-items-center gap-2">
                        @if (($product->stok_produk ?? 0) > 0)
                            {{-- Tombol Tambah --}}
                            <button class="btn btn-primary btn-sm flex-grow-1 fw-medium btn-add-product py-1" type="button" style="border-radius: 8px; font-size: 0.85rem;">
                                <i class="fa-solid fa-plus me-1"></i> Tambah
                            </button>
                            
                            {{-- Kontrol Qty (Hidden Awal) --}}
                            <div class="qty-control d-none flex-grow-1 justify-content-between align-items-center px-1 bg-primary bg-opacity-10 rounded-3" style="height: 32px;">
                                <button class="btn btn-sm p-0 text-primary btn-dec" style="width: 24px;"><i class="fa-solid fa-minus" style="font-size: 0.8rem;"></i></button>
                                <span class="qty-value fw-bold text-primary small">0</span>
                                <button class="btn btn-sm p-0 text-primary btn-inc" style="width: 24px;"><i class="fa-solid fa-plus" style="font-size: 0.8rem;"></i></button>
                            </div>

                            {{-- Info Stok --}}
                            <small class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
                                Stok: <strong>{{ $product->stok_produk }}</strong>
                            </small>
                        @else
                            <button class="btn btn-light text-muted w-100 btn-sm py-1 border" disabled style="border-radius: 8px; font-size: 0.85rem;">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                    <i class="fa-solid fa-box-open fa-2x text-secondary opacity-50"></i>
                </div>
                <h6 class="text-muted">Tidak ada produk ditemukan</h6>
            </div>
        </div>
    @endforelse
</div>

{{-- ======= Pagination ======= --}}
@if ($products->hasPages())
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- Floating Cart Summary --}}
<div id="cartSummary" class="position-fixed start-50 translate-middle-x bottom-0 mb-4 p-2 bg-dark text-white rounded-pill shadow-lg d-none align-items-center gap-3 z-3" 
     style="min-width: 300px; max-width: 90%; border: 1px solid rgba(255,255,255,0.1);">
    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center ms-1" style="width: 32px; height: 32px;">
        <i class="fa-solid fa-bag-shopping" style="font-size: 0.9rem;"></i>
    </div>
    <div class="d-flex flex-column lh-1">
        <span class="fw-bold" id="summaryItems" style="font-size: 0.9rem;">0 Item</span>
        <small class="text-white-50" style="font-size: 0.7rem;">Total Estimasi</small>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2">
        <span class="fw-bold me-2" id="summaryPrice">Rp0</span>
        <button id="btnCheckout" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium">
            Checkout <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
        </button>
    </div>
</div>

<form id="checkoutForm" action="{{ route('admin.sales.checkout') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="items" id="checkoutItems">
</form>

@endsection

@push('styles')
<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-card {
        transition: all 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
    }
    .qty-control.shake { animation: shake 0.3s; }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%, 75% { transform: translateX(-2px); }
        50% { transform: translateX(2px); }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const formatRupiah = (number) => {
        return 'Rp' + Number(number || 0).toLocaleString('id-ID');
    };

    const summaryEl = document.getElementById('cartSummary');
    const summaryItemsEl = document.getElementById('summaryItems');
    const summaryPriceEl = document.getElementById('summaryPrice');
    const checkoutBtn = document.getElementById('btnCheckout');
    const selections = {};

    const updateSummary = () => {
        const totalItems = Object.values(selections).reduce((sum, item) => sum + item.qty, 0);
        const totalPrice = Object.values(selections).reduce((sum, item) => sum + (item.qty * item.price), 0);

        if (totalItems > 0) {
            summaryItemsEl.textContent = `${totalItems} Item${totalItems > 1 ? 's' : ''}`;
            summaryPriceEl.textContent = formatRupiah(totalPrice);
            summaryEl.classList.remove('d-none');
            summaryEl.classList.add('d-flex');
        } else {
            summaryEl.classList.add('d-none');
            summaryEl.classList.remove('d-flex');
        }
    };

    document.querySelectorAll('.product-card').forEach(card => {
        const productId = card.dataset.productId;
        const price = Number(card.dataset.price || 0);
        const stock = Number(card.dataset.stock || 0);
        const btnAdd = card.querySelector('.btn-add-product');
        const qtyControl = card.querySelector('.qty-control');
        const btnInc = card.querySelector('.btn-inc');
        const btnDec = card.querySelector('.btn-dec');
        const qtyValue = card.querySelector('.qty-value');

        if (!btnAdd || !qtyControl) return;

        // Tambah ke keranjang
        btnAdd.addEventListener('click', () => {
            selections[productId] = { qty: 1, price };
            qtyValue.textContent = 1;
            btnAdd.classList.add('d-none');
            qtyControl.classList.remove('d-none');
            qtyControl.classList.add('d-flex'); // Flex enable
            updateSummary();
        });

        // Tambah Qty
        btnInc.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            if (current >= stock) {
                qtyControl.classList.add('shake');
                setTimeout(() => qtyControl.classList.remove('shake'), 300);
                return;
            }
            const next = current + 1;
            selections[productId] = { qty: next, price };
            qtyValue.textContent = next;
            updateSummary();
        });

        // Kurang Qty
        btnDec.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            const next = Math.max(current - 1, 0);

            if (next === 0) {
                delete selections[productId];
                qtyValue.textContent = 0;
                qtyControl.classList.add('d-none');
                qtyControl.classList.remove('d-flex');
                btnAdd.classList.remove('d-none');
            } else {
                selections[productId].qty = next;
                qtyValue.textContent = next;
            }
            updateSummary();
        });
    });

    // Checkout Action
    checkoutBtn?.addEventListener('click', () => {
        const totalItems = Object.values(selections).reduce((sum, item) => sum + item.qty, 0);
        if (totalItems === 0) return;
        
        const payload = Object.entries(selections).map(([id, data]) => ({
            id,
            qty: data.qty
        }));
        
        document.getElementById('checkoutItems').value = JSON.stringify(payload);
        document.getElementById('checkoutForm').submit();
    });
});
</script>
@endpush