@extends('layouts.admin')

@section('title', 'Transaksi Penjualan')

@push('page-actions')
    {{-- Halaman form tidak perlu tombol aksi di header --}}
@endpush

@section('content')

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <h5 class="alert-heading">Ada Kesalahan Input!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
        {{-- KOLOM KIRI (70%): Informasi + Item --}}
        <div class="col-lg-8">

            {{-- CARD 1: INFORMASI TRANSAKSI (Customer & Cabang) --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Informasi Transaksi</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="customer_id">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="" disabled {{ old('customer_id', $penjualan->customer_id ?? '') ? '' : 'selected' }}>Pilih customer...</option>
                                @foreach($semua_customer as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ (string) old('customer_id', $penjualan->customer_id ?? '') === (string) $customer->id ? 'selected' : '' }}
                                >
                                    {{ $customer->nama }} ({{ $customer->no_telp }})
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">
                                Jika customer baru, <a href="#" data-bs-toggle="modal" data-bs-target="#modalTambahCustomer">klik di sini untuk menambahkannya</a>.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="perusahaan_cabang_id">Lokasi Transaksi (Cabang)</label>
                            <select class="form-select" id="perusahaan_cabang_id" name="perusahaan_cabang_id" required>
                                @foreach ($semua_cabang as $branch)
                                    <option value="{{ $branch->id }}" {{ (string) old('perusahaan_cabang_id', $penjualan->perusahaan_cabang_id ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: DAFTAR ITEM YANG DIJUAL --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold mb-0">Item yang Dijual</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahItem">
                            <i class="fa-solid fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-product mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center" style="width: 80px;">Qty</th>
                                    <th class="text-end" style="width: 140px;">Harga</th>
                                    <th class="text-end" style="width: 160px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tableItemsBody">
                                @forelse ($items as $item)
                                    <tr data-product-id="{{ $item['product']->id ?? '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $item['product']->nama_produk ?? 'Produk tidak tersedia' }}</div>
                                            <small class="text-muted">{{ $item['product']->kode_sku ?? '-' }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if(isset($item['product']->id))
                                                <div class="d-inline-flex align-items-center gap-2 qty-control" data-product-id="{{ $item['product']->id }}">
                                                    <button type="button" class="btn btn-light btn-sm border btn-qty-dec" data-product-id="{{ $item['product']->id }}">-</button>
                                                    <span class="fw-semibold qty-value" data-product-id="{{ $item['product']->id }}">{{ $item['qty'] }}</span>
                                                    <button type="button" class="btn btn-light btn-sm border btn-qty-inc" data-product-id="{{ $item['product']->id }}">+</button>
                                                </div>
                                            @else
                                                x{{ $item['qty'] }}
                                            @endif
                                        </td>
                                        <td class="text-end">Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                                        <td class="text-end">Rp{{ number_format($item['line_total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada item yang dipilih.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (30%): Metode & Biaya --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">Pembayaran</h5>

                    <div class="mb-3">
                        <label class="form-label">Metode Kas</label>
                        @php
                            $kasValue = old('kas', $penjualan->kas ?? 'cash');
                        @endphp
                        <select name="kas" class="form-select">
                            <option value="cash" {{ $kasValue === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ $kasValue === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Diskon (Rp)</label>
                            <input type="number" name="diskon" class="form-control" value="{{ old('diskon', $penjualan->diskon ?? 0) }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Biaya Tambahan (Rp)</label>
                            <input type="number" name="biaya_tambahan" class="form-control" value="{{ old('biaya_tambahan', $biaya_tambahan_awal ?? 0) }}" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Depresiasi (Rp)</label>
                        <input type="number" name="depresiasi" class="form-control" value="{{ old('depresiasi', $depresiasi_awal ?? 0) }}" min="0">
                    </div>

                    <div class="mb-3 small text-muted" id="breakdownSection">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal Produk</span>
                            <span id="lineSubtotal">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger" id="lineDiskonRow" style="display: none;">
                            <span>Diskon</span>
                            <span id="lineDiskon">-Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger" id="lineDepresiasiRow" style="display: none;">
                            <span>Depresiasi</span>
                            <span id="lineDepresiasi">-Rp0</span>
                        </div>
                        <div class="d-flex justify-content-between text-success" id="lineBiayaRow" style="display: none;">
                            <span>Biaya Tambahan</span>
                            <span id="lineBiaya">Rp0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-control" placeholder="Catatan tambahan">{{ old('keterangan', $penjualan->keterangan ?? '') }}</textarea>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-semibold">Total</span>
                        <span class="fw-bold" id="subtotalValue">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Penjualan' : 'Simpan Penjualan' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Tambah Item --}}
<div class="modal fade" id="modalTambahItem" tabindex="-1" aria-labelledby="modalTambahItemLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahItem">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahItemLabel">Tambah Item Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="produkBaru">Produk</label>
                        <select class="form-select" id="produkBaru">
                            <option value="" selected disabled>Pilih produk...</option>
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
                        <label class="form-label" for="qtyProdukBaru">Jumlah</label>
                        <input type="number" class="form-control" id="qtyProdukBaru" value="1" min="1">
                    </div>
                    <div class="alert alert-warning d-none" id="infoStokProduk"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
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
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada item yang dipilih.</td></tr>';
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
                <td>
                    <div class="fw-semibold">${product.nama_produk}</div>
                    <small class="text-muted">${product.kode_sku ?? '-'}</small>
                </td>
                <td class="text-center">
                    <div class="d-inline-flex align-items-center gap-2 qty-control" data-product-id="${item.id}">
                        <button type="button" class="btn btn-light btn-sm border btn-qty-dec" data-product-id="${item.id}">-</button>
                        <span class="fw-semibold qty-value" data-product-id="${item.id}">${item.qty}</span>
                        <button type="button" class="btn btn-light btn-sm border btn-qty-inc" data-product-id="${item.id}">+</button>
                    </div>
                </td>
                <td class="text-end">${formatRupiah(price)}</td>
                <td class="text-end">${formatRupiah(item.qty * price)}</td>
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
            stockInfo.textContent = `Stok tersedia: ${stock}`;
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
{{-- Modal Tambah Customer (reuse dari inputPembelian) --}}
<div class="modal fade" id="modalTambahCustomer" tabindex="-1" aria-labelledby="modalTambahCustomerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahCustomer">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCustomerLabel">Tambah Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="customer_nama_modal">Nama</label>
                        <input type="text" class="form-control" id="customer_nama_modal" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_no_telp_modal">No. Telepon</label>
                        <input type="text" class="form-control" id="customer_no_telp_modal" name="no_telp" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_alamat_modal">Alamat</label>
                        <input type="text" class="form-control" id="customer_alamat_modal" name="alamat">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_jenis_kelamin_modal">Jenis Kelamin</label>
                        <select class="form-select" id="customer_jenis_kelamin_modal" name="jenis_kelamin" required>
                            <option value="" selected disabled>Pilih jenis kelamin...</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_referensi_modal">Referensi</label>
                        <input type="text" class="form-control" id="customer_referensi_modal" name="referensi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="customer_keterangan_modal">Keterangan</label>
                        <textarea class="form-control" id="customer_keterangan_modal" name="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanCustomer">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnSimpanCustomer = document.getElementById('btnSimpanCustomer');
    const formTambahCustomer = document.getElementById('formTambahCustomer');

    btnSimpanCustomer?.addEventListener('click', function() {
        const nama = document.getElementById('customer_nama_modal').value.trim();
        const no_telp = document.getElementById('customer_no_telp_modal').value.trim();
        if (!nama || !no_telp) { alert('Nama dan No. Telepon wajib diisi.'); return; }

        const formData = new FormData(formTambahCustomer);
        const data = Object.fromEntries(formData.entries());

        btnSimpanCustomer.disabled = true;
        btnSimpanCustomer.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        fetch("{{ route('admin.customers.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(result => {
            if (result.success && result.customer) {
                const customerSelect = document.getElementById('customer_id');
                const newOption = new Option(`${result.customer.nama} (${result.customer.no_telp})`, result.customer.id, true, true);
                customerSelect.appendChild(newOption);
                formTambahCustomer.reset();
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahCustomer'));
                modal?.hide();
            } else {
                alert('Gagal menyimpan customer: ' + (result.message || 'Error tidak diketahui.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMsg = 'Gagal menyimpan data.';
            if (error.errors) {
                errorMsg = Object.values(error.errors)[0][0];
            } else if (error.message) {
                errorMsg = error.message;
            }
            alert(errorMsg);
        })
        .finally(() => {
            btnSimpanCustomer.disabled = false;
            btnSimpanCustomer.innerHTML = 'Simpan Customer';
        });
    });
});
</script>
@endpush
@endsection
