@forelse ($data_pembelian as $index => $pembelian)
    <tr class="purchase-row" data-detail-url="{{ route('admin.purchases.show', $pembelian->id) }}">
        <td class="text-center text-muted fw-bold">{{ ($data_pembelian->firstItem() ?? 0) + $index }}</td>

        {{-- Kode Transaksi --}}
        <td>
            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                {{ $pembelian->kode_transaksi ?? '#' . $pembelian->id }}
            </span>
        </td>

        {{-- Customer --}}
        <td>
            <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                {{ $pembelian->customer->nama ?? '-' }}
            </span>
        </td>

        {{-- Tanggal --}}
        <td class="text-muted small opacity-90">
            <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
            {{-- <br> --}}
            <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
        </td>

        {{-- Cabang --}}
        <td class="text-dark small">
            {{ $pembelian->perusahaan_cabang->nama ?? '-' }}
        </td>

        {{-- Item Dibeli --}}
        <td>
            <span class="text-secondary small d-block"
                    title="{{ $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ') }}"
                    style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                @php
                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                    echo $itemNames ?: '-';
                @endphp
            </span>
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
        <td class="fw-bold text-dark">
            Rp{{ number_format($pembelian->harga_deal, 0, ',', '.') }}
        </td>

        {{-- Aksi --}}
        <td class="text-center" style="width:120px">
            <div class="d-flex justify-content-center gap-2">
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
                            onclick="confirmDelete(this)">
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
