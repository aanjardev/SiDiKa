<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesMonthlyExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Tanggal',
            'Customer',
            'Cabang',
            'Total',
        ];
    }

    public function map($sale): array
    {
        $fallbackTotal = $sale->detail_penjualan->sum(function ($detail) {
            return (int) ($detail->qty ?? 0) * (int) ($detail->harga_jual_satuan ?? 0);
        });
        $totalNominal = ($sale->harga_total ?? 0) > 0 ? $sale->harga_total : $fallbackTotal;

        return [
            $sale->kode_transaksi,
            optional($sale->created_at)->format('Y-m-d H:i'),
            $sale->customer->nama ?? '-',
            $sale->perusahaan_cabang->nama ?? '-',
            $totalNominal,
        ];
    }
}
