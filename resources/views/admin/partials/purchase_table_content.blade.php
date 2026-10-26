@forelse ($data_pembelian as $index => $pembelian)
<tr class="purchase-row" data-detail-url="{{ route('admin.purchases.show', $pembelian->id) }}">
    <td class="text-center text-muted fw-bold">{{ ($data_pembelian->firstItem() ?? 0) + $index }}</td>

    {{-- Kode Transaksi --}}
    <td>
        <a href="{{ route('admin.purchases.show', $pembelian->id) }}"
           class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small text-decoration-none clickable-code transaction-code"
           onclick="event.stopPropagation();">
            {{ $pembelian->kode_transaksi ?? '#' . $pembelian->id }}
        </a>
    </td>

    {{-- Customer --}}
    <td>
        <span class="text-dark fw-semibold transaction-text" style="font-size: 0.95rem;">
            {{ $pembelian->customer->nama ?? '-' }}
        </span>
    </td>

    {{-- Tanggal --}}
    <td class="text-muted small opacity-90 transaction-date">
        <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
        <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
    </td>

    {{-- Cabang --}}
    <td class="text-dark small">
        <span class="transaction-text">{{ $pembelian->perusahaan_cabang->nama ?? '-' }}</span>
    </td>

    {{-- Item Dibeli --}}
    <td>
        @php
            $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
        @endphp
        <span class="text-secondary small transaction-text" title="{{ $itemNames }}">{{ $itemNames ?: '-' }}</span>
    </td>

    {{-- Status --}}
    <td class="text-center">
        @if($pembelian->status_pembelian == 'deal')
            <span class="badge rounded-pill bg-success bg-opacity-10 text-success">Deal</span>
        @elseif($pembelian->status_pembelian == 'tidak_deal')
            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger">No-Deal</span>
        @else
            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Draft</span>
        @endif
    </td>

    {{-- Harga Deal --}}
    <td class="fw-bold text-dark transaction-amount">
        @if ($pembelian->harga_deal !== null)
            Rp{{ number_format($pembelian->harga_deal, 0, ',', '.') }}
        @else
            -
        @endif
    </td>

    {{-- Aksi --}}
    <td class="text-center">
        <div class="transaction-actions">
            {{-- Edit --}}
            <a href="{{ route('admin.purchases.edit', $pembelian->id) }}"
                class="btn-action btn-action-edit no-row-navigation"
                title="Edit">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>

            {{-- Hapus --}}
            <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="button"
                        class="btn-action btn-action-delete no-row-navigation"
                        title="Hapus"
                        data-message="Apakah Anda yakin ingin menghapus data pembelian ini?">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr class="tr-empty">
    <td colspan="9" class="text-center py-5">
        <div class="d-flex flex-column align-items-center opacity-50">
            <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-muted"></i>
            <h6 class="text-muted">Belum ada data pembelian</h6>
            <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.purchases.create') }}">transaksi pembelian</a> baru.</p>
        </div>
    </td>
</tr>
@endforelse
