    @extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

    @section('title', 'Data Produk') {{-- Judul Halaman --}}

    @push('page-actions')
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fas fa-plus fa-fw"></i>
            <span>Tambah Produk</span>
        </a>
    @endpush

    @section('content')


    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('admin.products.index') }}" id="searchForm">
        <div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
            <div class="flex-grow-1">
                <div class="input-group shadow-sm">
                    <span class="input-group-text">
                        <i class="fa-solid fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Cari produk berdasarkan nama atau SKU..." 
                           value="{{ $search_term ?? '' }}">
                </div>
            </div>

            <select name="kategori" class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" onchange="document.getElementById('searchForm').submit();">
                <option value="all" {{ ($selected_kategori ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                @foreach($semua_kategori ?? [] as $kat)
                    <option value="{{ $kat->id }}" {{ ($selected_kategori ?? 'all') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <select name="sort_by" class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);" onchange="document.getElementById('searchForm').submit();">
                <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Terakhir diubah</option>
                <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
            </select>

            <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
        </div>
    </form>

    {{-- Table --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-product">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th>Nama Produk</th>
                    <th>SKU</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Terakhir diubah</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $index => $product)
                    <tr>
                        <td class="text-center">{{ ($products->firstItem() ?? 0) + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($product->gambarUtama)
                                    <img src="{{ $product->gambarUtama->url }}"
                                            alt="{{ $product->nama_produk }}"
                                            class="rounded" style="width:60px; height:60px; object-fit:cover;"
                                            loading="lazy">
                                @else
                                    <img src="{{ asset('images/placeholder.jpg') }}"
                                            alt=" No Image"
                                            class="product-image" style="width:60px; height:60px; font-size:10px; object-fit:cover;">
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $product->nama_produk }}</div>
                                    <small class="text-muted">{{ $product->kategori_produk }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->kode_sku }}</td>
                        <td>Rp{{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                        <td>{{ $product->stok_produk }} Unit</td>
                        <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center py-5">
                            <div>
                                <i class="fa-solid fa-box-open fa-2x text-muted mb-3"></i>
                                <h5 class="mb-1">Tidak Ada Data Produk</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    @if ($products->hasPages())
    <div class="card-footer bg-white d-flex justify-content-center">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

    @endsection

    @push('styles')
    <style>
        /* Warna baris genap */
        .table-product tbody tr:nth-child(even) {
            background-color: #F8F9FC;
            /* abu muda lembut */
        }

        /* Hover biar smooth */
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
            cursor: pointer !important;
            font-size: 16px !important;
            line-height: 1 !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .btn-icon i,
        .btn-icon svg,
        .btn-icon .fa-solid {
            color: inherit !important;
            fill: currentColor !important;
            stroke: currentColor !important;
        }

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
        });
    </script>
    @endpush
