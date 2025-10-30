@extends('layouts.admin')  {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Dashboard') {{-- Mengatur judul halaman --}}

@section('content')

{{-- Bagian Tombol Aksi (Penjualan & Pembelian) --}}
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-end gap-3">
        {{-- Tombol Penjualan --}}
        <button class="btn btn-primary d-flex align-items-center gap-2">
            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4V20M20 12L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span>Penjualan</span>
        </button>

        {{-- Tombol Pembelian --}}
        <button class="btn btn-success d-flex align-items-center gap-2" style="background-color: #198754; border-color: #198754;">
            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4V20M20 12L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <span>Pembelian</span>
        </button>
    </div>
</div>

{{-- Baris Pertama: Card Ringkasan Pendapatan --}}
<div class="row">
    {{-- Card 1: Pendapatan Utama --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Pendapatan</p>
                    <span class="badge rounded-pill bg-primary fw-normal">July 2025</span>
                </div>
                <h3 class="mb-3 fw-bold">Rp300.000.000</h3>
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-muted">HPP</span>
                    <span class="fw-medium">Rp105.000.000</span>
                    <span class="badge bg-light text-dark">35%</span>
                </div>
                <div class="d-flex justify-content-between text-sm mt-1">
                    <span class="text-muted">Laba Kotor</span>
                    <span class="fw-medium">Rp105.000.000</span>
                    <span class="badge bg-light text-dark">35%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Pendapatan Cabang 1 --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Pendapatan</p>
                    <span class="badge rounded-pill bg-info fw-normal text-dark">Dinoyo Kamera 1</span>
                </div>
                <h3 class="mb-3 fw-bold">Rp300.000.000</h3>
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-muted">HPP</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
                <div class="d-flex justify-content-between text-sm mt-1">
                    <span class="text-muted">Laba Kotor</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Pendapatan Cabang 2 --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Pendapatan</p>
                    <span class="badge rounded-pill bg-info fw-normal text-dark">Dinoyo Kamera 1</span>
                </div>
                <h3 class="mb-3 fw-bold">Rp300.000.000</h3>
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-muted">HPP</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
                <div class="d-flex justify-content-between text-sm mt-1">
                    <span class="text-muted">Laba Kotor</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Pendapatan Cabang 3 --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 text-muted">Pendapatan</p>
                    <span class="badge rounded-pill bg-info fw-normal text-dark">Dinoyo Kamera 1</span>
                </div>
                <h3 class="mb-3 fw-bold">Rp300.000.000</h3>
                <div class="d-flex justify-content-between text-sm">
                    <span class="text-muted">HPP</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
                <div class="d-flex justify-content-between text-sm mt-1">
                    <span class="text-muted">Laba Kotor</span>
                    <span class="fw-medium">Rp105.000.000</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Baris Kedua: Grafik (Chart) --}}
<div class="row">
    {{-- Card Grafik Pendapatan Bulanan --}}
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 fw-bold">Pendapatan Bulanan</h5>
                    {{-- Ini bisa jadi filter dropdown --}}
                    <select class="form-select form-select-sm" style="width: auto; border: none;">
                        <option value="2025" selected>2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                {{--
                  Area Grafik Batang (Bar Chart)
                  Ini adalah placeholder. Anda perlu menggantinya dengan
                  library chart seperti Chart.js, ApexCharts, atau ECharts
                  untuk menampilkan data sesungguhnya.
                --}}
                <div id="monthlyRevenueChart" style="height: 300px;" class="d-flex align-items-center justify-content-center bg-light rounded text-muted">
                    [Placeholder untuk Grafik Pendapatan Bulanan]
                </div>
            </div>
        </div>
    </div>

    {{-- Card Grafik Total Transaksi (Donut Chart) --}}
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 fw-bold">Total Transaksi</h5>
                    <span class="badge rounded-pill bg-primary fw-normal">July 2025</span>
                </div>

                {{-- Area Grafik Donut (Donut Chart) --}}
                <div id="transactionChart" style="height: 250px;" class="d-flex align-items-center justify-content-center bg-light rounded text-muted">
                    [Placeholder untuk Grafik Total Transaksi]
                </div>

                {{-- Legenda Chart --}}
                <div class="d-flex justify-content-around mt-3">
                    <div class="text-center">
                        <h6 class="mb-1 fw-bold">251</h6>
                        <span class="text-muted">
                            <i class="bi bi-circle-fill text-primary"></i> Penjualan
                        </span>
                    </div>
                    <div class="text-center">
                        <h6 class="mb-1 fw-bold">176</h6>
                        <span class="text-muted">
                            <i class="bi bi-circle-fill text-info"></i> Pembelian
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
{{--
  Jika Anda menggunakan library chart, Anda perlu memuat JS-nya di sini.
  Contoh jika menggunakan ApexCharts:

  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script>
      // Contoh kode untuk Bar Chart (Pendapatan Bulanan)
      var optionsBar = {
          chart: { type: 'bar', height: 300, toolbar: { show: false } },
          series: [
              { name: 'Laba', data: [30, 40, 45, 50, 49, 60, 70, 91, 125, 100, 80, 60] },
              { name: 'HPP', data: [20, 30, 35, 40, 39, 50, 60, 81, 115, 90, 70, 50] }
          ],
          xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'] },
          // ...opsi lainnya
      };
      var chartBar = new ApexCharts(document.querySelector("#monthlyRevenueChart"), optionsBar);
      chartBar.render();

      // Contoh kode untuk Donut Chart (Total Transaksi)
      var optionsDonut = {
          chart: { type: 'donut', height: 250 },
          series: [251, 176], // Data Penjualan & Pembelian
          labels: ['Penjualan', 'Pembelian'],
          // ...opsi lainnya
      };
      var chartDonut = new ApexCharts(document.querySelector("#transactionChart"), optionsDonut);
      chartDonut.render();
  </script>
--}}
@endpush
