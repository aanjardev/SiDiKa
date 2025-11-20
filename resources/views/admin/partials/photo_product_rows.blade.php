@php
    $first = $products->firstItem();
@endphp

@forelse($products as $idx => $p)
    <tr>
        <td  style="width:60px;" class="text-center">{{ $first + $idx }}</td>
        <td>{{ $p->kode_sku }}</td>
        <td style="vertical-align: top">{{ $p->nama_produk }}</td>
        <td style="vertical-align: top">{{ $p->kategori->nama_kategori ?? '-' }}</td>
        <td style="vertical-align: top">{{ $p->stok_produk ?? 0 }}</td>
        <td style="vertical-align: top">Rp {{ number_format($p->harga_jual ?? 0, 0, ',', '.') }}</td>
        <td class="text-center">
            <a href="{{ route('admin.products.photos.upload', $p->id) }}" class="btn btn-sm btn-outline-primary" title="Unggah Foto">
                <i class="fas fa-upload"></i> Upload
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5">
            <div>
                <h5 class="mb-1">Tidak ada produk yang perlu foto</h5>
                <p class="text-muted mb-0">Semua produk sudah memiliki foto atau belum ada produk tanpa gambar.</p>
            </div>
        </td>
    </tr>
@endforelse
