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
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $cat_setting->nama_website) }}" placeholder="Contoh: Dinoyo Kamera">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Logo Website</h6>
                    <div class="row">
                        <label class="form-label">Ganti Logo</label>
                        <div class="col-md-6 mb-3">
                            <!-- <label class="form-label">Logo Utama</label> -->
                            <div class="mb-3" style="display: inline-block;">
                                <div class="card shadow-sm">
                                    <div class="card-body p-2">
                                        @php
                                            $path = $cat_setting->logo_path;
                                            $url = Str::startsWith($path, 'photos/') ? asset('storage/' . $path) : asset($path);
                                        @endphp
                                        <img src="{{ $url }}" alt="Logo" style="max-height: 180px; display: block;">
                                    </div>
                                </div>
                            </div>

                            <input type="file" class="form-control @error('logo') is-invalid @enderror" name="photo_logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Banner Homepage</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Tambahkan Foto untuk Banner Utama</label>
                            <input type="file" class="form-control @error('banner') is-invalid @enderror" name="banner" accept="image/*">
                            @error('banner')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="card shadow-sm">
                            <div class="card-body p-2">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>Gambar</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cat_banners as $index => $banner)
                                            <tr data-id="{{ $banner->id }}" data-type="banner">
                                                <!-- Nomor -->
                                                <td>{{ $index + 1 }}</td>

                                                <!-- Gambar -->
                                                <td>
                                                    @php
                                                        $path = $banner->banner_path;
                                                        if (Str::startsWith($path, 'photos/')) {
                                                            $url = asset('storage/' . $path); // dari storage
                                                        } else {
                                                            $url = asset($path); // misal mainIMG/...
                                                        }
                                                    @endphp
                                                    <img src="{{ $url }}" alt="Banner {{ $index + 1 }}" style="max-height: 120px;">
                                                </td>

                                                <!-- Aksi -->
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="deleted_banners" id="deletedBanners">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Logo Partner / Brand</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tambahkan Logo Brand Baru</label>
                            <input type="file" class="form-control @error('brand_logo_1') is-invalid @enderror" name="brand_logos" accept="image/*">
                            @error('brand_logo_1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="card shadow-sm">
                            <div class="card-body p-2">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>Gambar</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cat_partner as $index => $partner)
                                            <tr tr data-id="{{ $partner->id }}" data-type="partner">
                                                <!-- Nomor -->
                                                <td>{{ $index + 1 }}</td>

                                                <!-- Gambar -->
                                                <td>
                                                    <img 
                                                    src="
                                                        @if (Str::startsWith($partner->logo_path, 'http'))
                                                            {{ $partner->logo_path }}
                                                        @else
                                                            {{ asset('storage/' . $partner->logo_path) }}
                                                        @endif
                                                    "
                                                    alt="Logo {{ $index + 1 }}" 
                                                    style="max-height: 60px;">

                                                </td>

                                                <!-- Aksi -->
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="deleted_partners" id="deletedPartners">
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Link Media Sosial</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" class="form-control @error('social_facebook') is-invalid @enderror" name="social_facebook" value="{{ old('social_facebook', $cat_setting->facebook_link) }}" placeholder="https://facebook.com/...">
                            @error('social_facebook')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" class="form-control @error('social_instagram') is-invalid @enderror" name="social_instagram" value="{{ old('social_instagram', $cat_setting->instagram_link) }}" placeholder="https://instagram.com/...">
                            @error('social_instagram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tokopedia</label>
                            <input type="text" class="form-control @error('social_tokopedia') is-invalid @enderror" name="social_tokopedia" value="{{ old('social_tokopedia', $cat_setting->tokopedia_link) }}" placeholder="https://www.tokopedia.com/...">
                            @error('social_whatsapp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">TikTok</label>
                            <input type="text" class="form-control @error('social_tiktok') is-invalid @enderror" name="social_tiktok" value="{{ old('social_tiktok', $cat_setting->tiktok_link) }}" placeholder="https://www.tiktok.com/@...">
                            @error('social_tiktok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">YouTube</label>
                            <input type="text" class="form-control @error('social_youtube') is-invalid @enderror" name="social_youtube" value="{{ old('social_youtube', $cat_setting->youtube_link) }}" placeholder="https://www.youtube.com/...">
                            @error('social_youtube')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shopee</label>
                            <input type="text" class="form-control @error('social_shopee') is-invalid @enderror" name="social_shopee" value="{{ old('social_shopee', $cat_setting->shopee_link) }}" placeholder="https://shopee.co.id/...">
                            @error('social_shopee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <!-- <img src="{{ asset('storage/photos/8bcYNbeT6PZIL6jt3udnaAo3HckI9bzwJXMknLAn.png') }}" alt="Foto"> -->

                    <hr class="my-4">

                    <h6 class="mb-3">Info Kontak</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone', $cat_setting->nomor_telfon) }}" placeholder="Contoh: 0812-xxxx-xxxx">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <hr class="my-4">
                        <div class="col-12 mb-3">
                            <label class="form-label">Deskripsi Toko</label>
                            <textarea class="form-control @error('description_text') is-invalid @enderror" name="description_text" rows="3" placeholder="Tulis Deskripsi Toko">{{ old('description_text', $cat_setting->description) }}</textarea>
                            @error('description_text')
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
<script>
// Array untuk menyimpan ID yang dihapus
    let deletedPartnersArr = [];
    let deletedBannersArr = [];

    document.addEventListener("click", function(e) {
        if (e.target.classList.contains('btn-remove')) {
            let row = e.target.closest("tr");
            let id = row.getAttribute("data-id");
            let type = row.getAttribute("data-type"); // 'partner' atau 'banner'

            if(id && type) {
                if(type === 'partner') {
                    deletedPartnersArr.push(id);
                    document.getElementById("deletedPartners").value = JSON.stringify(deletedPartnersArr);
                } else if(type === 'banner') {
                    deletedBannersArr.push(id);
                    document.getElementById("deletedBanners").value = JSON.stringify(deletedBannersArr);
                }
            }
            // Hapus row dari tampilan
            row.remove();
        }
    });
</script>
@endsection

