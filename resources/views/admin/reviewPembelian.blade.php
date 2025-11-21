@extends('layouts.admin')

@section('title', 'Detail Pembelian #' . $pembelian->kode_transaksi)

@push('page-actions')
    {{-- Tombol Kembali ke Daftar --}}
    <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali</span>
    </a>

    {{-- Tombol Edit Transaksi Ini (Diubah ke Dark Gold Outline) --}}
    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" class="btn btn-outline-dark-gold btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-pen-to-square fa-fw"></i>
        <span>Edit Transaksi</span>
    </a>

    {{-- Tombol Cetak PDF (Tetap Biru Outline) --}}
    @if ($pembelian->status_pembelian == 'deal')
        <a href="{{ route('admin.purchases.print', $pembelian->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2" target="_blank">
            <i class="fas fa-print fa-fw"></i>
            <span>Cetak Nota</span>
        </a>
    @endif
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
                        <p class="fw-bold mb-0">{{ $pembelian->customer->nama ?? '-' }}</p>
                        <small class="text-muted">{{ $pembelian->customer->no_telp ?? '' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Cabang</label>
                        <p class="fw-bold mb-0">{{ $pembelian->perusahaan_cabang->nama ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Kasir</label>
                        <p class="fw-bold mb-0">{{ $pembelian->user->name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">Tanggal Transaksi</label>
                        <p class="fw-bold mb-0">{{ $pembelian->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: DAFTAR ITEM DRAFT --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Daftar Item ({{ $pembelian->item_pembelian_draft->count() }} item)</h5>

                {{-- Kita gunakan accordion untuk menampilkan semua 20+ data kondisi --}}
                <div class="accordion" id="accordionItemReview">
                    @forelse ($pembelian->item_pembelian_draft as $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed position-relative" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $item->id }}">

                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $item->nama_item }}</span>
                                        <span class="ms-2 badge bg-secondary">{{ $item->kategori->nama_kategori ?? '-' }}</span>
                                    </div>

                                    <div class="position-absolute d-flex justify-content-end"
                                        style="right: 50px; top: 50%; transform: translateY(-50%); width: 300px;">

                                        <span class="text-muted small text-start" style="width: 150px; font-size: 13px;">
                                            <b>SN: </b> {{ $item->serial_number ?? '-' }}
                                        </span>

                                        <span class="text-muted small text-start ms-2" style="width: 150px; font-size: 13px;">
                                            <b>SNL: </b> {{ $item->serial_lens ?? '-' }}
                                        </span>
                                    </div>

                                </button>
                            </h2>

                            <div id="collapse-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionItemReview">
                                <div class="accordion-body bg-light">
                                    <h6 class="fw-bold text-primary">Detail Kondisi</h6>
                                    <div class="row">
                                        @php
                                            $fields = [
                                                'Fisik' => $item->kondisi_fisik,
                                                'Baut' => $item->kondisi_baut,
                                                'Tutup USB' => $item->kondisi_tutup_usb,
                                                'Karet Grip' => $item->kondisi_grip,
                                                'Jamur Lensa' => $item->kondisi_jamur_lensa,
                                                'View Finder' => $item->kondisi_view_finder,
                                                'Mounting' => $item->kondisi_mounting,
                                                'Slot Memori' => $item->kondisi_slot_memori,
                                                'Jamur Sensor' => $item->kondisi_jamur_sensor,
                                                'LCD' => $item->kondisi_lcd,
                                                'Tombol' => $item->kondisi_tombol,
                                                'Zoom Lensa' => $item->kondisi_zoom_lensa,
                                                'AF Lensa' => $item->kondisi_af_lensa,
                                                'Diafragma Lensa' => $item->kondisi_diafragma_lensa,
                                                'Kalibrasi Fokus' => $item->kondisi_kalibrasi_fokus,
                                                'Flash' => $item->kondisi_flash,
                                                'Sound/Mic' => $item->kondisi_sound_mic,
                                                'Lain-lain' => $item->kondisi_lain_lain,
                                            ];
                                        @endphp

                                        @foreach ($fields as $label => $value)
                                            <div class="col-md-6 mb-1">
                                                <div class="d-flex small">
                                                    <div style="width: 140px; font-weight: 600;">{{ $label }}</div>
                                                    <div class="flex-grow-1">: &nbsp;{{ $value ?? '-' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <hr>

                                    <h6 class="fw-bold text-primary">Kelengkapan</h6>
                                    <p class="small">{{ $item->kelengkapan ?? 'Tidak ada kelengkapan khusus.' }}</p>
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
                    <h2 class="fw-bold text-success mb-0">
                        Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- CARD BARU: BAGIKAN LINK --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <h6 class="card-title fw-bold mb-2"><i class="fas fa-share-alt me-1"></i> Bagikan Tinjauan</h6>
                <p class="text-muted small mt-0 mb-2">Link ini dapat dibagikan (read-only).</p>
                <div class="input-group">
                    {{-- Ganti value di sini agar menggunakan fungsi route Laravel --}}
                    <input type="text" class="form-control form-control-sm" id="shareable-link" value="{{ route('admin.purchases.show', $pembelian->id) }}" readonly>
                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToClipboard()">
                        <i class="fas fa-copy me-1"></i> Salin
                    </button>
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
    .btn-outline-dark-gold {
        /* Warna teks dan border saat normal (Dark Gold) */
        color: #CC9900 !important; /* Warna Emas Pekat */
        border-color: #CC9900 !important;
    }

    /* Warna saat di-hover/focus */
    .btn-outline-dark-gold:hover,
    .btn-outline-dark-gold:focus,
    .btn-outline-dark-gold:active {
        color: #000 !important; /* Warna teks diubah menjadi hitam agar kontras */
        background-color: #D4A017 !important; /* Warna latar belakang saat hover (Sedikit lebih gelap dari normal) */
        border-color: #D4A017 !important;
    }
</style>
@endpush

@push('scripts')
{{-- ======================================================= --}}
{{-- SCRIPT UNTUK AUTO-COPY LINK & FUNGSI COPY TO CLIPBOARD --}}
{{-- ======================================================= --}}
<script>
    // Fungsi yang dipanggil oleh tombol "Salin"
    window.copyToClipboard = function() {
        const linkInput = document.getElementById('shareable-link');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);

        // Coba salin ke clipboard
        try {
            document.execCommand('copy');

            // Karena UI Feedback sudah ada di sistem Anda, kita tidak perlu alert() lagi.
            // Jika ada fungsi global untuk menampilkan notifikasi sukses (misalnya showSuccessToast), panggil di sini.

        } catch (err) {
            console.error('Gagal menyalin:', err);
            // Anda bisa menambahkan feedback gagal kustom di sini jika diperlukan.
        }
    }

    @if(session('auto_copy_link'))
    // Script ini dijalankan jika ada flash session 'auto_copy_link' (dari method store)
    document.addEventListener("DOMContentLoaded", function() {
        // Panggil fungsi salin yang kini tidak memiliki alert()
        window.copyToClipboard();
    });
    @endif
</script>
@endpush
