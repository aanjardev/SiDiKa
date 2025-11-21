@forelse ($data_penjualan as $penjualan)
<tr class="align-middle">
    <td class="text-center">{{ $loop->iteration }}</td>
    <td>{{ $penjualan->kode_transaksi }}</td>
    <td>{{ $penjualan->customer->nama ?? '-' }}</td>
    <td>{{ $penjualan->created_at->format('d M Y') }}</td>

    <td>
        @php
            $itemNames = $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ');
            echo \Illuminate\Support\Str::limit($itemNames, 40, '...');
        @endphp
    </td>

    <td>{{ $penjualan->perusahaan_cabang->nama ?? '-' }}</td>

    <td>Rp {{ number_format($penjualan->harga_total, 0, ',', '.') }}</td>

    <td class="text-center">
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('admin.sales.edit', $penjualan->id) }}"><i class="fa-solid fa-pen"></i></a>
            <form action="{{ route('admin.sales.destroy', $penjualan->id) }}" method="POST" class="delete-form">
                @csrf @method('DELETE')
                <button class="btn-icon"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr class="tr-empty">
    <td colspan="8" class="text-center py-5">
        Tidak ada data penjualan
    </td>
</tr>
@endforelse
