<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;

class DashboardController extends Controller
{
    public function index()
    {
        $selectedYear = now()->year;

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $pendapatanBulanan = Penjualan::selectRaw('MONTH(tanggal) as bulan, SUM(harga_total) as total')
            ->whereYear('tanggal', $selectedYear)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $hppBulanan = DetailPenjualan::selectRaw('MONTH(penjualan.tanggal) as bulan, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.tanggal', $selectedYear)
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

        $branches = Branch::withSum([
            'sales as pendapatanCabang' => function ($query) use ($selectedYear) {
                $query->whereYear('tanggal', $selectedYear);
            }
        ], 'harga_total')->get();

        $hppPerBranch = DetailPenjualan::selectRaw('penjualan.perusahaan_cabang_id, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total_hpp')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.tanggal', $selectedYear)
            ->groupBy('penjualan.perusahaan_cabang_id')
            ->pluck('total_hpp', 'perusahaan_cabang_id');

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

        $dataTransaksiChart = [
            Penjualan::whereYear('tanggal', $selectedYear)->count(),
            DB::table('pembelian')->whereYear('tanggal', $selectedYear)->count(),
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
