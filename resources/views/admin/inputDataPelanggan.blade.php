@extends('layouts.admin')

@section('title', isset($readOnly) && $readOnly ? 'Detail Data Pelanggan' : 'Edit Data Pelanggan')

@section('content')

{{-- ========================================== --}}
{{-- LAYOUT 1: MODE READ-ONLY (Detail & Riwayat)--}}
{{-- ========================================== --}}
@if(isset($readOnly) && $readOnly)
    <div class="row g-4">
        
        {{-- KOLOM KIRI: Informasi Pelanggan (Sidebar) --}}
        <div class="col-lg-4 col-xl-3">
            <div class="sticky-top" style="top: 1rem; z-index: 1;">
                @include('admin.partials.customer_detail_card')
            </div>
        </div>

        {{-- KOLOM KANAN: Statistik & Riwayat (Konten Utama) --}}
        <div class="col-lg-8 col-xl-9">
            <div class="d-flex flex-column gap-4">
                
                {{-- 1. Kartu Ringkasan Penjualan --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Riwayat Penjualan</h6>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-light text-secondary border">Lihat Semua</a>
                    </div>
                    
                    <div class="card-body">
                        {{-- Stats Row --}}
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Transaksi</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_transaksi['total_transaksi'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3 border text-center h-100">
                                    <small class="text-muted d-block mb-1">Total Item</small>
                                    <h4 class="fw-bold text-dark mb-0">{{ $ringkasan_transaksi['total_item'] ?? 0 }}</h4>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary text-center h-100">
                                    <small class="text-primary d-block mb-1">Total Belanja</small>
                                    <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($ringkasan_transaksi['total_nilai'] ?? 0, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive rounded border">
                            {{-- Masukkan tabel riwayat penjualan Anda di sini --}}
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                <thead class="bg-light text-secondary">
                                    <tr>
                                        <th class="ps-3 py-3">No</th>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Status</th>
                                        <th class="text-end pe-3 py-3">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop Data Riwayat --}}
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i><br>
                                            Data riwayat akan muncul di sini
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 2. Kartu Ringkasan Pembelian (Deal/Nego) --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <h6 class="fw-bold mb-0">Riwayat Pembelian / Deal</h6>
                        </div>
                    </div>
                    <div class="card-body">
                         {{-- Masukkan logika ringkasan pembelian Anda di sini --}}
                         <p class="text-muted text-center py-3 mb-0">Area Riwayat Pembelian</p>
                    </div>
                </div>

            </div>
        </div>
    </div>


{{-- ========================================== --}}
{{-- LAYOUT 2: MODE EDIT (Fokus Form Saja)      --}}
{{-- ========================================== --}}
@else
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            @include('admin.partials.customer_detail_card')
        </div>
    </div>
@endif

@endsection