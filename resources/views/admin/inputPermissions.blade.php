@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')

<form action="{{ isset($user) ? route('admin.permissions.update', $user->id) : route('admin.permissions.store') }}" method="POST">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    {{-- Nama Karyawan --}}
    <div class="mb-3">
        <label class="form-label">Nama Karyawan</label>
        <select name="karyawan_name" class="form-select form-select-sm" style="height: 40px;" required {{ isset($user) ? 'disabled' : '' }}>
            <option selected disabled value="">Pilih karyawan</option>
            @foreach ($karyawan_data as $k)
                <option value="{{ $k->id }}" 
                    {{ (old('karyawan_name') == $k->id || (isset($user) && $user->id == $k->id)) ? 'selected' : '' }}>
                    {{ $k->nama_lengkap }}
                </option>
            @endforeach
        </select>
        @if(isset($user))
            {{-- Input hidden untuk mengirim id saat edit --}}
            <input type="hidden" name="karyawan_name" value="{{ $user->id }}">
        @endif
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', isset($user) ? $user->email : '') }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password"
           name="password"
           id="password" 
           class="form-control @error('password') is-invalid @enderror" 
           style="height: 40px;" 
           value="{{ old('password', isset($user) ? '' : 'admin123') }}"
           placeholder="{{ isset($user) ? 'Kosongkan jika tidak ingin mengganti' : '' }}">
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
        </div>
    <div class="mb-3 form-check"> 
        <input type="checkbox" class="form-check-input" id="exampleCheck1" checked> 
        <label class="form-check-label" for="exampleCheck1">Lihat password</label> 
    </div>

    {{-- Submit --}}
    <div class="text-end mt-4">
        <a href="{{ route('admin.permissions') }}" class="btn btn-light me-2">Batal</a>
        <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Perbarui' : 'Simpan' }}</button>
    </div>
</form>
<script>
    const pass = document.getElementById('password');
    const checkbox = document.getElementById('exampleCheck1');

    // Set kondisi awal sesuai checkbox (karena default-nya checked)
    pass.type = checkbox.checked ? "text" : "password";

    checkbox.addEventListener("change", () => {
    pass.type = checkbox.checked ? "text" : "password";
});
</script>

@endsection
