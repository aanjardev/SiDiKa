@extends('layouts.admin')

@section('title', 'Dashboard')

@push('page-actions')
    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Penjualan</span>
    </a>
    @if(Route::has('admin.purchases.create'))
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2" style="background-color: #198754; border-color: #198754;">
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </a>
    @else
        <button class="btn btn-success btn-sm d-flex align-items-center gap-2" style="background-color: #198754; border-color: #198754;" disabled>
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </button>
    @endif
@endpush

@section('content')

{{-- ======================================================= --}}
{{-- FILTER SECTION: Form dengan auto-submit --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <label for="year" class="form-label mb-0 text-muted small">Tahun:</label>
                        <select name="year" id="year" class="form-select form-select-sm" style="width: auto; min-width: 100px;">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="month" class="form-label mb-0 text-muted small">Bulan:</label>
                        <select name="month" id="month" class="form-select form-select-sm" style="width: auto; min-width: 120px;">
                            <option value="">Semua Bulan</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                                <option value="{{ $index + 1 }}" {{ $selectedMonth == ($index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="branch_id" class="form-label mb-0 text-muted small">Cabang:</label>
                        <select name="branch_id" id="branch_id" class="form-select form-select-sm" style="width: auto; min-width: 150px;">
                            <option value="">Semua Cabang</option>
                            @foreach($allBranches as $branchOption)
                                <option value="{{ $branchOption->id }}" {{ $selectedBranch === $branchOption->id ? 'selected' : '' }}>
                                    {{ $branchOption->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </form>
                <div class="mt-3">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- SUMMARY CARDS: 4 Kolom (Total Pendapatan, Laba Bersih, Total Transaksi, Cabang Terbaik) --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    {{-- Card 1: Total Pendapatan --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #4E6BFF !important;">
            <div class="card-body position-relative" style="overflow: hidden;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Pendapatan</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line text-primary fa-lg"></i>
                    </div>
                </div>
                @if($growthPercentage != 0)
                    <div class="d-flex align-items-center gap-2">
                        @if($growthPercentage > 0)
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fas fa-arrow-up fa-xs"></i> {{ abs($growthPercentage) }}%
                            </span>
                            <span class="text-muted small">vs periode sebelumnya</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-arrow-down fa-xs"></i> {{ abs($growthPercentage) }}%
                            </span>
                            <span class="text-muted small">vs periode sebelumnya</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 2: Laba Bersih --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #198754 !important;">
            <div class="card-body position-relative" style="overflow: hidden;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Laba Bersih</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalLabaBersih, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-coins text-success fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">HPP: Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Transaksi --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body position-relative" style="overflow: hidden;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Transaksi</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-cart text-info fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Penjualan: {{ $dataTransaksiChart[0] ?? 0 }} | Pembelian: {{ $dataTransaksiChart[1] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

{{-- Card 4: Cabang Terbaik - Hanya tampil jika filter "Semua Cabang" --}}
@if($showBestBranch)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body position-relative" style="overflow: hidden;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Cabang Terbaik</p>
                        <h5 class="mb-1 fw-bold">{{ $namaCabangTerbaik }}</h5>
                        @if($labaCabangTerbaik > 0)
                            <h6 class="mb-0 text-muted">Rp {{ number_format($labaCabangTerbaik, 0, ',', '.') }}</h6>
                        @else
                            <h6 class="mb-0 text-muted">-</h6>
                        @endif
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items; center; justify-content: center;">
                        <i class="fas fa-trophy text-warning fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Laba bersih tertinggi</span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ======================================================= --}}
{{-- CHARTS SECTION: Area Chart & Donut Chart --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    {{-- Main Chart: Annual (Pendapatan & HPP) --}}
    <div class="col-md-12 col-lg-7 mb-4" >
        <div class="card shadow-sm border-0" style="min-height: 417px">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap p-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Grafik Annual ({{ $selectedYear }})</h5>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                        </div>
                        <span class="text-muted small">Pendapatan</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                            <div class="bg-info rounded-circle" style="width: 8px; height: 8px;"></div>
                        </div>
                        <span class="text-muted small">HPP</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="chartPendapatanBulanan" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    {{-- Donut Chart: Total Transaksi --}}
    <div class="col-md-12 col-lg-5 mb-4">
        <div class="card shadow-sm border-0" style="min-height: 417px">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center p-3">
                <h5 class="card-title fw-bold mb-0 text-dark">Transaksi</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div id="chartTotalTransaksi" style="min-height: 250px; width: 60%;"></div>
                    <div class="d-grid gap-3" style="width: 40%;">
                        <div class="d-flex align-items-start">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 mt-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Penjualan</span>
                                <h6 class="fw-bold mb-0">{{ $dataTransaksiChart[0] ?? 0 }}</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2 mt-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <div class="bg-success rounded-circle" style="width: 8px; height: 8px;"></div>
                            </div>
                            <div>
                                <span class="text-muted small d-block">Pembelian</span>
                                <h6 class="fw-bold mb-0">{{ $dataTransaksiChart[1] ?? 0 }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- WIDGET SECTION: Top Products (Full Width) --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-star text-warning me-2"></i>Top 5 Produk Terlaris
                </h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th>Produk</th>
                                    <th>Kode SKU</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr>
                                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($product['gambar'] && $product['gambar']->url)
                                                    <img src="{{ $product['gambar']->url }}" alt="{{ $product['nama_produk'] }}"
                                                         class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                                        {{ $product['nama_produk'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-secondary font-monospace">{{ $product['kode_sku'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">Rp {{ number_format($product['harga_jual'], 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $product['total_qty'] }}</span>
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
{{-- TRANSACTION PURCHASES TABLE: Tabel Transaksi Pembelian --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-shopping-bag text-success me-2"></i>Transaksi Pembelian Terakhir
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($recentPurchases) && $recentPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Customer</th>
                                    <th>Cabang</th>
                                    <th style="width: 25%;">Item Dibeli</th>
                                    <th class="text-end">Harga Deal</th>
                                    <th class="text-center">Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $index => $purchase)
                                    <tr>
                                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                                                {{ $purchase['kode_transaksi'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                                {{ $purchase['customer'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-secondary">
                                                {{ $purchase['cabang'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($purchase['items'] as $item)
                                                    <li>
                                                        <span class="text-dark">{{ $item['nama_produk'] }} (x{{ $item['qty'] }})</span>
                                                    </li>
                                                @endforeach
                                            </ul>

                                        <td class="text-end">
                                            @if($purchase['harga_deal'] > 0)
                                                <span class="fw-bold text-dark">Rp {{ number_format($purchase['harga_deal'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($purchase['status'] == 'deal')
                                                <span class="badge bg-success bg-opacity-10 text-success">Deal</span>
                                            @elseif($purchase['status'] == 'tidak_deal')
                                                <span class="badge bg-danger bg-opacity-10 text-danger">Tidak Deal</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td>
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

        // 1. GRAFIK PENDAPATAN BULANAN (Area Chart) - Nonaktifkan Zoom
        var optionsPendapatan = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false, // Nonaktifkan toolbar termasuk zoom
                    tools: {
                        zoom: false, // Nonaktifkan zoom secara spesifik
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                        download: false
                    }
                },
                zoom: {
                    enabled: false // Nonaktifkan zooming
                },
                sparkline: { enabled: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
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
                    show: true,
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
                },
                tooltip: {
                    enabled: false
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
                show: false
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'light',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif'
                },
                x: {
                    show: true,
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                        return w.globals.categoryLabels[dataPointIndex];
                    }
                },
                y: {
                    formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                        return formatRupiah(value);
                    },
                    title: {
                        formatter: function(seriesName) {
                            return seriesName + ':';
                        }
                    }
                },
                marker: {
                    show: true
                }
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 0,
                colors: ['#fff'],
                strokeColors: ['#4E6BFF', '#0dcaf0'],
                strokeWidth: 2,
                strokeOpacity: 0.9,
                strokeDashArray: 0,
                fillOpacity: 1,
                discrete: [],
                shape: "circle",
                radius: 2,
                hover: {
                    size: 6,
                    sizeOffset: 3
                }
            }
        };

        var chartPendapatan = new ApexCharts(document.querySelector("#chartPendapatanBulanan"), optionsPendapatan);
        chartPendapatan.render();

        // 2. GRAFIK TOTAL TRANSAKSI (Donut Chart) - TETAP SEPERTI ASLI
        var optionsTransaksi = {
            chart: {
                type: 'donut',
                height: 250
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
                                fontSize: '0.875rem',
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

        // Auto-submit form saat dropdown berubah (optional)
        document.getElementById('year').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('month').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        const branchSelect = document.getElementById('branch_id');
        if (branchSelect) {
            branchSelect.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
    });
</script>
@endpush
