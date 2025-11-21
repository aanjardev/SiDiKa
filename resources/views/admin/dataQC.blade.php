@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
   {{-- Halaman QC otomatis dari pembelian, tidak ada tombol tambah manual --}}
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
                placeholder="Cari nama item atau Serial Number..."
                style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option value="" selected>Semua Kategori</option>
                {{-- @foreach ($semua_kategori as $kat)
                    <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                @endforeach --}}
            </select>

            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Terbaru</option>
                <option>Terlama</option>
                <option>Progress Tertinggi</option>
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
                        <th style="width: 100px;">ID Beli</th>
                        <th style="width: 25%;">Nama Item</th>
                        <th>Serial Number</th>
                        <th>SN Lensa</th>
                        <th>Kategori</th>
                        <th style="width: 20%">Kelengkapan QC</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_qc as $index => $item)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($data_qc->firstItem() ?? 0) + $index }}</td>

                        {{-- ID Pembelian --}}
                        <td>
                            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                #{{ $item->pembelian_id }}
                            </span>
                        </td>

                        {{-- Nama Item (Tanpa Ikon) --}}
                        <td>
                            <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                {{ $item->nama_item }}
                            </span>
                        </td>

                        {{-- Serial Number --}}
                        <td class="text-secondary font-monospace small">
                            {{ $item->serial_number ?? '-' }}
                        </td>

                        {{-- SN Lensa --}}
                        <td class="text-secondary font-monospace small">
                            {{ $item->serial_lens ?? '-' }}
                        </td>

                        {{-- Kategori --}}
                        <td class="text-dark">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>

                        {{-- Progress QC --}}
                        <td>
                            @php $persen = round($item->persentase_lengkap); @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1 shadow-sm" style="height: 6px; border-radius: 10px; background-color: #f0f2f5;">
                                    <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $persen }}%; border-radius: 10px;" 
                                         aria-valuenow="{{ $persen }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="small fw-bold text-muted" style="font-size: 0.8rem; min-width: 35px; text-align: right;">
                                    {{ $persen }}%
                                </span>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center">
                            <a href="{{ route('admin.qc.edit', $item->id) }}" 
                               class="btn btn-sm btn-primary shadow-sm px-3 rounded-3 fw-medium"
                               style="font-size: 0.85rem;"
                               title="Proses QC">
                                <i class="fa-solid fa-clipboard-check me-1"></i> Proses
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-clipboard-list fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Tidak Ada Item Menunggu QC</h6>
                                <p class="small text-muted">Semua item dari transaksi 'Deal' akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_qc->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_qc->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection