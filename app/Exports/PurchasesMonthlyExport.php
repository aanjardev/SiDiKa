<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesMonthlyExport implements FromCollection, WithHeadings, WithMapping
{
    private int $rowIndex = 0;

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
            'No',
            'Kode',
            'Tanggal',
            'Customer',
            'Item Dibeli',
            'Cabang',
            'Status',
            'Harga Deal',
        ];
    }

    public function map($purchase): array
    {
        $this->rowIndex++;
        $itemList = $purchase->item_pembelian_draft->map(function ($item) {
            $nama = $item->nama_item ?? '-';
            $qty = (int) ($item->qty ?? 0);
            return $nama . ' (' . $qty . ')';
        })->implode(', ');

        return [
            $this->rowIndex,
            $purchase->kode_transaksi ?? ('#' . $purchase->id),
            optional($purchase->created_at)->format('Y-m-d H:i'),
            $purchase->customer->nama ?? '-',
            $itemList ?: '-',
            $purchase->perusahaan_cabang->nama ?? '-',
            $purchase->status_pembelian ?? '-',
            (int) ($purchase->harga_deal ?? 0),
        ];
    }
}
