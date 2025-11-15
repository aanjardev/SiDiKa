<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch; // (Model Anda dari file)
use App\Models\Penjualan; // (Model Anda dari file)
use App\Models\DetailPenjualan; // (Model Anda dari file)

class DashboardController extends Controller
{
    public function index()
    {
        $selectedYear = now()->year;

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // =======================================================
        // PERBAIKAN: Mengganti 'tanggal' -> 'created_at'
        // =======================================================
        $pendapatanBulanan = Penjualan::selectRaw('MONTH(created_at) as bulan, SUM(harga_total) as total')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // =======================================================
        // PERBAIKAN: Mengganti 'penjualan.tanggal' -> 'penjualan.created_at'
        // =======================================================
        $hppBulanan = DetailPenjualan::selectRaw('MONTH(penjualan.created_at) as bulan, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataPendapatanChart = [];
        $dataHppChart = [];
        foreach (range(1, 12) as $bulan) {
            $dataPendapatanChart[] = (int) ($pendapatanBulanan[$bulan] ?? 0);
            $dataHppChart[] = (int) ($hppBulanan[$bulan] ?? 0);
        }

        $totalPendapatan = array_sum($dataPendapatanChart);
        $totalHPP = array_sum($dataHppChart);
        $totalLabaKotor = $totalPendapatan - $totalHPP;

        $persentaseHPP = $totalPendapatan > 0 ? ($totalHPP / $totalPendapatan) * 100 : 0;
        $persentaseLabaKotor = $totalPendapatan > 0 ? ($totalLabaKotor / $totalPendapatan) * 100 : 0;

        // =======================================================
        // PERBAIKAN: Mengganti 'tanggal' -> 'created_at'
        // =======================================================
        $branches = Branch::withSum([
            'sales as pendapatanCabang' => function ($query) use ($selectedYear) {
                $query->whereYear('created_at', $selectedYear);
            }
        ], 'harga_total')->get();

        // =======================================================
        // PERBAIKAN: Mengganti 'penjualan.tanggal' -> 'penjualan.created_at'
        // =======================================================
        $hppPerBranch = DetailPenjualan::selectRaw('penjualan.perusahaan_cabang_id, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total_hpp')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $selectedYear)
            ->groupBy('penjualan.perusahaan_cabang_id')
            ->pluck('total_hpp', 'penjualan.perusahaan_cabang_id');

        $dataCabang = $branches->map(function ($branch) use ($hppPerBranch) {
            $pendapatan = (int) ($branch->pendapatanCabang ?? 0);
            $hpp = (int) ($hppPerBranch[$branch->id] ?? 0);
            $laba = $pendapatan - $hpp;

            return [
                'namaCabang' => $branch->nama,
                'pendapatanCabang' => max($pendapatan, 0),
                'hppCabang' => max($hpp, 0),
                'labaBersihCabang' => max($laba, 0),
            ];
        })->values();

        // =======================================================
        // PERBAIKAN: Mengganti 'tanggal' -> 'created_at'
        // =======================================================
        $dataTransaksiChart = [
            Penjualan::whereYear('created_at', $selectedYear)->count(),
            DB::table('pembelian')->whereYear('created_at', $selectedYear)->count(),
        ];

        return view('admin.index', [
            'dataPendapatanChart' => $dataPendapatanChart,
            'dataHppChart' => $dataHppChart,
            'labelBulan' => $labelBulan,
            'dataTransaksiChart' => $dataTransaksiChart,
            'totalPendapatan' => $totalPendapatan,
            'totalHPP' => $totalHPP,
            'totalLabaKotor' => $totalLabaKotor,
            'persentaseHPP' => $persentaseHPP,
            'persentaseLabaKotor' => $persentaseLabaKotor,
            'dataCabang' => $dataCabang,
        ]);
    }
}
