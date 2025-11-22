@extends('layouts.admin')

@section('title', 'Setting Web Katalog')

@push('page-actions')
    {{-- Tombol Simpan di Atas (Opsional, menggunakan JS untuk submit form) --}}
    <button type="button" onclick="document.getElementById('settingsForm').submit()" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-save fa-fw"></i>
        <span>Simpan Perubahan</span>
    </button>
@endpush

@section('content')
<form action="{{ route('admin.catalog-settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
    @csrf
    
    <div class="row">
        {{-- KOLOM KIRI: Informasi Umum, Kontak, & Sosmed --}}
        <div class="col-lg-7 mb-4">
            
            {{-- Card: Identitas & Kontak --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Identitas & Kontak</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="site_name" class="form-label fw-medium small text-muted">Nama Website</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $cat_setting->nama_website) }}" placeholder="Contoh: Dinoyo Kamera">
                        @error('site_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Nomor Telepon / WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-brands fa-whatsapp text-success"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="contact_phone" value="{{ old('contact_phone', $cat_setting->nomor_telfon) }}" placeholder="0812-xxxx-xxxx">
                        </div>
                        @error('contact_phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-medium small text-muted">Deskripsi Toko (Footer)</label>
                        <textarea class="form-control" name="description_text" rows="4" placeholder="Tulis deskripsi singkat tentang toko...">{{ old('description_text', $cat_setting->description) }}</textarea>
                        @error('description_text') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Card: Media Sosial --}}
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-share-nodes me-2 text-primary"></i>Link Media Sosial</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-instagram text-danger"></i></span>
                                <input type="text" class="form-control" name="social_instagram" value="{{ old('social_instagram', $cat_setting->instagram_link) }}" placeholder="https://instagram.com/...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-facebook text-primary"></i></span>
                                <input type="text" class="form-control" name="social_facebook" value="{{ old('social_facebook', $cat_setting->facebook_link) }}" placeholder="https://facebook.com/...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-tiktok text-dark"></i></span>
                                <input type="text" class="form-control" name="social_tiktok" value="{{ old('social_tiktok', $cat_setting->tiktok_link) }}" placeholder="https://tiktok.com/@...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">YouTube</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-brands fa-youtube text-danger"></i></span>
                                <input type="text" class="form-control" name="social_youtube" value="{{ old('social_youtube', $cat_setting->youtube_link) }}" placeholder="https://youtube.com/...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Tokopedia</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-store text-success"></i></span>
                                <input type="text" class="form-control" name="social_tokopedia" value="{{ old('social_tokopedia', $cat_setting->tokopedia_link) }}" placeholder="Link Tokopedia">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-muted">Shopee</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-solid fa-bag-shopping" style="color: #ff4000ff"></i></span>
                                <input type="text" class="form-control" name="social_shopee" value="{{ old('social_shopee', $cat_setting->shopee_link) }}" placeholder="Link Shopee">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Aset Visual (Logo, Banner, Partner) --}}
        <div class="col-lg-5 mb-4">
            
            {{-- Card: Logo Utama --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-regular fa-image me-2 text-primary"></i>Logo Website</h6>
                </div>
                <div class="card-body text-center">
                    <div class="bg-light rounded-3 p-3 mb-3 border border-dashed d-flex align-items-center justify-content-center" style="min-height: 150px;">
                        @php
                            $path = $cat_setting->logo_path;
                            $url = Str::startsWith($path, 'photos/') ? asset('storage/' . $path) : asset($path);
                        @endphp
                        <img src="{{ $url }}" alt="Logo Utama" class="img-fluid" style="max-height: 100px;">
                    </div>
                    <input type="file" class="form-control form-control-sm" name="photo_logo" accept="image/*">
                    <div class="form-text small text-muted">Format: PNG/JPG. Max: 2MB.</div>
                </div>
            </div>

            {{-- Card: Banner Homepage --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-panorama me-2 text-primary"></i>Banner Slider</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Upload Banner Baru</label>
                        <input type="file" class="form-control form-control-sm" name="banner" accept="image/*">
                    </div>

                    <div class="table-responsive border rounded-3">
                        <table class="table table-modern table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60%">Gambar</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cat_banners as $index => $banner)
                                <tr data-id="{{ $banner->id }}" data-type="banner">
                                    <td class="p-2">
                                        @php
                                            $path = $banner->banner_path;
                                            $url = Str::startsWith($path, 'photos/') ? asset('storage/' . $path) : asset($path);
                                        @endphp
                                        <img src="{{ $url }}" class="rounded shadow-sm w-100" style="height: 60px; object-fit: cover;">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn-action btn-action-delete btn-remove mx-auto" title="Hapus Banner">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="deleted_banners" id="deletedBanners">
                </div>
            </div>

            {{-- Card: Logo Partner --}}
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-handshake me-2 text-primary"></i>Logo Brand/Partner</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-muted">Upload Logo Baru</label>
                        <input type="file" class="form-control form-control-sm" name="brand_logos" accept="image/*">
                    </div>

                    <div class="table-responsive border rounded-3" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-modern table-sm mb-0">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th style="width: 60%">Logo</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cat_partner as $index => $partner)
                                <tr data-id="{{ $partner->id }}" data-type="partner">
                                    <td class="p-2 align-middle">
                                        <div class="bg-light rounded p-1 text-center border border-dashed">
                                            <img src="{{ Str::startsWith($partner->logo_path, 'http') ? $partner->logo_path : asset('storage/' . $partner->logo_path) }}" 
                                                 class="img-fluid" 
                                                 style="max-height: 40px;">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn-action btn-action-delete btn-remove mx-auto" title="Hapus Logo">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <input type="hidden" name="deleted_partners" id="deletedPartners">
                </div>
            </div>

        </div>
    </div>
    
    {{-- Tombol Simpan Mobile / Bottom --}}
    <div class="d-block d-md-none fixed-bottom bg-white border-top p-3 shadow-lg">
        <button type="submit" class="btn btn-primary w-100">Simpan Semua Pengaturan</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Logic Penghapusan (Sama seperti sebelumnya, hanya selector disesuaikan)
    let deletedPartnersArr = [];
    let deletedBannersArr = [];

    document.addEventListener("click", function(e) {
        // Mencari tombol btn-remove atau parentnya (karena ada icon <i> di dalamnya)
        const btn = e.target.closest('.btn-remove');
        
        if (btn) {
            let row = btn.closest("tr");
            let id = row.getAttribute("data-id");
            let type = row.getAttribute("data-type");

            if(id && type) {
                if(type === 'partner') {
                    deletedPartnersArr.push(id);
                    document.getElementById("deletedPartners").value = JSON.stringify(deletedPartnersArr);
                } else if(type === 'banner') {
                    deletedBannersArr.push(id);
                    document.getElementById("deletedBanners").value = JSON.stringify(deletedBannersArr);
                }
                
                // Efek fade out sebelum hapus
                row.style.transition = "all 0.3s";
                row.style.opacity = "0";
                setTimeout(() => row.remove(), 300);
            }
        }
    });
</script>
@endpush

