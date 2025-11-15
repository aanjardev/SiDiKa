<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPembelian extends Model
{
    use HasFactory;
    protected $table = 'item_pembelian_draft';

    protected $fillable = [
        'pembelian_id',
        'kode_sku',
        'nama_item',
        'kategori_id',
        'serial_number',
        'serial_lens',
        'kondisi_fisik',
        'kondisi_baut',
        'kondisi_tutup_usb',
        'kondisi_grip',
        'kondisi_jamur_lensa',
        'kondisi_view_finder',
        'kondisi_mounting',
        'kondisi_slot_memori',
        'kondisi_jamur_sensor',
        'kondisi_lcd',
        'kondisi_tombol',
        'kondisi_zoom_lensa',
        'kondisi_af_lensa',
        'kondisi_diafragma_lensa',
        'kondisi_kalibrasi_fokus',
        'kondisi_flash',
        'kondisi_sound_mic',
        'kondisi_lain_lain',
        'kelengkapan',
        'qty',
        'harga_jual',
        'harga_beli',
        'harga_servis',
        'grade',
        'status',
        'deskripsi_produk',
        'status_qc',
        'catatan_qc',
        'produk_id_final',
    ];

    public function kategori()
    {

        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke data Pembelian (induknya)
     */
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }
}
