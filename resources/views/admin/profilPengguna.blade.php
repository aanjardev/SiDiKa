@extends('layouts.admin')

@section('title', 'Profil Pengguna')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold mb-0">INFORMASI UMUM</h5>
                    <a href="#" class="btn btn-primary px-4">EDIT</a>
                </div>

                <form action="#" method="POST">
                    @csrf
                    {{-- @method('PUT') --}}

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label fw-medium">Nama Lengkap</label>
                                <input type="text" class="form-control border-2" id="nama_lengkap"
                                    value="Masukkan Nama Lengkap" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <input type="email" class="form-control border-2" id="email"
                                    value="Contoh: @dinoyokamera.com" disabled>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label fw-medium">Jabatan</label>
                                <input type="text" class="form-control border-2" id="jabatan"
                                    value="Misal: Staff Operasional" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="no_telp" class="form-label fw-medium">Nomor Telepon</label>
                                <input type="text" class="form-control border-2" id="no_telp"
                                    value="Masukkan nomor telepon aktif" disabled>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-semibold mb-3">KEAMANAN</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="d-flex gap-2">
                                    <input type="password" class="form-control border-2" id="password"
                                        value="********" disabled>
                                    <a href="#" class="btn btn-outline-primary">Reset Password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    </form>
                
                <div class="mt-4">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4">LOGOUT</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection