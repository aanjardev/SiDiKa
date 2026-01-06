<!DOCTYPE html>
<html>
<head>
    <title>Nota Penjualan #{{ $penjualan->kode_transaksi }}</title>
    <style>
        
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 0; }
        .container { width: 90%; margin: 0 auto; }

        
        .header {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            overflow: auto;
        }
        .logo-container {
            float: left;
            width: 15%;
            padding-right: 15px;
            box-sizing: border-box;
        }
        .text-details {
            overflow: hidden;
        }
        .text-details h3 {
            font-size: 20px;
            margin: 0 0 5px 0 !important;
        }
        .text-details p {
            font-size: 12px;
            margin: 0 0 3px 0 !important;
        }

        
        .footer { border-top: 1px solid #ccc; border-bottom: none; position: fixed; bottom: 0; width: 90%; }

        .content { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }

        
        .item-table th, .item-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        .item-table th { background-color: #f0f0f0; }

        
        .price-table { border: none; }
        .price-table td { border: none; padding: 4px 8px; }

        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .section-title {
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            font-weight: bold;
        }

        
        .signature-box { width: 100%; margin-top: 30px; }
        .signature-col { width: 50%; text-align: center; float: left; }
    </style>
</head>
<body>

<div class="container">
    {{-- HEADER PERUSAHAAN --}}
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('mainIMG/logoDinoyo.png') }}" alt="Logo Dinoyo Kamera" style="height: 70px;">
        </div>
        <div class="text-details">
            <h3 style="margin: 0; color: #333;">DINOYO KAMERA</h3>
            <p style="margin: 0;">Alamat: {{ $penjualan->perusahaan_cabang->nama ?? '-' }} ({{ $penjualan->perusahaan_cabang->alamat ?? '-' }})</p>
            <p style="margin: 0;">Kontak: {{ $penjualan->perusahaan_cabang->nomor_telepon ?? '-' }} | Instagram: @dinoyokamera</p>
        </div>
    </div>

    <div class="content">
        <h2 style="text-align: center; margin-bottom: 20px;">NOTA PENJUALAN</h2>

        {{-- DETAIL TRANSAKSI --}}
        <div style="float: left; width: 70%;">
            <p style="margin: 5px;"><strong>No. Transaksi:</strong> {{ $penjualan->kode_transaksi }}</p>
            <p style="margin: 5px;"><strong>Tanggal:</strong> {{ $penjualan->created_at->format('d M Y, H:i') }}</p>
            <p style="margin: 5px;"><strong>Metode Bayar:</strong> {{ strtoupper($penjualan->kas ?? '-') }}</p>
        </div>
        <div style="float: right; width: 30%; text-align: left;">
            <p style="margin: 5px;"><strong>Customer:</strong> {{ $penjualan->customer->nama ?? '-' }}</p>
            <p style="margin: 5px;"><strong>Telp:</strong> {{ $penjualan->customer->no_telp ?? '-' }}</p>
            <p style="margin: 5px;"><strong>Kasir:</strong> {{ $penjualan->user->name ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>

        {{-- DAFTAR ITEM (S/N Dihilangkan) --}}
        <div class="section-title">DAFTAR ITEM</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 40%;">Nama Produk</th>
                    <th style="width: 15%;" class="text-right">Qty</th>
                    <th style="width: 20%;" class="text-right">Harga Satuan</th>
                    <th style="width: 20%;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach (($penjualan->detail_penjualan ?? []) as $index => $detail)
                @php
                    $product = $detail->produk ?? null;
                    $qty = (int) ($detail->qty ?? 0);
                    $price = (int) ($detail->harga_jual_satuan ?? 0);
                    $lineTotal = $qty * $price;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->nama_produk ?? '-' }}</td>
                    <td class="text-right">{{ $qty }}</td>
                    <td class="text-right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- HARGA DEPRESIASI (Per Transaksi) --}}
        @if(($penjualan->harga_depresiasi_final ?? 0) > 0)
        <div class="section-title" style="margin-top: 12px;">HARGA DEPRESIASI</div>
        <table class="price-table" style="width: 50%; float: right; border: 1px solid #000; margin-top: 5px;">
            <tr>
                <td style="padding: 6px 8px; width: 60%;">Harga Depresiasi</td>
                <td class="text-right fw-bold" style="padding: 6px 8px; width: 40%;">Rp {{ number_format((int) $penjualan->harga_depresiasi_final, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
        @endif


        {{-- RINGKASAN HARGA (Menampilkan detail diskon & biaya tambahan) --}}
        <div style="clear: both;"></div>
        <div class="section-title" style="margin-top: 30px;">RINGKASAN HARGA</div>
        @php

            $subtotal = (int) ($subtotal ?? ($penjualan->subtotal ?? 0));
            $diskon = (int) ($penjualan->diskon ?? 0);

            $biayaTambahan = (int) ($penjualan->biaya_tambahan ?? 0);
            if ($biayaTambahan === 0 && isset($penjualan->harga_total)) {
                $biayaTambahan = max(0, (int) $penjualan->harga_total - $subtotal + $diskon);
            }
            $totalBayar = $penjualan->harga_total ?? max(0, $subtotal - $diskon + $biayaTambahan);

            $hargaBeliKembaliMaksimal = $penjualan->harga_depresiasi_final ?? 0;
        @endphp
        <table class="price-table" style="width: 50%; float: right; border: 1px solid #000;">
            {{-- Subtotal Produk --}}
            <tr>
                <td style="width: 60%; padding: 4px 8px;">Subtotal Produk</td>
                <td class="text-right" style="width: 40%; padding: 4px 8px;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            {{-- Diskon --}}
            @if($diskon > 0)
            <tr>
                <td style="padding: 4px 8px;">Diskon</td>
                <td class="text-right" style="padding: 4px 8px;">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
            </tr>
            @endif
            {{-- Biaya Tambahan --}}
            @if($biayaTambahan > 0)
            <tr>
                <td style="padding: 4px 8px;">Biaya Tambahan</td>
                <td class="text-right" style="padding: 4px 8px;">Rp {{ number_format($biayaTambahan, 0, ',', '.') }}</td>
            </tr>
            @endif
            {{-- TOTAL BAYAR (Gaya Plek Ketiplek) --}}
            <tr>
                <td class="fw-bold" style="background-color: #eee; padding: 8px 10px; width: 60%; border: none; font-size: 12px; border-right: 1px solid #000;">TOTAL BAYAR</td>
                <td class="fw-bold text-right" style="background-color: #eee; padding: 8px 10px; width: 40%; border: none; font-size: 12px;">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>

        {{-- HARGA BELI KEMBALI MAKSIMAL (Satu per Transaksi) --}}
        @if ($hargaBeliKembaliMaksimal > 0)
        <div class="section-title" style="margin-top: 30px; font-size: 12px; color: #a94442;">HARGA BELI KEMBALI MAKSIMAL</div>
        <table class="price-table" style="width: 50%; float: right; border: 1px solid #000; margin-top: 5px;">
            <tr>
                <td class="fw-bold" style="background-color: #f9f9f9; padding: 8px 10px; width: 60%; border: none; font-size: 12px; border-right: 1px solid #000;">HARGA BELI KEMBALI MAKSIMAL (REFERENSI)</td>
                <td class="fw-bold text-right" style="background-color: #f9f9f9; padding: 8px 10px; width: 40%; border: none; font-size: 12px;">Rp {{ number_format($hargaBeliKembaliMaksimal, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
        @endif

        {{-- TANDA TANGAN --}}
        <div class="signature-box">
            <div class="signature-col">
                <p style="margin-bottom: 50px;">Pembeli / Customer</p>
                <p style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto; padding-bottom: 5px;">( {{ $penjualan->customer->nama ?? 'Nama Pembeli' }} )</p>
            </div>
            <div class="signature-col">
                <p style="margin-bottom: 50px;">Hormat Kami,</p>
                <p style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto; padding-bottom: 5px;">( {{ $penjualan->user->name ?? 'Petugas' }} )</p>
            </div>
        </div>
        <div style="clear: both;"></div>

        {{-- CATATAN & KETENTUAN (Diperbarui dengan keterangan depresiasi Transaksi) --}}
        <div class="section-title" style="margin-top: 30px;">CATATAN & KETENTUAN</div>
        <ul style="font-size: 9px; padding-left: 15px; margin-top: 5px; list-style-type: disc;">
            <li style="margin-bottom: 5px;">Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian tertulis.</li>
            <li style="margin-bottom: 5px;">Kerusakan setelah barang diterima bukan tanggung jawab toko kecuali tercantum dalam garansi.</li>
            @if ($hargaBeliKembaliMaksimal > 0)
            <li style="margin-bottom: 5px;">*Harga Beli Kembali Maksimal* (Rp {{ number_format($hargaBeliKembaliMaksimal, 0, ',', '.') }}) dicantumkan sebagai referensi harga maksimal yang mungkin ditawarkan Dinoyo Kamera jika barang ini dijual kembali oleh customer di masa depan. Harga ini dapat berubah sewaktu-waktu tergantung kondisi fisik barang saat dijual kembali.</li>
            @endif
            <li style="margin-bottom: 5px;">Nota ini berlaku sebagai bukti transaksi penjualan.</li>
        </ul>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p style="margin: 0; font-size: 8px;">Terima kasih telah bertransaksi di {{ $penjualan->perusahaan_cabang->nama ?? 'Toko Kami' }}.</p>
    </div>
</div>

</body>
</html>
