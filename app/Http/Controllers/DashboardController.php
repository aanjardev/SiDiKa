<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Logika untuk mengambil data 12 bulan
        $dataPendapatanChart = [3000000, 4500000, 5000000, 6000000, 7000000, 8000000, 7500000, 9000000, 9500000, 11000000, 12000000, 13000000];
        $dataHppChart = [2000000, 3000000, 3500000, 4000000, 5000000, 5500000, 5000000, 6000000, 6500000, 7000000, 7500000, 8000000];
        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $dataTransaksiChart = [251, 176];

        // 1. Data Card Total Pendapatan
        $totalPendapatan = 300000000;
        $totalHPP = 105000000;
        $totalLabaKotor = $totalPendapatan - $totalHPP;

        $persentaseHPP = ($totalPendapatan > 0) ? ($totalHPP / $totalPendapatan) * 100 : 0;
        $persentaseLabaKotor = ($totalPendapatan > 0) ? ($totalLabaKotor / $totalPendapatan) * 100 : 0;

        $dataCabang = [
            [
                'namaCabang' => 'Dinoyo Kamera 1',
                'pendapatanCabang' => 120000000,
                'hppCabang' => 40000000,
                'labaBersihCabang' => 50000000 // Contoh (setelah dikurangi biaya operasional)
            ],
            [
                'namaCabang' => 'Dinoyo Kamera 2',
                'pendapatanCabang' => 90000000,
                'hppCabang' => 35000000,
                'labaBersihCabang' => 30000000
            ],
            [
                'namaCabang' => 'Dinoyo Kamera 3',
                'pendapatanCabang' => 90000000,
                'hppCabang' => 30000000,
                'labaBersihCabang' => 25000000
            ]
        ];


        return view('admin.index', [
            // Data untuk Chart
            'dataPendapatanChart' => $dataPendapatanChart,
            'dataHppChart' => $dataHppChart,
            'labelBulan' => $labelBulan,
            'dataTransaksiChart' => $dataTransaksiChart,

            // Data untuk Card Row 1
            'totalPendapatan' => $totalPendapatan,
            'totalHPP' => $totalHPP,
            'totalLabaKotor' => $totalLabaKotor,
            'persentaseHPP' => $persentaseHPP,
            'persentaseLabaKotor' => $persentaseLabaKotor,
            'dataCabang' => $dataCabang
        ]);
    }
}
