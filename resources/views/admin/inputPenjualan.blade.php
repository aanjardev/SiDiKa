@extends('layouts.admin')

@section('title', 'Transaksi Penjualan')

@push('page-actions')
    {{-- Halaman form tidak perlu tombol aksi di header --}}
@endpush

@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            <strong>Ada Kesalahan Input!</strong>
        </div>
        <ul class="mb-0 mt-1 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $isEdit = isset($penjualan);
    $items = $items ?? [];
    $daftar_produk = $daftar_produk ?? collect();
    $produkUntukJs = $daftar_produk->map(function ($produk) {
        return [
            'id' => $produk->id,
            'nama_produk' => $produk->nama_produk,
            'kode_sku' => $produk->kode_sku,
            'harga_jual' => $produk->harga_jual,
            'stok_produk' => is_null($produk->stok_produk) ? null : (int) $produk->stok_produk,
        ];
    })->values()->toArray();
@endphp

<form action="{{ $isEdit ? route('admin.sales.update', $penjualan->id) : route('admin.sales.store') }}" method="POST" id="formPenjualan">
    @csrf
    <input type="hidden" name="items" id="itemsInput" value='{{ $raw_items ?? '[]' }}'>
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        
        {{-- KOLOM KIRI: Informasi & Item --}}
        <div class="col-lg-8">

            {{-- CARD 1: Informasi Transaksi --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-file-invoice me-2 text-primary"></i>Informasi Transaksi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        {{-- Customer --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Customer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                                <select class="form-select border-start-0 ps-2 @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required style="height: 45px;">
                                    <option value="" disabled {{ old('customer_id', $penjualan->customer_id ?? '') ? '' : 'selected' }}>Pilih customer...</option>
                                    @foreach($semua_customer as $customer)
                                    <option value="{{ $customer->id }}" {{ (string) old('customer_id', $penjualan->customer_id ?? '') === (string) $customer->id ? 'selected' : '' }}>
                                        {{ $customer->nama }} ({{ $customer->no_telp }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                @error('customer_id') <div class="text-danger small">{{ $message }}</div> @enderror
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer" class="small text-decoration-none fw-bold text-primary">
                                    <i class="fa-solid fa-plus-circle me-1"></i>Customer Baru
                                </a>
                            </div>
                        </div>

                        {{-- Cabang --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Lokasi Transaksi (Cabang)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-store"></i></span>
                                <select class="form-select border-start-0 ps-2 @error('perusahaan_cabang_id') is-invalid @enderror" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required style="height: 45px;">
                                    @foreach ($semua_cabang as $branch)
                                        <option value="{{ $branch->id }}" {{ (string) old('perusahaan_cabang_id', $penjualan->perusahaan_cabang_id ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                            {{ $branch->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('perusahaan_cabang_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: Daftar Item --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cart-shopping me-2 text-warning"></i>Item Penjualan
                    </h6>
                    <button type="button" class="btn btn-primary btn-sm fw-medium d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
                        <i class="fa-solid fa-plus fa-fw"></i> Tambah Item
                    </button>
                </div>
                
                <div class="card-body p-4">
                    <div class="table-responsive border rounded-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small">
                                <tr>
                                    <th class="ps-3 py-3">Nama Produk</th>
                                    <th class="text-center py-3" style="width: 120px;">Qty</th>
                                    <th class="text-end py-3" style="width: 140px;">Harga Satuan</th>
                                    <th class="text-end pe-3 py-3" style="width: 160px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tableItemsBody">
                                @forelse ($items as $item)
                                    <tr data-product-id="{{ $item['product']->id ?? '' }}">
                                        <td class="ps-3">
                                            <div class="fw-semibold text-dark">{{ $item['product']->nama_produk ?? 'Produk tidak tersedia' }}</div>
                                            <small class="text-muted font-monospace">{{ $item['product']->kode_sku ?? '-' }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if(isset($item['product']->id))
                                                <div class="input-group input-group-sm qty-control justify-content-center" data-product-id="{{ $item['product']->id }}" style="width: 100px; margin: auto;">
                                                    <button type="button" class="btn btn-light border btn-qty-dec" data-product-id="{{ $item['product']->id }}"><i class="fa-solid fa-minus"></i></button>
                                                    <span class="input-group-text bg-white border-start-0 border-end-0 fw-bold qty-value" style="min-width: 30px; justify-content: center;" data-product-id="{{ $item['product']->id }}">{{ $item['qty'] }}</span>
                                                    <button type="button" class="btn btn-light border btn-qty-inc" data-product-id="{{ $item['product']->id }}"><i class="fa-solid fa-plus"></i></button>
                                                </div>
                                            @else
                                                x{{ $item['qty'] }}
                                            @endif
                                        </td>
                                        <td class="text-end text-muted small">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td class="text-end pe-3 fw-medium text-dark">Rp{{ number_format($item['line_total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">
                                            <i class="fa-solid fa-cart-plus fa-2x mb-2 opacity-50"></i>
                                            <p class="small mb-0">Belum ada item yang dipilih.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Pembayaran --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px; border-radius: 10px; z-index: 10;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-cash-register me-2 text-success"></i>Rincian Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Metode Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Metode Pembayaran</label>
                        @php $kasValue = old('kas', $penjualan->kas ?? 'cash'); @endphp
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-wallet"></i></span>
                            <select name="kas" class="form-select border-start-0 ps-2" style="height: 45px;">
                                <option value="cash" {{ $kasValue === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                                <option value="transfer" {{ $kasValue === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Input Biaya Tambahan & Diskon --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small">Diskon</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                <input type="number" name="diskon" class="form-control border-start-0 ps-1" value="{{ old('diskon', $penjualan->diskon ?? 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-medium text-secondary small">Biaya Tambahan</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                                <input type="number" name="biaya_tambahan" class="form-control border-start-0 ps-1" value="{{ old('biaya_tambahan', $biaya_tambahan_awal ?? 0) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Depresiasi (Pengurangan Nilai)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted border-end-0">Rp</span>
                            <input type="number" name="depresiasi" class="form-control border-start-0 ps-1" value="{{ old('depresiasi', $depresiasi_awal ?? 0) }}" min="0">
                        </div>
                    </div>

                    {{-- Breakdown Kalkulasi --}}
                    <div class="bg-light p-3 rounded-3 mb-3 small" id="breakdownSection">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Subtotal Produk</span>
                            <span class="fw-bold text-dark" id="lineSubtotal">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger mb-1" id="lineDiskonRow" style="display: none;">
                            <span>Diskon</span>
                            <span id="lineDiskon">-Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger mb-1" id="lineDepresiasiRow" style="display: none;">
                            <span>Depresiasi</span>
                            <span id="lineDepresiasi">-Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between text-success mb-1" id="lineBiayaRow" style="display: none;">
                            <span>Biaya Tambahan</span>
                            <span id="lineBiaya">Rp0</span>
                        </div>
                        <div class="border-top my-2"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">TOTAL BAYAR</span>
                            <span class="fw-bold text-primary fs-5" id="subtotalValue">Rp{{ number_format($subtotal ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Catatan Transaksi</label>
                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Opsional...">{{ old('keterangan', $penjualan->keterangan ?? '') }}</textarea>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            {{ $isEdit ? 'Update Transaksi' : 'Proses Transaksi' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Tambah Item --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 10px;">
            <form id="formTambahItem">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Tambah Produk
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Pilih Produk</label>
                        <select class="form-select" id="produkBaru" style="height: 45px;">
                            <option value="" selected disabled>-- Cari Produk --</option>
                            @foreach(($daftar_produk ?? collect()) as $produk)
                                <option value="{{ $produk->id }}"
                                    data-price="{{ $produk->harga_jual }}"
                                    data-stock="{{ is_null($produk->stok_produk) ? '' : $produk->stok_produk }}">
                                    {{ $produk->nama_produk }} ({{ $produk->kode_sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Jumlah</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-light border" onclick="document.getElementById('qtyProdukBaru').stepDown()"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" class="form-control text-center" id="qtyProdukBaru" value="1" min="1">
                            <button type="button" class="btn btn-light border" onclick="document.getElementById('qtyProdukBaru').stepUp()"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="alert alert-warning d-none small border-0 bg-warning bg-opacity-10 text-warning" id="infoStokProduk"></div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light border px-4 fw-medium text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 fw-medium">Tambah ke Keranjang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemsInput = document.getElementById('itemsInput');
    const tableBody = document.getElementById('tableItemsBody');
    const subtotalEl = document.getElementById('subtotalValue');
    const submitBtn = document.querySelector('#formPenjualan button[type="submit"]');
    const lineSubtotalEl = document.getElementById('lineSubtotal');
    const lineDiskonRow = document.getElementById('lineDiskonRow');
    const lineDiskon = document.getElementById('lineDiskon');
    const lineDepresiasiRow = document.getElementById('lineDepresiasiRow');
    const lineDepresiasi = document.getElementById('lineDepresiasi');
    const lineBiayaRow = document.getElementById('lineBiayaRow');
    const lineBiaya = document.getElementById('lineBiaya');
    const diskonInput = document.querySelector('input[name=\"diskon\"]');
    const biayaInput = document.querySelector('input[name=\"biaya_tambahan\"]');
    const depresiasiInput = document.querySelector('input[name=\"depresiasi\"]');

    let cartItems = [];
    try {
        cartItems = JSON.parse(itemsInput.value || '[]');
    } catch (error) {
        cartItems = [];
    }

    if (!Array.isArray(cartItems)) {
        cartItems = [];
    }

    const productCatalog = @json($produkUntukJs);

    const productsMap = {};
    productCatalog.forEach(prod => {
        productsMap[String(prod.id)] = prod;
    });

    const formatRupiah = (value) => {
        const number = Number(value || 0);
        return 'Rp' + number.toLocaleString('id-ID');
    };

    const syncHiddenInput = () => {
        itemsInput.value = JSON.stringify(cartItems);
    };

    const recalcTotals = () => {
        let subtotal = 0;
        cartItems.forEach(item => {
            const product = productsMap[String(item.id)];
            if (!product) {
                return;
            }
            subtotal += item.qty * (Number(product.harga_jual) || 0);
        });

        const diskonVal = Math.max(0, Number(diskonInput?.value || 0));
        const biayaVal = Math.max(0, Number(biayaInput?.value || 0));
        const depresiasiVal = Math.max(0, Number(depresiasiInput?.value || 0));

        const total = Math.max(0, subtotal - diskonVal - depresiasiVal + biayaVal);

        if (lineSubtotalEl) lineSubtotalEl.textContent = formatRupiah(subtotal);
        if (lineDiskon && lineDiskonRow) {
            lineDiskon.textContent = `-` + formatRupiah(diskonVal);
            lineDiskonRow.style.display = diskonVal > 0 ? 'flex' : 'none';
        }
        if (lineDepresiasi && lineDepresiasiRow) {
            lineDepresiasi.textContent = `-` + formatRupiah(depresiasiVal);
            lineDepresiasiRow.style.display = depresiasiVal > 0 ? 'flex' : 'none';
        }
        if (lineBiaya && lineBiayaRow) {
            lineBiaya.textContent = formatRupiah(biayaVal);
            lineBiayaRow.style.display = biayaVal > 0 ? 'flex' : 'none';
        }

        if (subtotalEl) {
            subtotalEl.textContent = formatRupiah(total);
        }
        if (submitBtn) {
            submitBtn.disabled = cartItems.length === 0;
        }
    };

    const renderRows = () => {
        if (!tableBody) {
            return;
        }
        if (!cartItems.length) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-5"><i class="fa-solid fa-cart-plus fa-2x mb-2 opacity-50"></i><p class="small mb-0">Belum ada item yang dipilih.</p></td></tr>';
            recalcTotals();
            return;
        }

        const fragment = document.createDocumentFragment();
        cartItems.forEach(item => {
            const product = productsMap[String(item.id)];
            if (!product) {
                return;
            }
            const price = Number(product.harga_jual) || 0;
            const row = document.createElement('tr');
            row.dataset.productId = item.id;
            row.innerHTML = `
                <td class="ps-3">
                    <div class="fw-semibold text-dark">${product.nama_produk}</div>
                    <small class="text-muted font-monospace">${product.kode_sku ?? '-'}</small>
                </td>
                <td class="text-center">
                    <div class="input-group input-group-sm qty-control justify-content-center" data-product-id="${item.id}" style="width: 100px; margin: auto;">
                        <button type="button" class="btn btn-light border btn-qty-dec" data-product-id="${item.id}"><i class="fa-solid fa-minus"></i></button>
                        <span class="input-group-text bg-white border-start-0 border-end-0 fw-bold qty-value" style="min-width: 30px; justify-content: center;" data-product-id="${item.id}">${item.qty}</span>
                        <button type="button" class="btn btn-light border btn-qty-inc" data-product-id="${item.id}"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </td>
                <td class="text-end text-muted small">${formatRupiah(price)}</td>
                <td class="text-end pe-3 fw-medium text-dark">${formatRupiah(item.qty * price)}</td>
            `;
            fragment.appendChild(row);
        });

        tableBody.innerHTML = '';
        tableBody.appendChild(fragment);
        recalcTotals();
    };

    const findIndex = (productId) => cartItems.findIndex(item => String(item.id) === String(productId));

    const getLimitQty = (productId) => {
        const product = productsMap[String(productId)];
        if (!product) {
            return Infinity;
        }
        if (product.stok_produk === null || product.stok_produk === undefined) {
            return Infinity;
        }
        const stock = Number(product.stok_produk);
        return isNaN(stock) ? Infinity : Math.max(stock, 0);
    };

    const updateQty = (productId, delta) => {
        const index = findIndex(productId);
        if (index === -1) {
            return;
        }
        const limit = getLimitQty(productId);
        const currentQty = Number(cartItems[index].qty || 0);
        let nextQty = currentQty + delta;
        if (nextQty < 1) {
            cartItems.splice(index, 1);
        } else {
            if (limit !== Infinity && nextQty > limit) {
                nextQty = limit;
            }
            cartItems[index].qty = nextQty;
        }
        syncHiddenInput();
        renderRows();
    };

    tableBody?.addEventListener('click', (event) => {
        const incTarget = event.target.closest('.btn-qty-inc');
        const decTarget = event.target.closest('.btn-qty-dec');
        if (incTarget) {
            updateQty(incTarget.dataset.productId, 1);
        } else if (decTarget) {
            updateQty(decTarget.dataset.productId, -1);
        }
    });

    const produkSelect = document.getElementById('produkBaru');
    const qtyInput = document.getElementById('qtyProdukBaru');
    const stockInfo = document.getElementById('infoStokProduk');

    produkSelect?.addEventListener('change', () => {
        if (!stockInfo) {
            return;
        }
        const selectedOption = produkSelect.options[produkSelect.selectedIndex];
        const stock = selectedOption?.dataset?.stock;
        if (stock === undefined || stock === '') {
            stockInfo.classList.add('d-none');
            stockInfo.textContent = '';
        } else {
            stockInfo.innerHTML = `<i class="fa-solid fa-circle-info me-1"></i> Stok tersedia: <strong>${stock}</strong>`;
            stockInfo.classList.remove('d-none');
        }
    });

    document.getElementById('formTambahItem')?.addEventListener('submit', (event) => {
        event.preventDefault();
        const productId = produkSelect?.value;
        let qty = Number(qtyInput?.value || 0);
        if (!productId || qty < 1) {
            return;
        }

        const limit = getLimitQty(productId);
        if (limit !== Infinity) {
            qty = Math.min(qty, limit);
        }

        const existingIndex = findIndex(productId);
        if (existingIndex === -1) {
            cartItems.push({ id: Number(productId), qty });
        } else {
            const combinedQty = cartItems[existingIndex].qty + qty;
            cartItems[existingIndex].qty = limit === Infinity ? combinedQty : Math.min(combinedQty, limit);
        }

        syncHiddenInput();
        renderRows();

        const modalElement = document.getElementById('modalTambahItem');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance?.hide();

        qtyInput.value = '1';
    });

    diskonInput?.addEventListener('input', recalcTotals);
    biayaInput?.addEventListener('input', recalcTotals);
    depresiasiInput?.addEventListener('input', recalcTotals);

    renderRows();
});
</script>
@endpush
@endsection