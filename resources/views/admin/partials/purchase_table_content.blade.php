@forelse ($data_pembelian as $pembelian)
        <tr>
            {{-- ... baris data Anda yang normal ... --}}
            <td>{{ $pembelian->kode_transaksi }}</td>
            <td>{{ $pembelian->customer->nama ?? '-' }}</td>
            <td>{{ $pembelian->created_at->format('d M Y, H:i') }}</td>
            <td>{{ $pembelian->perusahaan_cabang->nama ?? '-' }}</td>
            <td>
                @php
                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                    echo \Illuminate\Support\Str::limit($itemNames, 40, '...');
                @endphp
            </td>
            <td>
                @if($pembelian->status_pembelian == 'deal')
                    <span class="badge bg-success-subtle text-success-emphasis">Deal</span>
                @elseif($pembelian->status_pembelian == 'tidak_deal')
                    <span class="badge bg-danger-subtle text-danger-emphasis">Tidak Deal</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Draft</span>
                @endif
            </td>
            <td>
                @if($pembelian->harga_deal)
                    Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}
                @else
                    -
                @endif
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" class="btn-action btn-action-edit no-row-navigation" title="Edit Transaksi">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
<<<<<<< HEAD
                        <button type="button" class="btn-icon" title="Hapus" onclick="confirmDeletePurchase(this)">
=======
                        <button type="submit" class="btn-action btn-action-delete no-row-navigation" title="Hapus">
>>>>>>> 99e10f2c9d05daca8b76d5053a5782b85968abea
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr class="tr-empty">
            {{-- Tambahkan p-0 di td untuk menghapus padding, dan tambahkan h-100 di inner div --}}
            <td colspan="8" class="p-0">
                {{-- d-flex dan h-100 adalah kunci untuk membuat inner div ini bisa dipusatkan --}}
                <div class="d-flex flex-column align-items-center justify-content-center h-100 p-5 empty-message">
                    <i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>
                    <h5 class="mb-1">Tidak Ada Data Pembelian</h5>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
