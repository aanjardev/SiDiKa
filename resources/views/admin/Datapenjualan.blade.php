@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Penjualan</span>
    </a>
@endpush

@section('content')

{{-- Filter dan Pencarian (Style: Satu Card Putih Clean) --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Ikon Filter & Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i> 
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent" 
                   placeholder="Cari Kode Penjualan (ID) atau Nama Customer..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown Filter --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            {{-- Filter Kategori (Dinamis) --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option value="" selected>Semua Kategori</option>
                @foreach ($semua_kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach
            </select>

            {{-- Filter Urutkan --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Terbaru</option>
                <option>Terlama</option>
                <option>Total Tertinggi</option>
            </select>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-hover">
            {{-- Header Abu-abu Terang --}}
            <thead class="bg-light"> 
                <tr class="text-dark fw-bold" style="border-bottom: 2px solid #eee;">
                    <th class="text-center py-3" style="width: 80px;">ID</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Tanggal</th>
                    <th class="py-3" style="width: 20%;">Item Terjual</th>
                    <th class="py-3">Cabang</th>
                    <th class="py-3">Total</th>
                    <th class="text-center py-3" style="width: 140px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($data_penjualan as $penjualan)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        {{-- ID Transaksi --}}
                        <td class="text-center fw-bold text-secondary">
                            #{{ $penjualan->id }}
                        </td>
                        
                        {{-- Customer --}}
                        <td>
                            <span class="fw-bold text-dark">{{ $penjualan->customer->nama ?? 'N/A' }}</span>
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-muted small">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-regular fa-calendar"></i>
                                {{ $penjualan->created_at->format('d M Y, H:i') }}
                            </div>
                        </td>

                        {{-- Item Terjual (Diproses PHP untuk truncate) --}}
                        <td class="text-muted small text-truncate" style="max-width: 200px;" 
                            title="{{ $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ') }}">
                             @php
                                // Ambil nama produk dari relasi detail_penjualan -> produk
                                $itemNames = $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ');
                                echo \Illuminate\Support\Str::limit($itemNames, 35, '...');
                            @endphp
                        </td>

                        {{-- Cabang --}}
                        <td class="text-dark">{{ $penjualan->perusahaan_cabang->nama ?? '-' }}</td>

                        {{-- Total Harga --}}
                        <td class="fw-bold text-dark">
                            Rp{{ number_format($penjualan->harga_total, 0, ',', '.') }}
                        </td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Detail --}}
                                <a href="#" {{-- route('admin.sales.show', $penjualan->id) --}}
                                   class="btn btn-sm btn-light text-dark border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="#" {{-- route('admin.sales.edit', $penjualan->id) --}}
                                   class="btn btn-sm btn-light text-primary border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Hapus --}}
                                <form action="#" {{-- route('admin.sales.destroy', $penjualan->id) --}} method="POST" class="d-inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-light text-danger border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                            style="width: 32px; height: 32px;" 
                                            title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Empty State --}}
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-receipt fa-2x text-secondary"></i>
                                </div>
                                <h5 class="text-muted fw-bold">Belum Ada Data Penjualan</h5>
                                <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.sales.create') }}">transaksi penjualan</a> baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if ($data_penjualan->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
            {{ $data_penjualan->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection