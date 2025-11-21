@extends('layouts.admin')

@section('title', 'Detail Penjualan')

@push('page-actions')
    <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    <a href="{{ route('admin.sales.edit', $penjualan->id) }}" class="btn btn-primary btn-sm">Edit</a>
@endpush

@section('content')
@php
    $fallbackTotal = $penjualan->detail_penjualan->sum(function($d){
        return ($d->qty ?? 0) * ($d->harga_jual_satuan ?? 0);
    });
    $totalNominal = ($penjualan->harga_total ?? 0) > 0 ? $penjualan->harga_total : $fallbackTotal;
@endphp

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Info Transaksi</h5>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">ID</dt>
                    <dd class="col-7 fw-semibold">#{{ $penjualan->id }}</dd>

                    <dt class="col-5 text-muted">Tanggal</dt>
                    <dd class="col-7">{{ $penjualan->created_at?->format('d M Y H:i') ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Customer</dt>
                    <dd class="col-7">{{ $penjualan->customer->nama ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Cabang</dt>
                    <dd class="col-7">{{ $penjualan->perusahaan_cabang->nama ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Kas</dt>
                    <dd class="col-7 text-uppercase">{{ $penjualan->kas ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Diskon</dt>
                    <dd class="col-7">Rp{{ number_format($penjualan->diskon ?? 0, 0, ',', '.') }}</dd>

                    <dt class="col-5 text-muted">Total</dt>
                    <dd class="col-7 fw-bold">Rp{{ number_format($totalNominal, 0, ',', '.') }}</dd>

                    <dt class="col-5 text-muted">Keterangan</dt>
                    <dd class="col-7">{{ $penjualan->keterangan ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Item Terjual</h5>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Harga</th>
                                <th class="text-end" style="width: 140px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penjualan->detail_penjualan as $detail)
                                @php
                                    $price = $detail->harga_jual_satuan ?? 0;
                                    $lineTotal = ($detail->qty ?? 0) * $price;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $detail->produk->nama_produk ?? 'Produk tidak tersedia' }}</div>
                                        <small class="text-muted">{{ $detail->produk->kode_sku ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">x{{ $detail->qty ?? 0 }}</td>
                                    <td class="text-end">Rp{{ number_format($price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($lineTotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada detail penjualan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
