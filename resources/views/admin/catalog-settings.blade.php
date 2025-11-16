@extends('layouts.admin')

@section('title', 'Setting Web Katalog')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Pengaturan Umum</h5>
                <form action="{{ route('admin.catalog-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_name" class="form-label">Nama Website</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $setting->site_name) }}" placeholder="Contoh: Dinoyo Kamera">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Logo Website</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo Utama</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->logo_path)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Preview saat ini:</small>
                                    <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Banner Homepage</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Banner Utama</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" accept="image/*">
                            @error('banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->banner_path)
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Preview saat ini:</small>
                                    <img src="{{ asset('storage/' . $setting->banner_path) }}" alt="Banner" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Logo Partner / Brand</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Logo Brand 1</label>
                            <input type="file" class="form-control @error('brand_logo_1') is-invalid @enderror" name="brand_logo_1" accept="image/*">
                            @error('brand_logo_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->brand_logo_1_path)
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/' . $setting->brand_logo_1_path) }}" alt="Brand 1" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Logo Brand 2</label>
                            <input type="file" class="form-control @error('brand_logo_2') is-invalid @enderror" name="brand_logo_2" accept="image/*">
                            @error('brand_logo_2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->brand_logo_2_path)
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/' . $setting->brand_logo_2_path) }}" alt="Brand 2" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Logo Brand 3</label>
                            <input type="file" class="form-control @error('brand_logo_3') is-invalid @enderror" name="brand_logo_3" accept="image/*">
                            @error('brand_logo_3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->brand_logo_3_path)
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/' . $setting->brand_logo_3_path) }}" alt="Brand 3" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Logo Brand 4</label>
                            <input type="file" class="form-control @error('brand_logo_4') is-invalid @enderror" name="brand_logo_4" accept="image/*">
                            @error('brand_logo_4')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($setting->brand_logo_4_path)
                                <div class="mt-2 text-center">
                                    <img src="{{ asset('storage/' . $setting->brand_logo_4_path) }}" alt="Brand 4" style="max-height: 60px;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Link Media Sosial</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" class="form-control @error('social_facebook') is-invalid @enderror" name="social_facebook" value="{{ old('social_facebook', $setting->social_facebook) }}" placeholder="https://facebook.com/...">
                            @error('social_facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" class="form-control @error('social_instagram') is-invalid @enderror" name="social_instagram" value="{{ old('social_instagram', $setting->social_instagram) }}" placeholder="https://instagram.com/...">
                            @error('social_instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" class="form-control @error('social_whatsapp') is-invalid @enderror" name="social_whatsapp" value="{{ old('social_whatsapp', $setting->social_whatsapp) }}" placeholder="https://wa.me/...">
                            @error('social_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TikTok</label>
                            <input type="text" class="form-control @error('social_tiktok') is-invalid @enderror" name="social_tiktok" value="{{ old('social_tiktok', $setting->social_tiktok) }}" placeholder="https://www.tiktok.com/@...">
                            @error('social_tiktok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">YouTube</label>
                            <input type="text" class="form-control @error('social_youtube') is-invalid @enderror" name="social_youtube" value="{{ old('social_youtube', $setting->social_youtube) }}" placeholder="https://www.youtube.com/...">
                            @error('social_youtube')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Info Kontak & Alamat</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" placeholder="Contoh: 0812-xxxx-xxxx">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" placeholder="Contoh: info@dinoyokamera.com">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control @error('address_text') is-invalid @enderror" name="address_text" rows="3" placeholder="Tulis alamat toko / cabang utama">{{ old('address_text', $setting->address_text) }}</textarea>
                            @error('address_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

