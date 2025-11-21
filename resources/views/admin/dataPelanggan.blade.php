@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Pelanggan') {{-- Judul Halaman --}}

@section('content')


{{-- Search & Filter --}}
<form method="GET" action="{{ route('admin.customers.index') }}" id="searchForm">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
        <div class="flex-grow-1">
            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa-solid fa-search"></i>
                </span>
                <input type="text" 
                       class="form-control" 
                       name="search" 
                       placeholder="Cari pelanggan berdasarkan nama atau nomor telepon..." 
                       value="{{ $search_term ?? '' }}">
            </div>
        </div>

        <select name="sort_by" class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" onchange="document.getElementById('searchForm').submit();">
            <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Terakhir diubah</option>
            <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
        </select>

        <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
    </div>
</form>



<div class="card shadow-sm">
    <div class="card-body p-0 table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-product">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th style="width:30%">Alamat</th>
                        <th>NIK</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_pelanggan as $index => $pelanggan)
                        <tr>
                            <td class="text-center">{{ ($data_pelanggan->firstItem() ?? 0) + $index }}</td>
                            <td>{{ $pelanggan->nama }}</td>
                            <td>{{ $pelanggan->jenis_kelamin }}</td>
                            <td>{{ $pelanggan->no_telp }}</td>
                            <td>{{ $pelanggan->alamat }}</td>
                            <td>{{ $pelanggan->identitas }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.customers.show', $pelanggan->id) }}" title="Lihat">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $pelanggan->id) }}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $pelanggan->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-icon btn-delete" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>


                    @empty
                        <tr class="tr-empty">
                            <td colspan="7" class="text-center">
                                <div>
                                    <i class="fa-solid fa-users fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Pelanggan</h5>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    @if ($data_pelanggan->hasPages())
        <div class="card-footer bg-white">
            {{  $data_pelanggan->links('pagination::bootstrap-5')  }}
        </div>
    @endif

</div>

@endsection


@push('styles')
    <style>
        .table {
            border-radius: 5px;
            overflow: hidden;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-product tbody tr:nth-child(even) {
            background-color: #F8F9FC;
        }

        .table-product tbody tr:hover {
            background-color: #EFF3F9;
            transition: 0.2s;
        }

        button.btn-icon,
        .table-product button.btn-icon,
        form .btn-icon {
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            color: #dc3545 !important;
            /* merah */
            cursor: pointer !important;
            font-size: 16px !important;
            line-height: 1 !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Pastikan ikon mewarisi warna, dan svg FA menggunakan fill:currentColor */
        .btn-icon i,
        .btn-icon svg,
        .btn-icon .fa-solid {
            color: inherit !important;
            fill: currentColor !important;
            stroke: currentColor !important;
        }

        /* Hilangkan efek fokus/active yang mungkin ditambahkan global */
        button.btn-icon:focus,
        button.btn-icon:active,
        .btn-icon:focus,
        .btn-icon:active {
            outline: none !important;
            box-shadow: none !important;
        }

        .btn-icon:hover {
            color: #bb2d3b;
        }

        .table-wrapper {
            min-height: 700px;
            /* display: flex;
            flex-direction: column; */
        }

        .table-responsive {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .table-product {
            flex-grow: 1;
        }

        .table-product tbody {
            height: 100%;
        }

        .table-product tr.tr-empty {
            height: 100%;
        }

        .table-product tr.tr-empty td {
            vertical-align: middle;
            padding-top: 0;
            padding-bottom: 0;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const searchForm = document.getElementById('searchForm');
            let searchTimeout;

            if (searchInput && searchForm) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        searchForm.submit();
                    }, 500); // Submit setelah 500ms tidak ada input
                });

                // Submit saat Enter ditekan
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        searchForm.submit();
                    }
                });
            }

            // Custom alert untuk delete pelanggan dengan informasi nama
            // Menggunakan event delegation untuk menghindari konflik dengan alert.js
            document.addEventListener('click', function(e) {
                // Cek apakah klik pada button delete di dalam form customers
                const deleteBtn = e.target.closest('.btn-delete');
                if (!deleteBtn) return;
                
                const form = deleteBtn.closest('form');
                if (!form || !form.action || !form.action.includes('customers')) return;

                e.preventDefault();
                e.stopPropagation();

                // Ambil nama pelanggan dari baris tabel (kolom ke-2 adalah nama)
                const row = form.closest('tr');
                const namaPelanggan = row ? row.querySelector('td:nth-child(2)')?.textContent?.trim() || 'pelanggan ini' : 'pelanggan ini';

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    html: `Data pelanggan <strong>${namaPelanggan}</strong> akan dihapus secara permanen!<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }, true); // Menggunakan capture phase untuk dijalankan sebelum alert.js
        });
    </script>
    @endpush
