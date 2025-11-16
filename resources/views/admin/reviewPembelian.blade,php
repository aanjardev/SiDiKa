@extends('layouts.admin')

@section('title', 'Tinjauan Pembelian #' . $pembelian->id)

@push('page-actions')
    {{-- Tombol Edit untuk kembali ke form --}}
    <a href="#" {{-- Nanti: route('admin.purchases.edit', $pembelian->id) --}} class="btn btn-warning btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-pen-to-square fa-fw"></i>
        <span>Edit Transaksi Ini</span>
    </a>
    {{-- Tombol Cetak PDF --}}
    <a href="#" class="btn btn-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-print fa-fw"></i>
        <span>Cetak</span>
    </a>
@endpush

@section('content')

{{--
  Ini adalah halaman read-only untuk di-share.
  Mirip dengan form, tapi semua data tidak bisa diedit.
--}}

<div class="row">
    {{-- KOLOM KIRI (70%): Detail --}}
    <div class="col-lg-8">

        {{-- CARD 1: INFORMASI TRANSAKSI --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-4">Informasi Transaksi</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Customer</label>
                        <p class="fw-bold mb-0">{{ $pembelian->customer->nama ?? 'N/A' }}</p>
                        <small class="text-muted">{{ $pembelian->customer->no_telp ?? '' }}</p></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Lokasi Transaksi (Cabang)</label>
                        <p class="fw-bold mb-0">{{ $pembelian->perusahaan_cabang->nama ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Petugas (Karyawan)</label>
                        <p class="fw-bold mb-0">{{ $pembelian->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Dibuat</label>
                        <p class="fw-bold mb-0">{{ $pembelian->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: DAFTAR ITEM DRAFT --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Item yang Ditawarkan ({{ $pembelian->item_pembelian_draft->count() }} item)</h5>

                {{-- Kita gunakan accordion untuk menampilkan semua 20+ data kondisi --}}
                <div class="accordion" id="accordionItemReview">
                    @forelse ($pembelian->item_pembelian_draft as $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->id }}">
                                    <span class="fw-bold">{{ $item->nama_item }}</span>
                                    <span class="ms-2 badge bg-secondary">{{ $item->kategori->nama_kategori ?? 'N/A' }}</span>
                                    <span class="ms-auto me-2 text-muted small">SN: {{ $item->serial_number ?? 'N/A' }}</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionItemReview">
                                <div class="accordion-body bg-light">
                                    <h6 class="fw-bold text-primary">Detail Kondisi</h6>
                                    <div class="row">
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Fisik:</strong> {{ $item->kondisi_fisik ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Baut:</strong> {{ $item->kondisi_baut ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Tutup USB:</strong> {{ $item->kondisi_tutup_usb ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Karet Grip:</strong> {{ $item->kondisi_grip ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Jamur Lensa:</strong> {{ $item->kondisi_jamur_lensa ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>View Finder:</strong> {{ $item->kondisi_view_finder ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Mounting:</strong> {{ $item->kondisi_mounting ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Slot Memori:</strong> {{ $item->kondisi_slot_memori ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Jamur Sensor:</strong> {{ $item->kondisi_jamur_sensor ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>LCD:</strong> {{ $item->kondisi_lcd ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Tombol:</strong> {{ $item->kondisi_tombol ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Zoom Lensa:</strong> {{ $item->kondisi_zoom_lensa ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>AF Lensa:</strong> {{ $item->kondisi_af_lensa ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Diafragma Lensa:</strong> {{ $item->kondisi_diafragma_lensa ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Kalibrasi Fokus:</strong> {{ $item->kondisi_kalibrasi_fokus ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Flash:</strong> {{ $item->kondisi_flash ?? '-' }}</p></div>
                                        <div class="col-md-6"><p class="mb-1 small"><strong>Sound/Mic:</strong> {{ $item->kondisi_sound_mic ?? '-' }}</p></div>
                                        <div class="col-md-12"><p class="mb-1 small"><strong>Lain-lain:</strong> {{ $item->kondisi_lain_lain ?? '-' }}</p></div>
                                    </div>
                                    <hr>
                                    <h6 class="fw-bold text-primary">Kelengkapan</h6>
                                    <p class="small">{{ $item->kelengkapan_awal ?? 'Tidak ada kelengkapan khusus.' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Tidak ada item dalam transaksi draft ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN (30%): Harga & Status --}}
    <div class="col-lg-4">

        <div class="card shadow-sm border-0 mb-4 position-sticky" style="top: 20px;">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Harga & Status</h5>

                {{-- Status --}}
                <div class="mb-3">
                    <label class="form-label text-muted small">Status Transaksi</label>
                    <p>
                        @if($pembelian->status_pembelian == 'deal')
                            <span class="badge fs-6 bg-success-subtle text-success-emphasis">DEAL</span>
                        @elseif($pembelian->status_pembelian == 'tidak_deal')
                            <span class="badge fs-6 bg-danger-subtle text-danger-emphasis">TIDAK DEAL</span>
                        @else
                            <span class="badge fs-6 bg-secondary-subtle text-secondary-emphasis">DRAFT</span>
                        @endif
                    </p>
                </div>

                <hr>

                {{-- Harga Tawaran --}}
                <div class="mb-3">
                    <label class="form-label text-muted small">Tawaran Customer (Total)</label>
                    <h4 class="fw-normal">Rp {{ number_format($pembelian->harga_tawaran_customer, 0, ',', '.') }}</h4>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Tawaran Toko (Total)</label>
                    <h4 class="fw-normal">Rp {{ number_format($pembelian->harga_tawaran_toko, 0, ',', '.') }}</h4>
                </div>

                {{-- Harga Final (Paling Menonjol) --}}
                <div class="mb-3 p-3 rounded" style="background-color: var(--bs-success-bg-subtle);">
                    <label class="form-label fw-bold text-success small text-uppercase">Harga Deal (Final)</label>
                    <h2 class="fw-bold text-success mb-0">Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}</h2>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        color: var(--bs-primary);
        background-color: var(--bs-primary-bg-subtle);
    }
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(78, 107, 255, 0.25);
    }
</style>
@endpush

@push('scripts')
{{-- ======================================================= --}}
{{-- SCRIPT UNTUK AUTO-COPY LINK (SESUAI PERMINTAAN) --}}
{{-- ======================================================= --}}
@if(session('auto_copy_link'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Buat textarea sementara
        const el = document.createElement('textarea');
        el.value = window.location.href; // Ambil URL halaman ini
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy'); // Salin
        document.body.removeChild(el); // Hapus

        // Beri notifikasi (opsional, tapi bagus)
        alert('Link tinjauan berhasil disalin ke clipboard!');
    });
</script>
@endif
@endpush
