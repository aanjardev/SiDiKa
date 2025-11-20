@extends('layouts.admin') 

@section('title', 'Data Pelanggan') 

@section('content')

{{-- Filter dan Pencarian (Style: Satu Card Putih Clean) --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Ikon Filter & Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i> 
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent" 
                   placeholder="Cari pelanggan berdasarkan nama atau nomor telepon..."
                   style="font-size: 0.95rem;">
        </div>

        {{-- Bagian Kanan: Dropdown Filter --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Urutkan: Terakhir diubah</option>
                <option value="az">Nama (A-Z)</option>
                <option value="za">Nama (Z-A)</option>
            </select>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
    <div class="card-body p-0">
        <table class="table align-middle mb-0 table-hover">
            {{-- Header Abu-abu Terang --}}
            <thead class="bg-light"> 
                <tr class="text-dark fw-bold" style="border-bottom: 2px solid #eee;">
                    <th class="text-center py-3" style="width: 5%;">No</th>
                    <th class="py-3">Nama Pelanggan</th>
                    <th class="py-3">Jenis Kelamin</th>
                    <th class="py-3">No. Telepon</th>
                    <th class="py-3" style="width: 25%;">Alamat</th>
                    <th class="py-3">NIK</th>
                    <th class="text-center py-3" style="width: 140px;">Aksi</th> 
                </tr>
            </thead>
            <tbody>
                @forelse ($data_pelanggan as $pelanggan)
                    <tr style="border-bottom: 1px solid #f0f0f0;">
                        <td class="text-center">{{ $loop->iteration }}</td>
                        
                        <td>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                                {{ $pelanggan->nama }}
                            </span>
                        </td>

                        <td>
                            {{-- Opsional: Badge sederhana untuk JK --}}
                            @if(in_array(strtolower($pelanggan->jenis_kelamin), ['laki-laki', 'l']))
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Laki-laki</span>
                            @elseif(in_array(strtolower($pelanggan->jenis_kelamin), ['perempuan', 'p']))
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Perempuan</span>
                            @else
                                <span class="text-secondary">{{ $pelanggan->jenis_kelamin }}</span>
                            @endif
                        </td>

                        <td class="text-muted">{{ $pelanggan->no_telp }}</td>
                        
                        <td class="text-muted small text-wrap" style="line-height: 1.4;">
                            {{ $pelanggan->alamat }}
                        </td>
                        
                        <td class="text-dark fw-medium">{{ $pelanggan->identitas }}</td>

                        {{-- Aksi Buttons --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Tombol Lihat --}}
                                <a href="#" 
                                   class="btn btn-sm btn-light text-dark border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Tombol Edit --}}
                                <a href="#" 
                                   class="btn btn-sm btn-light text-primary border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                   style="width: 32px; height: 32px;"
                                   title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="" method="POST" class="d-inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-light text-danger border shadow-sm d-flex align-items-center justify-content-center rounded-3"
                                            style="width: 32px; height: 32px;" 
                                            title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fa-solid fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Data Pelanggan</h5>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if ($data_pelanggan->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-end py-3">
            {{ $data_pelanggan->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection