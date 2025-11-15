@extends('layouts.admin') {{-- Menggunakan layout admin.blade.php --}}

@section('title', 'Data Pelanggan') {{-- Judul Halaman --}}

@section('content')


{{-- Search & Filter --}}
<div class="card-body d-flex flex-wrap gap-2 align-items-center mb-4 p-0">
    <div class="flex-grow-1 ">

        <div class="input-group shadow-sm">
            <span class="input-group-text">
                <i class="fa-solid fa-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Cari pelanggan berdasarkan nama atau nomor telepon...">
        </div>
    </div>

    <select class="form-select w-auto shadow-sm" style="height: calc(2.5rem + 10px);">
        <option selected>Terakhir diubah</option>
        <option>Nama (A-Z)</option>
    </select>
</div>



<div class="card shadow-sm">
    <div class="card-body p-0 table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-product">
                <thead class="table-light">
                    {{-- ======================================================= --}}
                    {{-- BAGIAN 4: Ganti Kolom Tabel (thead) --}}
                    {{-- ======================================================= --}}
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Nama</th>
                        <th>No. Telepon</th>
                        <th style="width:30%">Alamat</th>
                        <th>NIK</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ======================================================= --}}
                    {{-- BAGIAN 5: Ganti Isi Data (tbody) & @empty --}}
                    {{-- ======================================================= --}}

                    {{--
                      Kita gunakan @forelse dengan array kosong (empty array)
                      untuk mensimulasikan "Tidak Ada Data" (karena Anda minta FE dulu)
                    --}}
                    @forelse ([] as $pelanggan) {{-- Ganti [] dengan $data_pelanggan nanti --}}
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $pelanggan->nama }}</td>
                            <td>{{ $pelanggan->no_telp }}</td>
                            <td>{{ $pelanggan->alamat }}</td>
                            <td>{{ $pelanggan->identitas }}</td> {{-- (Asumsi 'identitas' = NIK) --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" title="Lihat">
                                        <i class="fa-solid fa-eye" style="color: black;"></i>
                                    </a>
                                    <a href="#" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')" class="d-inline">
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
                            <td colspan="6" class="text-center">
                                <div>
                                    <i class="fa-solid fa-users fa-2x text-muted mb-3"></i>
                                    <h5 class="mb-1">Tidak Ada Data Pelanggan</h5>
                                    <p class="text-muted mb-0">Silakan <a href="#">tambah data pelanggan</a> baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination (Dinamis dari Controller) --}}
    {{-- ======================================================= --}}
    {{-- BAGIAN 6: Pagination (Jika Perlu) --}}
    {{-- ======================================================= --}}
    @if (false) {{-- Ganti false dengan $data_pelanggan->hasPages() nanti --}}
        <div class="card-footer bg-white">
            {{-- Ini akan otomatis menampilkan link pagination (1, 2, 3, Next, Prev) --}}
            {{ '' /* $data_pelanggan->links('pagination::bootstrap-5') */ }}
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

    /* Style untuk Tombol Hapus (btn-icon) */
    button.btn-icon,
    .table-product button.btn-icon,
    form .btn-icon {
        background: transparent !important; border: none !important;
        padding: 0 !important; color: #dc3545 !important;
        cursor: pointer !important; font-size: 16px !important;
        line-height: 1 !important; appearance: none !important;
        box-shadow: none !important; outline: none !important;
    }
    .btn-icon i, .btn-icon svg, .btn-icon .fa-solid {
        color: inherit !important; fill: currentColor !important;
        stroke: currentColor !important;
    }
    button.btn-icon:focus, button.btn-icon:active,
    .btn-icon:focus, .btn-icon:active {
        outline: none !important; box-shadow: none !important;
    }
    .btn-icon:hover { color: #bb2d3b !important; }

    /* ======================================================= */
    /* PERBAIKAN 5: CSS UNTUK TINGGI TABEL FIX & EMPTY STATE */
    /* ======================================================= */
    .table-wrapper {
        min-height: 700px; /* Atur tinggi minimal */
        display: flex;
        flex-direction: column;
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
        /* (PENTING) Hapus 'height: 100%' dari sini */
    }
    .table-product tr.tr-empty {
        /* (PENTING) Ganti 'height: 100%' dengan ini */
        /* Ini akan membuat <tr> mengisi sisa ruang */
        flex-grow: 1;
        display: table-row;
    }
    .table-product tr.tr-empty td {
        vertical-align: middle;
        padding-top: 0;
        padding-bottom: 0;
    }
</style>
@endpush
