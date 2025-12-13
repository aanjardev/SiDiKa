@extends('layouts.admin')

@section('title', 'Dashboard')

@push('page-actions')
    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3">
        <i class="fas fa-plus fa-fw"></i>
        <span>Penjualan</span>
    </a>
    @if(Route::has('admin.purchases.create'))
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3">
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </a>
    @else
        <button class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3" disabled>
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </button>
    @endif
@endpush

@section('content')

{{-- ======================================================= --}}
{{-- FILTER SECTION --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-xl">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label for="year" class="form-label mb-1 text-muted small fw-medium">Tahun</label>
                        <select name="year" id="year" class="form-select form-select-sm ">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label mb-1 text-muted small fw-medium">Bulan</label>
                        <select name="month" id="month" class="form-select form-select-sm ">
                            <option value="">Semua Bulan</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                                <option value="{{ $index + 1 }}" {{ $selectedMonth == ($index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label mb-1 text-muted small fw-medium">Cabang</label>
                        <select name="branch_id" id="branch_id" class="form-select form-select-sm ">
                            <option value="">Semua Cabang</option>
                            @foreach($allBranches as $branchOption)
                            <option value="{{ $branchOption->id }}" {{ (int)$selectedBranch === (int)$branchOption->id ? 'selected' : '' }}>
                                    {{ $branchOption->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- SUMMARY CARDS: Flexible Layout --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    @php
        $cardCount = $showBestBranch ? 4 : 3;
        $colClass = $cardCount == 4 ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-4';
    @endphp

    {{-- Card 1: Total Pendapatan --}}
    <div class="{{ $colClass }} mb-1">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #4E6BFF;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Pendapatan</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-line text-primary fa-lg"></i>
                    </div>
                </div>
                @if($growthPercentage != 0)
                    <div class="d-flex align-items-center gap-2">
                        @if($growthPercentage > 0)
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                                <i class="fas fa-arrow-up fa-xs me-1"></i>{{ abs($growthPercentage) }}%
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">
                                <i class="fas fa-arrow-down fa-xs me-1"></i>{{ abs($growthPercentage) }}%
                            </span>
                        @endif
                        <span class="text-muted small">vs periode sebelumnya</span>
                    </div>
                @else
                    <div class="text-muted small">Tidak ada data sebelumnya</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 2: Gross Profit --}}
    <div class="{{ $colClass }} mb-1">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #198754;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Gross Profit</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalLabaBersih, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-coins text-success fa-lg"></i>
                    </div>
                </div>
                @php
                    $hppPercent = $totalPendapatan > 0 ? round(($totalHPP / $totalPendapatan) * 100, 1) : 0;
                    $grossMargin = $totalPendapatan > 0 ? round(($totalLabaBersih / $totalPendapatan) * 100, 1) : 0;
                @endphp
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">HPP</span>
                        <span class="text-muted small">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-secondary border border-secondary-subtle rounded-pill">{{ $hppPercent }}%</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Gross Margin</span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill">{{ $grossMargin }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Transaksi --}}
    <div class="{{ $colClass }} mb-1">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #0dcaf0;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Transaksi</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-shopping-cart text-info fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary me-2 rounded-pill px-2 py-1">
                        Penjualan: {{ $dataTransaksiChart[0] ?? 0 }}
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                        Pembelian: {{ $dataTransaksiChart[1] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Cabang Terbaik -- Hanya tampil jika filter "Semua Cabang" --}}
    @if($showBestBranch)
        <div class="{{ $colClass }} mb-1">
            <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted small mb-1 fw-medium">Cabang Terbaik</p>
                            <h5 class="mb-1 fw-bold text-truncate" title="{{ $namaCabangTerbaik }}">
                                {{ $namaCabangTerbaik }}
                            </h5>
                            @if($labaCabangTerbaik > 0)
                                <h6 class="mb-0 text-muted">Rp {{ number_format($labaCabangTerbaik, 0, ',', '.') }}</h6>
                            @else
                                <h6 class="mb-0 text-muted">-</h6>
                            @endif
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-trophy text-warning fa-lg"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Gross Profit tertinggi</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ======================================================= --}}
{{-- CHARTS SECTION --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    {{-- Main Chart: Annual --}}
    <div class="col-xl-7 col-lg-12 mb-1">
        <div class="card shadow-sm border-0 h-100 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4 pb-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Grafik Annual ({{ $selectedYear }})</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div id="chartPendapatanBulanan" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>

   {{-- Donut Chart: Total Transaksi --}}
    <div class="col-xl-5 col-lg-12 mb-1">
        <div class="card shadow-sm border-0 h-100 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4 pb-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Transaksi</h5>
            </div>
            <div class="card-body pt-0" style="padding:  0px 50px !important;">
                <div class="row align-items-center h-100">
                    <div class="col-7 d-flex align-items-center justify-content-center">
                        <div id="chartTotalTransaksi" style="min-height: 250px; width: 100%;"></div>
                    </div>
                    <div class="col-5">
                        @php
                            $totalTransaksi = array_sum($dataTransaksiChart ?? []);
                            $penjualan = $dataTransaksiChart[0] ?? 0;
                            $pembelian = $dataTransaksiChart[1] ?? 0;
                            $penjualanPct = $totalTransaksi > 0 ? round(($penjualan / $totalTransaksi) * 100, 1) : 0;
                            $pembelianPct = $totalTransaksi > 0 ? round(($pembelian / $totalTransaksi) * 100, 1) : 0;
                        @endphp
                        <div class="transaksi-legend">
                            <div class="transaksi-pill">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="transaksi-dot bg-primary bg-opacity-10">
                                        <span class="transaksi-dot-inner"></span>
                                        <i class="fas fa-bag-shopping text-primary transaksi-dot-icon"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted small d-block">Penjualan</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <h4 class="fw-bold mb-0 text-primary">{{ number_format($penjualan) }}</h4>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">{{ $penjualanPct }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="transaksi-pill">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="transaksi-dot bg-success bg-opacity-10">
                                        <span class="transaksi-dot-inner"></span>
                                        <i class="fas fa-cart-arrow-down text-success transaksi-dot-icon"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted small d-block">Pembelian</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <h4 class="fw-bold mb-0 text-success">{{ number_format($pembelian) }}</h4>
                                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold">{{ $pembelianPct }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- WIDGET SECTION: Top Products --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-star text-warning me-2"></i>Top 5 Produk Terlaris
                </h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <div class="table-modern-container">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center ps-4" style="width: 5%;">No</th>
                                    <th>Produk</th>
                                    <th>Kode SKU</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center pe-4">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr class="table-row-hover clickable-row" data-detail-url="{{ route('admin.products.show', $product['id']) }}">
                                        <td class="text-center align-middle ps-4">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($product['gambar'] && $product['gambar']->url)
                                                    <img src="{{ $product['gambar']->url }}" alt="{{ $product['nama_produk'] }}"
                                                         class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.9rem;">
                                                        {{ $product['nama_produk'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-monospace text-secondary bg-light rounded px-2 py-1">{{ $product['kode_sku'] }}</span>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span class="fw-bold text-dark">Rp {{ number_format($product['harga_jual'], 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center align-middle pe-4">
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-2">
                                                {{ $product['total_qty'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada data produk</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- TRANSACTION PURCHASES TABLE --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-shopping-bag text-success me-2"></i>Transaksi Pembelian Terakhir
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($recentPurchases) && $recentPurchases->count() > 0)
                    <div class="table-modern-container">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center ps-4" style="width: 5%;">No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Customer</th>
                                    <th>Cabang</th>
                                    <th style="width: 25%;">Item Dibeli</th>
                                    <th class="text-end">Harga Deal</th>
                                    <th class="text-center">Status</th>
                                    <th class="pe-4">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $index => $purchase)
                                    <tr class="table-row-hover clickable-row" data-detail-url="{{ route('admin.purchases.show', $purchase['id']) }}">
                                        <td class="text-center align-middle ps-4">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-monospace text-primary fw-semibold bg-opacity-5 rounded px-2 py-1">
                                                {{ $purchase['kode_transaksi'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-dark fw-semibold d-block" style="font-size: 0.9rem;">
                                                {{ $purchase['customer'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="fw-medium text-secondary rounded px-2 py-1">
                                                {{ $purchase['cabang'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($purchase['items'] as $item)
                                                    <div class="rounded px-2 py-1">
                                                        <span class="text-dark">{{ $item['nama_produk'] }}</span>
                                                        <span class="text-muted small">(x{{ $item['qty'] }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-end align-middle">
                                            @if($purchase['harga_deal'] > 0)
                                                <span class="fw-bold text-dark">Rp {{ number_format($purchase['harga_deal'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($purchase['status'] == 'deal')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Deal</span>
                                            @elseif($purchase['status'] == 'tidak_deal')
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Tidak Deal</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Draft</span>
                                            @endif
                                        </td>
                                        <td class="align-middle pe-4">
                                            <span class="text-muted small">{{ $purchase['waktu'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada transaksi pembelian</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ======================================================= --}}
{{-- SCRIPTS: ApexCharts dengan format Rupiah --}}
{{-- ======================================================= --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Format Rupiah Helper
        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(value);
        }

        // 1. GRAFIK PENDAPATAN BULANAN
        var optionsPendapatan = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
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
                curve: 'smooth',
                width: 3,
                lineCap: 'round'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#4E6BFF', '#0dcaf0'],
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: @json($labelBulan),
                labels: {
                    style: {
                        colors: '#6c757d',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                axisBorder: {
                    show: true,
                    color: '#e9ecef',
                    height: 1
                },
                axisTicks: {
                    show: true,
                    color: '#e9ecef'
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        if (value >= 1000000) {
                            return (value / 1000000).toFixed(1) + " jt";
                        } else if (value >= 1000) {
                            return (value / 1000).toFixed(0) + " rb";
                        }
                        return value;
                    },
                    style: {
                        colors: '#6c757d',
                        fontSize: '12px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                axisBorder: {
                    show: true,
                    color: '#e9ecef'
                }
            },
            grid: {
                show: true,
                strokeDashArray: 3,
                borderColor: '#e9ecef',
                padding: {
                    top: 0,
                    right: 10,
                    bottom: 0,
                    left: 10
                }
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    width: 8,
                    height: 8,
                    radius: 4
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light',
                x: {
                    show: true,
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                        return w.globals.categoryLabels[dataPointIndex];
                    }
                },
                y: {
                    formatter: function (value) {
                        return formatRupiah(value);
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 4,
                colors: ['#fff'],
                strokeColors: ['#4E6BFF', '#0dcaf0'],
                strokeWidth: 2,
                hover: {
                    size: 6
                }
            }
        };

        var chartPendapatan = new ApexCharts(document.querySelector("#chartPendapatanBulanan"), optionsPendapatan);
        chartPendapatan.render();

        // 2. GRAFIK TOTAL TRANSAKSI
        var optionsTransaksi = {
            chart: {
                type: 'donut',
                height: 300
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
                            color: '#1f2937'
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#1f2937',
                            fontSize: '0.95rem',
                            fontWeight: 700
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

        // Auto-submit form on change
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            document.getElementById('year').addEventListener('change', function() {
                filterForm.submit();
            });

            document.getElementById('month').addEventListener('change', function() {
                filterForm.submit();
            });

            const branchSelect = document.getElementById('branch_id');
            if (branchSelect) {
                branchSelect.addEventListener('change', function() {
                    filterForm.submit();
                });
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Semua card menggunakan rounded-xl yang konsisten dengan overflow-hidden */
    .card {
        border-radius: 16px !important;
    }

    /* Container untuk tabel modern */
    .table-modern-container {
        overflow: hidden;
        border-radius: 0 0 16px 16px;
    }

    /* Tabel dengan styling modern */
    .table-modern {
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(0, 0, 0, 0.02);
        --bs-table-hover-bg: rgba(79, 107, 255, 0.04);
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }

    .table-modern thead tr {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table-modern thead th {
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    /* Hapus border radius pada header karena sudah dihandle oleh card */
    .table-modern thead th:first-child,
    .table-modern thead th:last-child {
        border-radius: 0;
    }

    .table-modern tbody td {
        border: none;
        padding: 1rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Hapus border radius pada baris terakhir karena sudah dihandle oleh container */
    .table-modern tbody tr:last-child td:first-child,
    .table-modern tbody tr:last-child td:last-child {
        border-radius: 0;
    }

    /* Transaksi legend styling */
    .transaksi-legend {
        display: grid;
        gap: 12px;
    }
    .transaksi-pill {
        padding: 12px 14px;
        border: 1px solid #e8eef6;
        background: linear-gradient(135deg, #f8fafc 0%, #f4f7fb 100%);
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .transaksi-dot {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .transaksi-dot-inner {
        display: block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
    }
    .transaksi-dot-icon {
        position: absolute;
        font-size: 0.85rem;
    }

    .table-row-hover:hover {
        background-color: var(--bs-table-hover-bg) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* Badge dengan rounded-pill */
    .badge {
        border-radius: 50px !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    /* Form select rounded */
    .form-select {
        border-radius: 7px !important;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        border-color: #4E6BFF;
        box-shadow: 0 0 0 0.25rem rgba(79, 107, 255, 0.25);
    }

    .apexcharts-donut-series path {
    stroke-width: 0;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.apexcharts-donut-series polygon {
    stroke-width: 2;
    stroke: #fff;
}

.apexcharts-donut .apexcharts-datalabels-group {
    transform: translateY(5px);
}

.apexcharts-donut .apexcharts-datalabel-value {
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Badge percentage */
.position-relative .badge {
    transform: translate(-50%, -50%);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-weight: 600;
    min-width: 30px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Chart container */
#chartTotalTransaksi {
    position: relative;
}

    /* Responsif untuk mobile */
    @media (max-width: 768px) {
        .card-header, .card-body {
            padding: 1.25rem !important;
        }

        .table-modern {
            font-size: 0.85rem;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 0.75rem 0.5rem;
        }

        .row.align-items-center > .col-6 {
            padding: 0.5rem !important;
        }
    }

    /* Animasi untuk hover effects */
    .table-row-hover,
    .badge,
    .btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush
