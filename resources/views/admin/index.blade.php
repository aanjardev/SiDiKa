@extends('layouts.admin')

@section('title', 'Dashboard')

@push('page-actions')
    <a href="{{ route('admin.sales.index') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Penjualan</span>
</a>
    <button class="btn btn-success btn-sm d-flex align-items-center gap-2" style="background-color: #198754; border-color: #198754;">
        <i class="fas fa-plus fa-fw"></i>
        <span>Pembelian</span>
    </button>
@endpush


@section('content')

{{-- Baris Pertama: Card Ringkasan Pendapatan --}}
<div class="row">
    {{-- Card 1: Pendapatan Utama (dengan HPP & Laba) --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                {{-- Header Card --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Total Pendapatan</p>

                    {{-- PERBAIKAN 2: Dua Dropdown (Bulan & Tahun) --}}
                    <div class="d-flex gap-1">
                        {{-- Dropdown Bulan --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light btn-link text-decoration-none dropdown-toggle p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem;">
                                Semua Bulan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="max-height: 200px; overflow-y: auto;">
                                <li><a class="dropdown-item" href="#">Semua Bulan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Januari</a></li>
                                <li><a class="dropdown-item" href="#">Februari</a></li>
                                <li><a class="dropdown-item" href="#">Maret</a></li>
                                <li><a class="dropdown-item" href="#">April</a></li>
                                <li><a class="dropdown-item" href="#">Mei</a></li>
                                <li><a class="dropdown-item" href="#">Juni</a></li>
                                <li><a class="dropdown-item" href="#">Juli</a></li>
                                <li><a class="dropdown-item" href="#">Agustus</a></li>
                                <li><a class="dropdown-item" href="#">September</a></li>
                                <li><a class="dropdown-item" href="#">Oktober</a></li>
                                <li><a class="dropdown-item" href="#">November</a></li>
                                <li><a class="dropdown-item" href="#">Desember</a></li>
                            </ul>
                        </div>
                        {{-- Dropdown Tahun --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light btn-link text-decoration-none dropdown-toggle p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.75rem;">
                                2025
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" style="max-height: 200px; overflow-y: auto;">
                                <li><a class="dropdown-item" href="#">2025</a></li>
                                <li><a class="dropdown-item" href="#">2024</a></li>
                                <li><a class="dropdown-item" href="#">2023</a></li>
                                <li><a class="dropdown-item" href="#">2022</a></li>
                                <li><a class="dropdown-item" href="#">2021</a></li>
                                <li><a class="dropdown-item" href="#">2020</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Total Pendapatan --}}
                <h3 class="mb-3 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>

                <hr class="my-2">

                {{-- Info HPP --}}
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">HPP</span>
                    <div>
                        <span class="fw-medium small me-2">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-dark small">{{ round($persentaseHPP) }}%</span>
                    </div>
                </div>

                {{-- Info Laba Kotor --}}
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Laba Kotor</span>
                    <div>
                        <span class="fw-medium small me-2">Rp {{ number_format($totalLabaKotor, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-dark small">{{ round($persentaseLabaKotor) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Cabang (Dibuat dinamis menggunakan @foreach) --}}
    @foreach ($dataCabang as $cabang)
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                {{-- Header Card (Nama Cabang STATIS) --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Pendapatan Cabang</p>
                    <span class="badge bg-info-subtle text-info-emphasis fw-semibold">{{ $cabang['namaCabang'] }}</span>
                </div>
                {{-- Total Pendapatan Cabang --}}
                <h3 class="mb-3 fw-bold">Rp {{ number_format($cabang['pendapatanCabang'], 0, ',', '.') }}</h3>
                <hr class="my-2">
                {{-- Info HPP Cabang (BARU) --}}
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">HPP</span>
                    <span class="fw-medium small">Rp {{ number_format($cabang['hppCabang'], 0, ',', '.') }}</span>
                </div>
                {{-- Info Laba Bersih Cabang (BARU) --}}
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Laba Bersih</span>
                    <span class="fw-medium small">Rp {{ number_format($cabang['labaBersihCabang'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Baris Kedua: Grafik (Chart) --}}
<div class="row">
    {{-- Card Grafik Pendapatan Bulanan (Style "Gross Sales") --}}
    <div class="col-md-12 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            {{-- PERBAIKAN 3: Ganti 'card-header' dengan 'p-3 border-bottom' --}}
            <div class="flex-wrap d-flex justify-content-between align-items-center p-3 border-bottom">
                <div class="header-title">
                    <h4 class="card-title fw-bold mb-0">Grafik Annual</h4>
                </div>
                <div class="d-flex align-items-center align-self-center">
                    {{-- Legenda 1 --}}
                    <div class="d-flex align-items-center text-primary">
                        <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"></circle></svg>
                        <div class="ms-2">
                            <span class="text-muted">Pendapatan</span>
                        </div>
                    </div>
                    {{-- Legenda 2 --}}
                    <div class="d-flex align-items-center ms-3 text-info">
                        <svg class="icon-12" xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="8" fill="currentColor"></circle></svg>
                        <div class="ms-2">
                            <span class="text-muted">HPP</span>
                        </div>
                    </div>
                </div>
                {{-- Dropdown Filter --}}
                <div class="dropdown">
                    <a href="#" class="text-muted dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        2025
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="max-height: 200px; overflow-y: auto;">
                        <li><a class="dropdown-item active" href="#">2025</a></li>
                        <li><a class="dropdown-item" href="#">2024</a></li>
                        <li><a class="dropdown-item" href="#">2023</a></li>
                        <li><a class="dropdown-item" href="#">2022</a></li>
                        <li><a class="dropdown-item" href="#">2021</a></li>
                        <li><a class="dropdown-item" href="#">2020</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                {{-- WADAH GRAFIK PENDAPATAN --}}
                <div id="chartPendapatanBulanan" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>
    {{-- Card Grafik Total Transaksi (Style "Earnings") --}}
    <div class="col-md-12 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            {{-- PERBAIKAN 4: Ganti 'card-header' dengan 'p-3 border-bottom' --}}
            <div class="flex-wrap d-flex justify-content-between p-3 border-bottom">
                <div class="header-title">
                    <h4 class="card-title fw-bold mb-0">Total Transaksi</h4>
                </div>
                <div class="dropdown">
                    <a href="#" class="text-muted dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                        Oktober 2025
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="max-height: 200px; overflow-y: auto;">
                        <li><a class="dropdown-item" href="#">Januari 2025</a></li>
                        <li><a class="dropdown-item" href="#">Februari 2025</a></li>
                        <li><a class="dropdown-item" href="#">Maret 2025</a></li>
                        <li><a class="dropdown-item" href="#">April 2025</a></li>
                        <li><a class="dropdown-item" href="#">Mei 2025</a></li>
                        <li><a class="dropdown-item" href="#">Juni 2025</a></li>
                        <li><a class="dropdown-item" href="#">Juli 2025</a></li>
                        <li><a class="dropdown-item" href="#">Agustus 2025</a></li>
                        <li><a class="dropdown-item" href="#">September 2025</a></li>
                        <li><a class="dropdown-item active" href="#">Oktober 2025</a></li>
                        <li><a class="dropdown-item" href="#">November 2025</a></li>
                        <li><a class="dropdown-item" href="#">Desember 2025</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="flex-wrap d-flex align-items-center justify-content-between">
                    {{-- WADAH GRAFIK TRANSAKSI --}}
                    <div id="chartTotalTransaksi" class="col-md-7 col-lg-7" style="min-height: 200px;"></div>

                    {{-- Legenda Kustom Sebelah Kanan --}}
                    <div class="d-grid gap-3 col-md-5 col-lg-5">
                        <div class="d-flex align-items-start">
                            <svg class="mt-1 icon-14" xmlns="http://www.w3.org/2000/svg" width="14" viewBox="0 0 24 24" fill="#4E6BFF"><circle cx="12" cy="12" r="8" fill="#4E6BFF"></circle></svg>
                            <div class="ms-3">
                                <span class="text-muted small">Penjualan</span>
                                <h6 class="fw-bold mb-0">{{ $dataTransaksiChart[0] ?? 0 }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <svg class="mt-1 icon-14" xmlns="http://www.w3.org/2000/svg" width="14" viewBox="0 0 24 24" fill="#198754"><circle cx="12" cy="12" r="8" fill="#198754"></circle></svg>
                            <div class="ms-3">
                                <span class="text-muted small">Pembelian</span>
                                <h6 class="fw-bold mb-0">{{ $dataTransaksiChart[1] ?? 0 }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- ======================================================= --}}
{{-- PERBAIKAN 5: Membersihkan SEMUA karakter aneh dari JavaScript --}}
{{-- ======================================================= --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. GRAFIK PENDAPATAN BULANAN
        var optionsPendapatan = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                sparkline: { enabled: false },
            },
            series: [
                {
                    name: 'Pendapatan',
                    data: @json($dataPendapatanChart)
                },
                {
                    name: 'HPP',
                    data: @json($dataHppChart)
                }
            ],
            colors: ['#4E6BFF', '#0dcaf0'],
            stroke: {
                curve: 'straight',
                width: 3,
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },
            xaxis: {
                categories: @json($labelBulan),
                labels: { show: true },
                axisBorder: { show: true },
                axisTicks: { show: true },
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return (value / 1000000) + " jt";
                    }
                }
            },
            grid: {
                show: true,
                strokeDashArray: 3,
                borderColor: '#e9ecef'
            },
            legend: { show: false },
            tooltip: {
                x: { show: true },
                y: {
                    formatter: function (value) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (value) {
                    var val = Math.round(value / 1000000);
                    return val + ' Jt';
                },

                style: {
                    fontSize: '9px',
                    fontWeight: 500,
                    colors: ["#333"]
                },

                offsetY: -5,

                background: {
                    enabled: false,
                }
            }
        };

        var chartPendapatan = new ApexCharts(document.querySelector("#chartPendapatanBulanan"), optionsPendapatan);
        chartPendapatan.render();


        // 2. GRAFIK TOTAL TRANSAKSI
        var optionsTransaksi = {
            chart: {
                type: 'donut',
                height: 200
            },
            series: @json($dataTransaksiChart),
            labels: ['Penjualan', 'Pembelian'],
            colors: ['#4E6BFF', '#198754'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            value: {
                                show: true,
                                fontSize: '1.25rem',
                                fontWeight: 'bold',
                                color: '#2F353F',
                                formatter: function (val) {
                                    return val
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#6c757d',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                }
                            }
                        }
                    }
                }
            },
            legend: { show: false },
            dataLabels: { enabled: false },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + " Transaksi";
                    }
                }
            }
        };

        var chartTransaksi = new ApexCharts(document.querySelector("#chartTotalTransaksi"), optionsTransaksi);
        chartTransaksi.render();

    });
</script>
@endpush
