<tbody>
    @forelse ($data_pembelian as $pembelian)
        <tr>
            <td class="text-center">#{{ $pembelian->id }}</td>
            <td>{{ $pembelian->customer->nama ?? 'N/A' }}</td>
            <td>{{ $pembelian->created_at->format('d M Y, H:i') }}</td>
            <td>{{ $pembelian->perusahaan_cabang->nama ?? 'N/A' }}</td>
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
                    <a href="{{ route('admin.purchases.show', $pembelian->id) }}" title="Lihat Detail Transaksi">
                        <i class="fa-solid fa-eye" style="color: black;"></i>
                    </a>
                    <a href="{{ route('admin.purchases.edit', $pembelian->id) }}" title="Edit Transaksi">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr class="tr-empty">
            <td colspan="8" class="text-center">
                <div>
                    <i class="fa-solid fa-shopping-bag fa-2x text-muted mb-3"></i>
                    <h5 class="mb-1">Tidak Ada Data Pembelian</h5>
                    <p class="text-muted mb-0">Silakan <a href="{{ route('admin.purchases.create') }}">tambah transaksi pembelian</a> baru.</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>

@if ($data_pembelian->hasPages())
    {{-- Pagination akan dimuat di luar tag <table>, jadi kita kirim juga --}}
    <div class="card-footer bg-white">
        {{ $data_pembelian->links('pagination::bootstrap-5') }}
    </div>
@endif
