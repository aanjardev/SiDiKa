@extends('layouts.admin')

@section('title', 'Profil Pengguna')

@section('content')
<div class="row">
    
    {{-- KOLOM KIRI: Kartu Identitas & Aksi Utama --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 text-center h-100" style="border-radius: 10px;">
            <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                {{-- Avatar Placeholder --}}
                <div class="mb-3 position-relative">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary" 
                         style="width: 120px; height: 120px; font-size: 3rem;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    {{-- Opsional: Tombol ganti foto bisa ditaruh sini --}}
                </div>

                <h5 class="fw-bold text-dark mb-1">{{ Auth::user()->name ?? 'Nama Pengguna' }}</h5>
                <p class="text-muted small mb-4">{{ Auth::user()->role ?? 'Jabatan / Role' }}</p>

                <div class="d-grid w-100 gap-2">
                    {{-- Tombol Edit Profil (Mengarahkan ke halaman edit jika ada) --}}
                    <a href="{{ route('admin.employees.edit', Auth::user()->id) }}?from=pageProfil" class="btn btn-outline-primary fw-medium">
                        <i class="fa-solid fa-user-pen me-2"></i> Edit Profil
                    </a>

                    {{-- Tombol Logout --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-danger fw-medium">
                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Detail Informasi --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-circle-info me-2 text-primary"></i>Informasi Akun
                </h6>
            </div>
            <div class="card-body p-4">
                <form>
                    {{-- Baris 1 --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->name ?? 'Nama Lengkap' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->email ?? 'email@contoh.com' }}" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2 --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Jabatan / Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-briefcase"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->role ?? 'Staff' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2 bg-light" 
                                    value="{{ Auth::user()->no_telp ?? '-' }}" readonly placeholder="Tidak ada nomor telepon">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Keamanan --}}
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-shield-halved me-2 text-success"></i>Keamanan
                    </h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-medium text-secondary small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control border-start-0 ps-2 bg-light" value="dummy12345" readonly>
                                <button class="btn btn-outline-secondary border-start-0" type="button" disabled><i class="fa-solid fa-eye-slash"></i></button>
                            </div>
                            <div class="form-text small text-muted">Password disembunyikan demi keamanan.</div>
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <a href="{{ route('admin.profile.resetPassword') }}" class="btn btn-light border w-100 text-primary fw-medium">
                                <i class="fa-solid fa-key me-1"></i> Ganti Password
                            </a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection