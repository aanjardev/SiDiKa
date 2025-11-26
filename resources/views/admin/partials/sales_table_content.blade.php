@forelse ($data_penjualan as $index => $penjualan)
<tr>
    <td class="text-center text-muted fw-bold">{{ ($data_penjualan->firstItem() ?? 0) + $index }}</td>

    <td>
        <a href="{{ route('admin.sales.show', $penjualan->id) }}"
            class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small text-decoration-none">
            {{ $penjualan->kode_transaksi }}
        </a>

    </td>

    <td>
        <div class="d-flex align-items-center gap-3">
            <div class="flex-grow-1">
                <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                    {{ $penjualan->customer->nama ?? 'Umum / N/A' }}
                </span>
            </div>
        </div>
    </td>

    <td class="text-muted small">
        <span class="fw-medium text-dark">{{ $penjualan->created_at->format('d M Y') }}</span>
        <br>
        <span class="opacity-75">{{ $penjualan->created_at->format('H:i') }} WIB</span>
    </td>

    <td>
        <span class="text-secondary small d-block"
            title="{{ $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ') }}"
            style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            @php
            $itemNames = $penjualan->detail_penjualan->pluck('produk.nama_produk')->implode(', ');
            echo $itemNames ?: '-';
            @endphp
        </span>
    </td>

    <td class="text-dark fw-medium">
        {{ $penjualan->perusahaan_cabang->nama ?? '-' }}
    </td>

    <td class="fw-bold text-dark">
        @php
        $fallbackTotal = $penjualan->detail_penjualan->sum(function($d){
        return ($d->qty ?? 0) * ($d->harga_jual_satuan ?? 0);
        });
        $totalNominal = ($penjualan->harga_total ?? 0) > 0 ? $penjualan->harga_total : $fallbackTotal;
        @endphp
        Rp{{ number_format($totalNominal, 0, ',', '.') }}
    </td>

    <td class="text-center">
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ route('admin.sales.edit', $penjualan->id) }}" class="btn-action btn-action-edit" title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <form action="{{ route('admin.sales.destroy', $penjualan->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button" class="btn-action btn-action-delete" title="Hapus" onclick="handleDeletePenjualan(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <div class="d-flex flex-column align-items-center opacity-50">
            <i class="fa-solid fa-receipt fa-3x mb-3 text-muted"></i>
            <h6 class="text-muted">Belum ada data penjualan</h6>
            <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.sales.create') }}">transaksi penjualan</a> baru.</p>
        </div>
    </td>
</tr>
@endforelse