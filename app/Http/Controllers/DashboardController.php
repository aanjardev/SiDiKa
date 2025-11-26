<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Pembelian;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // =======================================================
        // FILTER DINAMIS: Ambil year & month dari request
        // =======================================================
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month', now()->month);
        $allBranches = Branch::orderBy('nama', 'asc')->get(['id', 'nama']);
        $selectedBranch = $request->input('branch_id');
        $selectedBranch = $selectedBranch === 'all' ? null : $selectedBranch;
        if (!empty($selectedBranch)) {
            $selectedBranch = (int) $selectedBranch;
            if (!$allBranches->pluck('id')->contains($selectedBranch)) {
                $selectedBranch = null;
            }
        } else {
            $selectedBranch = null;
        }
        $selectedBranchName = $selectedBranch
            ? optional($allBranches->firstWhere('id', $selectedBranch))->nama ?? 'Cabang'
            : 'Semua Cabang';
        
        // Validasi input
        $selectedYear = (int) $selectedYear;
        $selectedMonth = $selectedMonth ? (int) $selectedMonth : null;

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // =======================================================
        // QUERY BASE dengan filter tahun
        // =======================================================
        $queryPenjualan = Penjualan::whereYear('created_at', $selectedYear);
        $queryDetailPenjualan = DetailPenjualan::join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear);

        // Jika ada filter bulan, tambahkan kondisi
        if ($selectedMonth) {
            $queryPenjualan->whereMonth('created_at', $selectedMonth);
            $queryDetailPenjualan->whereMonth('penjualan.created_at', $selectedMonth);
        }
        if ($selectedBranch) {
            $queryPenjualan->where('perusahaan_cabang_id', $selectedBranch);
            $queryDetailPenjualan->where('penjualan.perusahaan_cabang_id', $selectedBranch);
        }

        // =======================================================
        // STATISTIK UTAMA: Total Pendapatan, Laba Bersih, Total Transaksi
        // =======================================================
        $totalPendapatan = (int) $queryPenjualan->clone()->sum('harga_total');
        
        $totalHPP = (int) $queryDetailPenjualan->clone()
            ->selectRaw('SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total')
            ->value('total') ?? 0;
        
        $totalLabaBersih = $totalPendapatan - $totalHPP;

        // Total Transaksi (Penjualan + Pembelian)
        $totalPenjualan = $queryPenjualan->clone()->count();
        $queryPembelian = Pembelian::whereYear('created_at', $selectedYear);
        if ($selectedMonth) {
            $queryPembelian->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedBranch) {
            $queryPembelian->where('perusahaan_cabang_id', $selectedBranch);
        }
        $totalPembelian = $queryPembelian->count();
        $totalTransaksi = $totalPenjualan + $totalPembelian;

        // =======================================================
        // PERTUMBUHAN (Growth): Bandingkan bulan ini dengan bulan sebelumnya
        // =======================================================
        $growthPercentage = 0;
        if ($selectedMonth) {
            // Pendapatan bulan ini
            $pendapatanBulanIni = (int) Penjualan::whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->sum('harga_total');
            
            // Pendapatan bulan sebelumnya
            $bulanSebelumnya = $selectedMonth - 1;
            $tahunSebelumnya = $selectedYear;
            if ($bulanSebelumnya < 1) {
                $bulanSebelumnya = 12;
                $tahunSebelumnya = $selectedYear - 1;
            }
            
            $pendapatanBulanSebelumnya = (int) Penjualan::whereYear('created_at', $tahunSebelumnya)
                ->whereMonth('created_at', $bulanSebelumnya)
                ->sum('harga_total');
            
            // Hitung growth
            if ($pendapatanBulanSebelumnya > 0) {
                $growthPercentage = (($pendapatanBulanIni - $pendapatanBulanSebelumnya) / $pendapatanBulanSebelumnya) * 100;
            } elseif ($pendapatanBulanIni > 0) {
                $growthPercentage = 100; // 100% growth jika sebelumnya 0
            }
        } else {
            // Jika filter tahun, bandingkan dengan tahun sebelumnya
            $pendapatanTahunIni = (int) Penjualan::whereYear('created_at', $selectedYear)->sum('harga_total');
            $pendapatanTahunSebelumnya = (int) Penjualan::whereYear('created_at', $selectedYear - 1)->sum('harga_total');
            
            if ($pendapatanTahunSebelumnya > 0) {
                $growthPercentage = (($pendapatanTahunIni - $pendapatanTahunSebelumnya) / $pendapatanTahunSebelumnya) * 100;
            } elseif ($pendapatanTahunIni > 0) {
                $growthPercentage = 100;
            }
        }

        // =======================================================
        // CHART DATA: Area Chart (Pendapatan & HPP per bulan)
        // =======================================================
        $pendapatanBulananQuery = Penjualan::selectRaw('MONTH(created_at) as bulan, SUM(harga_total) as total')
            ->whereYear('created_at', $selectedYear);
        if ($selectedBranch) {
            $pendapatanBulananQuery->where('perusahaan_cabang_id', $selectedBranch);
        }
        $pendapatanBulanan = $pendapatanBulananQuery->groupBy('bulan')->pluck('total', 'bulan');

        $hppBulananQuery = DetailPenjualan::selectRaw('MONTH(penjualan.created_at) as bulan, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear);
        if ($selectedBranch) {
            $hppBulananQuery->where('penjualan.perusahaan_cabang_id', $selectedBranch);
        }
        $hppBulanan = $hppBulananQuery->groupBy('bulan')->pluck('total', 'bulan');

        $dataPendapatanChart = [];
        $dataHppChart = [];
        foreach (range(1, 12) as $bulan) {
            $dataPendapatanChart[] = (int) ($pendapatanBulanan[$bulan] ?? 0);
            $dataHppChart[] = (int) ($hppBulanan[$bulan] ?? 0);
        }

        // =======================================================
        // CHART DATA: Donut Chart (Proporsi Penjualan vs Pembelian)
        // =======================================================
        $countPenjualan = Penjualan::whereYear('created_at', $selectedYear);
        $countPembelian = Pembelian::whereYear('created_at', $selectedYear);
        if ($selectedBranch) {
            $countPenjualan->where('perusahaan_cabang_id', $selectedBranch);
            $countPembelian->where('perusahaan_cabang_id', $selectedBranch);
        }
        $dataTransaksiChart = [
            $countPenjualan->count(),
            $countPembelian->count(),
        ];

        // =======================================================
        // WIDGET DATA: Top 5 Products (berdasarkan qty penjualan tahun ini)
        // =======================================================
        $topProductsQuery = DetailPenjualan::selectRaw('
                produk.id,
                produk.nama_produk,
                produk.harga_jual,
                SUM(detail_penjualan.qty) as total_qty
            ')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear);
        if ($selectedBranch) {
            $topProductsQuery->where('penjualan.perusahaan_cabang_id', $selectedBranch);
        }
        $topProducts = $topProductsQuery
            ->groupBy('produk.id', 'produk.nama_produk', 'produk.harga_jual')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $produk = Produk::with('gambarUtama')->find($item->id);
                return [
                    'id' => $item->id,
                    'nama_produk' => $item->nama_produk,
                    'harga_jual' => (int) $item->harga_jual,
                    'total_qty' => (int) $item->total_qty,
                    'gambar' => $produk ? $produk->gambarUtama : null,
                ];
            });

        // =======================================================
        // WIDGET DATA: Recent 5 Sales (transaksi terakhir)
        // =======================================================
        $recentSales = Penjualan::with(['customer', 'branch'])
            ->whereYear('created_at', $selectedYear)
            ->when($selectedBranch, fn ($q) => $q->where('perusahaan_cabang_id', $selectedBranch))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($penjualan) {
                return [
                    'id' => $penjualan->id,
                    'kode_transaksi' => $penjualan->kode_transaksi ?? 'N/A',
                    'customer' => $penjualan->customer->nama ?? 'Guest',
                    'total' => (int) $penjualan->harga_total,
                    'status' => $penjualan->kas ?? 'cash',
                    'waktu' => $penjualan->created_at->format('d M Y, H:i'),
                    'cabang' => $penjualan->branch->nama ?? 'N/A',
                ];
            });

        // =======================================================
        // WIDGET DATA: Recent 5 Purchases (transaksi pembelian terakhir)
        // =======================================================
        $recentPurchases = Pembelian::with(['customer', 'perusahaan_cabang'])
            ->whereYear('created_at', $selectedYear)
            ->when($selectedBranch, fn ($q) => $q->where('perusahaan_cabang_id', $selectedBranch))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($pembelian) {
                return [
                    'id' => $pembelian->id,
                    'kode_transaksi' => $pembelian->kode_transaksi ?? '#' . $pembelian->id,
                    'customer' => $pembelian->customer->nama ?? 'N/A',
                    'harga_deal' => (int) ($pembelian->harga_deal ?? 0),
                    'status' => $pembelian->status_pembelian ?? 'draft',
                    'waktu' => $pembelian->created_at->format('d M Y, H:i'),
                    'cabang' => $pembelian->perusahaan_cabang->nama ?? 'N/A',
                ];
            });

        // =======================================================
        // BRANCH DATA: Performa per cabang (tetap dipertahankan)
        // =======================================================
        $branchesQuery = Branch::query();
        if ($selectedBranch) {
            $branchesQuery->where('id', $selectedBranch);
        }
        $branches = $branchesQuery->withSum([
            'sales as pendapatanCabang' => function ($query) use ($selectedYear, $selectedMonth, $selectedBranch) {
                $query->whereYear('created_at', $selectedYear);
                if ($selectedMonth) {
                    $query->whereMonth('created_at', $selectedMonth);
                }
                if ($selectedBranch) {
                    $query->where('perusahaan_cabang_id', $selectedBranch);
                }
            }
        ], 'harga_total')->get();

        $hppPerBranch = DetailPenjualan::selectRaw('penjualan.perusahaan_cabang_id, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total_hpp')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear);
        
        if ($selectedMonth) {
            $hppPerBranch->whereMonth('penjualan.created_at', $selectedMonth);
        }
        if ($selectedBranch) {
            $hppPerBranch->where('penjualan.perusahaan_cabang_id', $selectedBranch);
        }
        
        $hppPerBranch = $hppPerBranch->groupBy('penjualan.perusahaan_cabang_id')
            ->pluck('total_hpp', 'penjualan.perusahaan_cabang_id');

        $dataCabang = $branches->map(function ($branch) use ($hppPerBranch) {
            $pendapatan = (int) ($branch->pendapatanCabang ?? 0);
            $hpp = (int) ($hppPerBranch[$branch->id] ?? 0);
            $laba = $pendapatan - $hpp;

            return [
                'id' => $branch->id,
                'namaCabang' => $branch->nama,
                'pendapatanCabang' => max($pendapatan, 0),
                'hppCabang' => max($hpp, 0),
                'labaBersihCabang' => max($laba, 0),
            ];
        })->values();

        // Cabang terbaik (dengan omzet tertinggi)
        $cabangTerbaik = $dataCabang->sortByDesc('pendapatanCabang')->first();
        $namaCabangTerbaik = $cabangTerbaik ? $cabangTerbaik['namaCabang'] : 'N/A';
        $omzetCabangTerbaik = $cabangTerbaik ? $cabangTerbaik['pendapatanCabang'] : 0;

        // =======================================================
        // RETURN DATA KE VIEW
        // =======================================================
        return view('admin.index', [
            // Filter
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'labelBulan' => $labelBulan,
            'selectedBranch' => $selectedBranch,
            'allBranches' => $allBranches,
            
            // Statistik Utama
            'totalPendapatan' => $totalPendapatan,
            'totalHPP' => $totalHPP,
            'totalLabaBersih' => $totalLabaBersih,
            'totalTransaksi' => $totalTransaksi,
            'growthPercentage' => round($growthPercentage, 2),
            
            // Chart Data
            'dataPendapatanChart' => $dataPendapatanChart,
            'dataHppChart' => $dataHppChart,
            'dataTransaksiChart' => $dataTransaksiChart,
            
            // Widget Data
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            
            // Branch Data
            'dataCabang' => $dataCabang,
            'namaCabangTerbaik' => $namaCabangTerbaik,
            'omzetCabangTerbaik' => $omzetCabangTerbaik,
            'selectedBranchName' => $selectedBranchName,
        ]);
    }
}
