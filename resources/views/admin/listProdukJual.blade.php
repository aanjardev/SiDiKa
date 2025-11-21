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
    @forelse ($products as $product)
        <div class="col">
            <div class="card h-100 shadow-sm product-card"
                 data-product-id="{{ $product->id }}"
                 data-price="{{ $product->harga_jual ?? 0 }}"
                 data-stock="{{ $product->stok_produk ?? 0 }}">
                @php
                    $image = $product->gambarUtama ? $product->gambarUtama->url : asset('images/placeholder.jpg');
                @endphp
                <img src="{{ $image }}" class="card-img-top" alt="{{ $product->nama_produk }}">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6 mb-1">{{ $product->nama_produk }}</h5>
                    <p class="mb-1 text-muted small">{{ $product->kode_sku }}</p>
                    <p class="card-text fw-bold text-primary mb-3">Rp{{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                    <small class="text-muted mb-2">Stok: {{ $product->stok_produk ?? 0 }} | {{ $product->kategori->nama_kategori ?? 'Tanpa Kategori' }}</small>

                    @if (($product->stok_produk ?? 0) > 0)
                        <button class="btn btn-primary w-100 mt-auto btn-add-product" type="button">Tambah Produk</button>
                        <div class="qty-control d-none mt-3 justify-content-center">
                            <button class="qty-btn btn-dec" type="button">-</button>
                            <span class="qty-value">0</span>
                            <button class="qty-btn btn-inc" type="button">+</button>
                        </div>
                    @else
                        <div class="alert alert-secondary mt-auto mb-0 py-2 text-center small">Stok habis</div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col">
            <div class="text-center py-5 border rounded">
                <i class="fa-solid fa-box-open fa-2x text-muted mb-3"></i>
                <h5 class="mb-1">Tidak Ada Produk</h5>
                <p class="text-muted mb-0">Tambahkan produk terlebih dahulu di halaman Data Produk.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- ======= Pagination ======= --}}
@if ($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- Floating summary --}}
<div id="cartSummary" class="cart-summary d-none">
    <div class="d-flex flex-column text-white">
        <span id="summaryItems" class="fw-semibold">0 item</span>
        <small class="text-white-50">Total</small>
    </div>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span id="summaryPrice" class="fw-semibold text-white">Rp0</span>
        <button id="btnCheckout" type="button" class="btn btn-light btn-sm px-3 rounded-pill">Checkout</button>
    </div>
</div>

<form id="checkoutForm" action="{{ route('admin.sales.checkout') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="items" id="checkoutItems">
</form>

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
    .card-img-top { height: 220px; object-fit: cover; }
    .input-group-text i { font-size: 1rem; }

    .qty-control {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f5f7ff;
        border: 2px solid #375dfb;
        border-radius: 999px;
        padding: 6px 10px;
        min-width: 140px;
    }
    .qty-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #375dfb;
        background: #fff;
        color: #375dfb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        line-height: 1;
        transition: transform 0.1s ease;
    }
    .qty-btn:active { transform: scale(0.95); }
    .qty-value { min-width: 28px; text-align: center; font-weight: 600; }
    .qty-control.shake { animation: shake 0.3s; }
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        50% { transform: translateX(2px); }
        75% { transform: translateX(-2px); }
        100% { transform: translateX(0); }
    }

    .cart-summary {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        bottom: 24px;
        background: #375dfb;
        border-radius: 999px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 12px 28px rgba(0,0,0,0.18);
        min-width: 320px;
        max-width: 640px;
        width: 80%;
        z-index: 1050;
    }
    .cart-summary .fa-bag-shopping { font-size: 18px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const formatRupiah = (number) => {
        const stringNumber = Number(number || 0).toFixed(0);
        const parts = stringNumber.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'Rp' + parts.join('.');
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
            summaryItemsEl.textContent = `${totalItems} item${totalItems > 1 ? 's' : ''}`;
            summaryPriceEl.textContent = formatRupiah(totalPrice);
            summaryEl.classList.remove('d-none');
        } else {
            summaryEl.classList.add('d-none');
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

        btnAdd.addEventListener('click', () => {
            selections[productId] = { qty: 1, price };
            qtyValue.textContent = 1;
            btnAdd.classList.add('d-none');
            qtyControl.classList.remove('d-none');
            updateSummary();
        });

        btnInc.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            if (current >= stock) {
                qtyControl.classList.add('shake');
                setTimeout(() => qtyControl.classList.remove('shake'), 400);
                return;
            }
            const next = current + 1;
            selections[productId] = { qty: next, price };
            qtyValue.textContent = next;
            updateSummary();
        });

        btnDec.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            const next = Math.max(current - 1, 0);

            if (next === 0) {
                delete selections[productId];
                qtyValue.textContent = 0;
                qtyControl.classList.add('d-none');
                btnAdd.classList.remove('d-none');
            } else {
                selections[productId].qty = next;
                qtyValue.textContent = next;
            }
            updateSummary();
        });
    });

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
