    @extends('layouts.admin')

    @section('title', 'Data Cabang')

    @push('page-actions')
    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah Cabang</span>
    </a>
    @endpush

    @section('content')

    {{-- Search & Filter --}}
    <div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
        <div class="flex-grow-1 ">
            <div class="input-group shadow-sm">
                <span class="input-group-text">
                    <i class="fa-solid fa-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Cari produk berdasarkan nama atau SKU...">
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-product">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Cabang</th>
                            <th style="width:40%">Alamat</th>
                            <th>No. Telepon</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data_cabang as $cabang)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $cabang->nama }}</td>
                            <td>
                                @if ($cabang->link_maps)
                                <a href="{{ $cabang->link_maps }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    title="Lihat di Google Maps">
                                    {{ $cabang->alamat }}
                                    <i class="fa-solid fa-location-dot ms-1 text-danger"></i>
                                </a>
                                @else
                                {{ $cabang->alamat }}
                                @endif
                            </td>

                            <td>{{ $cabang->nomor_telepon }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- <a href="#" title="Lihat">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a> -->
                                    <a href="{{ route('admin.branches.edit', $cabang->id) }}" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.branches.destroy', $cabang->id) }}" method="POST" class="d-inline">
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
                        <tr>
                            <td colspan="5" class="text-center">
                                <div>
                                    <i class="fa-solid fa-store fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Cabang</h5>
                                    <p class="text-muted mb-0">Silakan <a href="{{ route('admin.branches.create') }}">tambah data cabang</a> baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($data_cabang->hasPages())
        <div class="card-footer bg-white">
            {{ $data_cabang->links('pagination::bootstrap-5') }}
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
