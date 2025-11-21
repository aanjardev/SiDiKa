@extends('layouts.admin')

@section('title', 'Data Pembelian')

@push('page-actions')
<a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Pembelian</span>
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
                placeholder="Cari ID Pembelian atau Nama Customer..."
                style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            {{-- Filter Status --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option value="" selected>Semua Status</option>
                <option value="draft">Draft</option>
                <option value="deal">Deal</option>
                <option value="tidak_deal">Tidak Deal</option>
            </select>

            {{-- Filter Urutkan --}}
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Terbaru</option>
                <option>Terlama</option>
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
                        <th style="width: 15%;">ID Transaksi</th>
                        <th style="width: 20%;">Customer</th>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th style="width: 15%;">Item Dibeli</th>
                        <th class="text-center">Status</th>
                        <th>Harga Deal</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_pembelian as $index => $pembelian)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ ($data_pembelian->firstItem() ?? 0) + $index }}</td>

                        {{-- ID Transaksi --}}
                        <td>
                            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                #{{ $pembelian->id }}
                            </span>
                        </td>

                        {{-- Customer --}}
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $pembelian->customer->nama ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Tanggal --}}
                        <td class="text-muted small">
                            <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
                            <br>
                            <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
                        </td>

                        {{-- Cabang --}}
                        <td class="text-dark fw-medium">
                            {{ $pembelian->perusahaan_cabang->nama ?? '-' }}
                        </td>

                        {{-- Item Dibeli --}}
                        <td>
                            <span class="text-secondary small d-block" 
                                  title="{{ $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ') }}"
                                  style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                @php
                                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                                    echo $itemNames ?: '-';
                                @endphp
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @if($pembelian->status_pembelian == 'deal')
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                    Deal
                                </span>
                            @elseif($pembelian->status_pembelian == 'tidak_deal')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">
                                    Batal
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">
                                    Draft
                                </span>
                            @endif
                        </td>

                        {{-- Harga Deal --}}
                        <td class="fw-bold text-dark">
                            Rp{{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Detail --}}
                                <a href="#" {{-- route('admin.purchases.show', $pembelian->id) --}}
                                   class="btn-action btn-action-view"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="#" {{-- route('admin.purchases.edit', $pembelian->id) --}}
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Hapus --}}
                                <form action="#" {{-- route('admin.purchases.destroy', $pembelian->id) --}} method="POST" class="d-inline">
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
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data pembelian</h6>
                            </div>
                            <div class="d-flex flex-column align-items-center">
                                <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.purchases.create') }}">transaksi pembelian</a> baru.</p>
                            </div >
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_pembelian->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_pembelian->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        if (confirm('Apakah Anda yakin ingin menghapus data pembelian ini?')) {
            button.form.submit();
        }
    }
</script>
@endpush