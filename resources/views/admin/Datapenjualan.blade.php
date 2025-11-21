@extends('layouts.admin')

@section('title', 'Data Penjualan')

@push('page-actions')
<a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Penjualan</span>
</a>
@endpush

@section('content')

{{-- Session Alerts (Dari Main) --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Filter dan Pencarian (Visual: HEAD, Logic: Main) --}}
<form action="{{ route('admin.sales.index') }}" method="GET" id="searchForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">
            
            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text" 
                       name="search"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari ID Penjualan atau Nama Customer..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
            <div class="d-flex align-items-center gap-2 pe-2">
                
                {{-- Filter Kategori --}}
                <select name="kategori" 
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" 
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="" selected>Semua Kategori</option>
                    @foreach ($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ ($kategori_filter ?? '') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Sort --}}
                <select name="sort" 
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" 
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="total_tertinggi" {{ ($sort_filter ?? '') == 'total_tertinggi' ? 'selected' : '' }}>Total Tertinggi</option>
                </select>
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
                        <th style="width: 15%;">ID Transaksi</th>
                        <th style="width: 20%;">Customer</th>
                        <th>Tanggal</th>
                        <th style="width: 20%;">Item Terjual</th>
                        <th>Cabang</th>
                        <th>Total</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_penjualan as $index => $penjualan)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($data_penjualan->firstItem() ?? 0) + $index }}</td>

                        {{-- ID Transaksi --}}
                        <td>
                            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                #{{ $penjualan->id }}
                            </span>
                        </td>

                        {{-- Customer --}}
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $penjualan->customer->nama ?? 'Umum / N/A' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-muted small">
                            <span class="fw-medium text-dark">{{ $penjualan->created_at->format('d M Y') }}</span>
                            <br>
                            <span class="opacity-75">{{ $penjualan->created_at->format('H:i') }} WIB</span>
                        </td>

                        {{-- Item Terjual --}}
                        <td>
                            <span class="text-secondary small d-block" 
                                  title="{{ $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ') }}"
                                  style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                @php
                                    $itemNames = $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ');
                                    echo $itemNames ?: '-';
                                @endphp
                            </span>
                        </td>

                        {{-- Cabang --}}
                        <td class="text-dark fw-medium">
                            {{ $penjualan->perusahaan_cabang->nama ?? '-' }}
                        </td>

                        {{-- Total (Logic Kalkulasi dari Main) --}}
                        <td class="fw-bold text-dark">
                            @php
                                // Logic dari Main: Hitung manual jika harga_total null/0 (backup data lama)
                                $fallbackTotal = $penjualan->detail_penjualan->sum(function($d){
                                    return ($d->qty ?? 0) * ($d->harga_jual_satuan ?? 0);
                                });
                                $totalNominal = ($penjualan->harga_total ?? 0) > 0 ? $penjualan->harga_total : $fallbackTotal;
                            @endphp
                            Rp{{ number_format($totalNominal, 0, ',', '.') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Detail --}}
                                <a href="#" {{-- route('admin.sales.show', $penjualan->id) --}}
                                   class="btn-action btn-action-view"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.sales.edit', $penjualan->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('admin.sales.destroy', $penjualan->id) }}" method="POST" class="d-inline">
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
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-receipt fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data penjualan</h6>
                                <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.sales.create') }}">transaksi penjualan</a> baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_penjualan->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_penjualan->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus data penjualan ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush