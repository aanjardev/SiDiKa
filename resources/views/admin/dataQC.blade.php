@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
    {{-- Halaman QC biasanya otomatis dari pembelian, jadi tombol tambah disembunyikan --}}
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
                   placeholder="Cari nama item atau Serial Number..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown Filter --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            {{-- Filter Kategori --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option value="" selected>Semua Kategori</option>
                {{-- Loop kategori di sini --}}
                {{-- @foreach ($semua_kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach --}}
            </select>

            {{-- Filter Urutkan --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Terbaru</option>
                <option>Terlama</option>
                <option>Progress Tertinggi</option>
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
                    <th class="text-center py-3" style="width: 100px;">ID Beli</th>
                    <th class="py-3" style="width: 25%;">Nama Item</th>
                    <th class="py-3">Serial Number</th>
                    <th class="py-3">SN Lensa</th>
                    <th class="py-3">Kategori</th>
                    <th class="py-3" style="width: 20%">Kelengkapan QC</th>
                    <th class="text-center py-3" style="width: 120px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($data_qc as $item)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        {{-- ID Pembelian --}}
                        <td class="text-center fw-bold text-secondary">
                            #{{ $item->pembelian_id }}
                        </td>
                        
                        {{-- Nama Item --}}
                        <td>
                            <span class="fw-bold text-dark">{{ $item->nama_item }}</span>
                        </td>

                        {{-- Serial Number Body --}}
                        <td class="text-muted small font-monospace">
                            {{ $item->serial_number ?? '-' }}
                        </td>

                        {{-- Serial Number Lensa --}}
                        <td class="text-muted small font-monospace">
                            {{ $item->serial_lens ?? '-' }}
                        </td>

                        {{-- Kategori --}}
                        <td class="text-dark">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>

                        {{-- Progress Bar Persentase --}}
                        <td>
                            @php $persen = round($item->persentase_lengkap); @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1 shadow-sm" style="height: 8px; border-radius: 10px; background-color: #e9ecef;">
                                    <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $persen }}%; border-radius: 10px;" 
                                         aria-valuenow="{{ $persen }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="small fw-bold text-muted" style="min-width: 35px;">{{ $persen }}%</span>
                            </div>
                        </td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <a href="{{ route('admin.qc.edit', $item->id) }}" 
                               class="btn btn-sm btn-warning text-dark border shadow-sm d-flex align-items-center justify-content-center rounded-3 fw-medium px-3 mx-auto"
                               style="height: 32px; width: fit-content;"
                               title="Proses QC">
                                <i class="fa-solid fa-clipboard-check me-2"></i> Proses
                            </a>
                        </td>
                    </tr>
                @empty
                    {{-- Empty State --}}
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-clipboard-list fa-2x text-secondary"></i>
                                </div>
                                <h5 class="text-muted fw-bold">Tidak Ada Item Menunggu QC</h5>
                                <p class="text-muted small mb-0">Semua item dari transaksi 'Deal' akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if ($data_qc->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
            {{ $data_qc->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection