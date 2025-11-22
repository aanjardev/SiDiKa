{{-- @forelse ($data_qc as $item)
    <tr>
        <td class="text-center" style="width: 60px;">{{ $loop->iteration + (($data_qc->currentPage()-1) * $data_qc->perPage()) }}</td>
        <td>{{ $item->pembelian->kode_transaksi}}</td>
        <td>{{ $item->nama_item }}</td>
        <td>{{ $item->serial_number ?? '-' }}/{{ $item->serial_lens ?? '-'}}</td>
        <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
        <td>
            @php $persen = round($item->persentase_lengkap); @endphp
            <div class="progress" role="progressbar" style="height: 12px;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar bg-primary" style="width: {{ $persen }}%"></div>
            </div>
            <span class="small text-muted">{{ $persen }}% Lengkap</span>
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                @if(!empty($show_restore))
                    <form action="{{ route('admin.quality-control.restore', $item->id) }}" method="POST" class="d-inline restore-form">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" title="Kembalikan dari Arsip">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span class="ms-1">Restore</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.quality-control.edit', $item->id) }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1 px-2" title="Proses QC">
                        <i class="fa-solid fa-clipboard-check fa-fw"></i>
                        <span>Proses</span>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr class="tr-empty">
        <td colspan="7" class="p-0">
            <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">
                <i class="fa-solid fa-check-circle fa-2x text-muted mb-3"></i>
                @php
                    $emptyHeading = $empty_heading ?? 'Tidak Ada Item Menunggu QC';
                    $emptyText = $empty_text ?? "Semua item dari transaksi 'Deal' akan muncul di sini.";
                @endphp
                <h5 class="mb-1">{{ $emptyHeading }}</h5>
                <p class="text-muted mb-0">{{ $emptyText }}</p>
            </div>
        </td>
    </tr>
@endforelse --}}
