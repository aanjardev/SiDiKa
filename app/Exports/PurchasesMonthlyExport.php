<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesMonthlyExport implements FromCollection, WithHeadings, WithMapping
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
            'Status',
            'Harga Deal',
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->kode_transaksi ?? ('#' . $purchase->id),
            optional($purchase->created_at)->format('Y-m-d H:i'),
            $purchase->customer->nama ?? '-',
            $purchase->perusahaan_cabang->nama ?? '-',
            $purchase->status_pembelian ?? '-',
            (int) ($purchase->harga_deal ?? 0),
        ];
    }
}
