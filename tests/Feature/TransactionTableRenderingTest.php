<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TransactionTableRenderingTest extends TestCase
{
    public function test_sale_renders_escaped_item_names_and_the_calculated_total(): void
    {
        $sale = (object) [
            'id' => 1, 'kode_transaksi' => 'PJ-001', 'created_at' => now(),
            'customer' => (object) ['nama' => 'Customer'], 'perusahaan_cabang' => null,
            'harga_total' => 0,
            'detail_penjualan' => collect([(object) [
                'produk' => (object) ['nama_produk' => '<b>Camera & Lens</b>'],
                'qty' => 2, 'harga_jual_satuan' => 15000,
            ]]),
        ];
        $html = view('admin.partials.sales_table_content', [
            'data_penjualan' => new LengthAwarePaginator([$sale], 1, 10),
        ])->render();
        $this->assertStringContainsString('Rp30.000', $html);
        $this->assertStringContainsString('&lt;b&gt;Camera &amp; Lens&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>Camera', $html);
    }

    public function test_purchase_distinguishes_an_unset_price_from_zero(): void
    {
        foreach ([[null, '-'], [0, 'Rp0']] as [$price, $expected]) {
            $purchase = (object) [
                'id' => 1, 'kode_transaksi' => 'PB-001', 'created_at' => now(),
                'customer' => null, 'perusahaan_cabang' => null,
                'harga_deal' => $price, 'status_pembelian' => 'draft',
                'item_pembelian_draft' => collect([(object) ['nama_item' => '<b>Camera</b>']]),
            ];
            $html = view('admin.partials.purchase_table_content', [
                'data_pembelian' => new LengthAwarePaginator([$purchase], 1, 10),
            ])->render();
            $document = new \DOMDocument();
            @$document->loadHTML($html);
            $amount = (new \DOMXPath($document))->query('//td[contains(@class,"transaction-amount")]')->item(0);
            $this->assertSame($expected, trim($amount->textContent));
            $this->assertStringContainsString('&lt;b&gt;Camera&lt;/b&gt;', $html);
        }
    }
}
