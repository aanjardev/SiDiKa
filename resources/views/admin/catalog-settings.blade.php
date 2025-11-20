@extends('layouts.admin')

@section('title', 'Setting Web Katalog')

@section('content')

@php
    $R2 = env('R2_PUBLIC_URL');
@endphp

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Pengaturan Umum</h5>

                <form action="{{ route('admin.catalog-settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Website</label>
                            <input type="text" class="form-control" name="site_name"
                                   value="{{ old('site_name', $cat_setting->nama_website) }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ==================== LOGO ==================== --}}
                    <h6 class="mb-3">Logo Website</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">

                            <div class="card shadow-sm mb-3">
                                <div class="card-body p-2">

                                    @if($cat_setting->logo_path)
                                        <img src="{{ $R2 . '/' . $cat_setting->logo_path }}"
                                             alt="Logo"
                                             style="max-height: 180px;">
                                    @else
                                        <span class="text-muted">Belum ada logo</span>
                                    @endif

                                </div>
                            </div>

                            <input type="file" class="form-control" name="photo_logo" accept="image/*">

                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ==================== BANNERS ==================== --}}
                    <h6 class="mb-3">Banner Homepage</h6>
                    <div class="row">

                        <div class="col-md-8 mb-3">
                            <label class="form-label">Tambahkan Banner Baru</label>
                            <input type="file" class="form-control" name="banner" accept="image/*">
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body p-2">

                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Gambar</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($cat_banners as $index => $banner)
                                            <tr data-id="{{ $banner->id }}" data-type="banner">

                                                <td>{{ $index + 1 }}</td>

                                                <td>
                                                    <img src="{{ $R2 . '/' . $banner->banner_path }}"
                                                         style="max-height:120px;">
                                                </td>

                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                        Hapus
                                                    </button>
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

                    {{-- ==================== PARTNER ==================== --}}
                    <h6 class="mb-3">Logo Partner / Brand</h6>
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Tambahkan Logo Brand Baru</label>
                            <input type="file" class="form-control" name="brand_logos" accept="image/*">
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body p-2">

                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Gambar</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($cat_partner as $index => $partner)
                                            <tr data-id="{{ $partner->id }}" data-type="partner">

                                                <td>{{ $index + 1 }}</td>

                                                <td>
                                                    <img src="{{ $R2 . '/' . $partner->logo_path }}"
                                                         style="max-height:60px;">
                                                </td>

                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                                                        Hapus
                                                    </button>
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

                    {{-- ==================== SOSMED ==================== --}}
                    <h6 class="mb-3">Link Media Sosial</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" class="form-control" name="social_facebook"
                                   value="{{ old('social_facebook', $cat_setting->facebook_link) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" class="form-control" name="social_instagram"
                                   value="{{ old('social_instagram', $cat_setting->instagram_link) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tokopedia</label>
                            <input type="text" class="form-control" name="social_tokopedia"
                                   value="{{ old('social_tokopedia', $cat_setting->tokopedia_link) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">TikTok</label>
                            <input type="text" class="form-control" name="social_tiktok"
                                   value="{{ old('social_tiktok', $cat_setting->tiktok_link) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">YouTube</label>
                            <input type="text" class="form-control" name="social_youtube"
                                   value="{{ old('social_youtube', $cat_setting->youtube_link) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Shopee</label>
                            <input type="text" class="form-control" name="social_shopee"
                                   value="{{ old('social_shopee', $cat_setting->shopee_link) }}">
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ==================== DESKRIPSI ==================== --}}
                    <h6 class="mb-3">Info Kontak & Deskripsi</h6>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" name="contact_phone"
                                   value="{{ old('contact_phone', $cat_setting->nomor_telfon) }}">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Deskripsi Toko</label>
                            <textarea class="form-control" name="description_text" rows="3">{{ old('description_text', $cat_setting->description) }}</textarea>
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

{{-- ==================== JS Remove Logic ==================== --}}
<script>
let deletedPartnersArr = [];
let deletedBannersArr = [];

document.addEventListener("click", function(e) {
    if (e.target.classList.contains('btn-remove')) {

        let row = e.target.closest("tr");
        let id = row.dataset.id;
        let type = row.dataset.type;

        if (id && type) {
            if (type === 'partner') {
                deletedPartnersArr.push(id);
                document.getElementById("deletedPartners").value = JSON.stringify(deletedPartnersArr);
            } else {
                deletedBannersArr.push(id);
                document.getElementById("deletedBanners").value = JSON.stringify(deletedBannersArr);
            }
        }

        row.remove();
    }
});
</script>

@endsection
