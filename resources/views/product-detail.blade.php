<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $setting = $cat_setting ?? \App\Models\CatalogSettings::first();
    @endphp
    <title>{{ $produk->nama_produk }} - Detail Produk - {{ $setting?->nama_website ?? 'Katalog' }}</title>
    <link rel="shortcut icon" href="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/mainPage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detail-produk.css') }}">
    <script type="module" src="{{ asset('js/loadingScreen.js') }}"></script>
    <script type="module" src="{{ asset('js/productHover.js') }}"></script>
    <script type="module" src="{{ asset('js/scrollNavigation.js') }}"></script>
    @php
        $rawPhone = preg_replace('/\D+/', '', $setting?->nomor_telfon ?? '');
        if ($rawPhone && str_starts_with($rawPhone, '0')) {
            $rawPhone = '62' . substr($rawPhone, 1);
        } elseif ($rawPhone && str_starts_with($rawPhone, '8')) {
            $rawPhone = '62' . $rawPhone;
        }

        $storeName = $setting?->nama_website ?? 'Toko';
        $pesan = "Halo {$storeName}, saya tertarik dengan produk *{$produk->nama_produk}* (SKU: {$produk->kode_sku}). Apakah produk ini masih tersedia dan bisakah saya mendapatkan informasi lebih lanjut?";
        $linkWA = $rawPhone ? ("https://wa.me/{$rawPhone}?text=" . urlencode($pesan)) : null;

        // Get main image and other images
        $mainImage = $produk->gambar->firstWhere('is_main', true);
        $otherImages = $produk->gambar->filter(function($g) { return !$g->is_main; });

        // If no main image is set, use the first image as main
        if (!$mainImage && $produk->gambar->isNotEmpty()) {
            $mainImage = $produk->gambar->first();
            $otherImages = $produk->gambar->skip(1);
        }

        // Combine main image with other images for gallery
        $galleryImages = $mainImage ? collect([$mainImage])->concat($otherImages) : collect();
        $galleryImages = $galleryImages->values();
        $galleryImageUrls = $galleryImages->map(fn($g) => $g->url);
        $mainImageUrl = $galleryImageUrls->first();
    @endphp

<style>
    /* Product Detail Page Styles */
:root {
    --primary-color: #2F353F;
    --secondary-color: #EA4E2D;
    --text-color: #333333;
    --gray-100: #f8f9fa;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #6c757d;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    --transition-base: all 0.3s ease;
}

#product-detail {
    background-color: var(--gray-100);
    padding: 2rem 0;
}

/* Back Button */
.btn-back {
    color: var(--gray-700);
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: var(--transition-base);
}

.btn-back:hover {
    background-color: var(--gray-200);
    color: var(--primary-color);
}

/* Search Bar */
.input-group {
    border: 1px solid var(--gray-300);
    border-radius: 8px;
    overflow: hidden;
}

.input-group-text {
    background-color: white;
    border: none;
    color: var(--gray-600);
    padding-left: 1rem;
}

.input-group .form-control {
    border: none;
    padding: 0.75rem 1rem;
}

.input-group .form-control:focus {
    box-shadow: none;
}

.action-btn {
    height: 48px;
    padding: 0.55rem 1rem;
    border-radius: 10px;
    font-weight: 600;
}

.action-search .input-group-text,
.action-search .form-control {
    height: 48px;
}

/* Product Images */
.carousel,
.carousel-inner,
.carousel-item {
    aspect-ratio: 1/1;
    width: 100%;
    max-width: 400px;
    margin: 0 auto;
    background: #fff;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-item {
    min-height: 0;
    height: auto;
}

.main-image {
    width: 100%;
    height: 100%;
    aspect-ratio: 1/1;
    object-fit: contain;
    background-color: white;
    /* padding: 1rem; */
    max-width: 100%;
    max-height: 100%;
    margin: 0 auto;
    display: block;
}

.carousel-control-prev,
.carousel-control-next {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    top: 50%;
    transform: translateY(-50%);
    margin: 0 1rem;
}

/* Thumbnails */
.thumbnails-container {
    background: white;
    padding: 0.75rem;
    border-radius: 12px;
    margin-top: 1rem;
    box-shadow: var(--shadow-sm);
    width: 100%;
    overflow: hidden;
}

.thumbnails-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: thin;
    scrollbar-color: var(--gray-300) transparent;
}
.thumbnails-scroll::-webkit-scrollbar {
    height: 6px;
}
.thumbnails-scroll::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 8px;
}
.thumbnails-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.thumbnail {
    border-radius: 8px;
    border: 2px solid transparent;
    transition: var(--transition-base);
    background: white;
    padding: 0.25rem;
    width: 80px;
    height: 80px;
    flex: 0 0 auto;
    object-fit: cover;
}

.thumbnail:hover {
    border-color: var(--secondary-color);
    transform: translateY(-2px);
}

.thumbnail.active {
    border-color: var(--secondary-color);
}

/* Product Info */
.product-info {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--shadow-sm);
}

.product-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.product-price {
    font-size: 2rem;
    font-weight: 700;
    color: var(--secondary-color);
    margin-bottom: 1.5rem;
}

.product-meta {
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--gray-700);
}

.meta-label {
    font-weight: 600;
    min-width: 100px;
}

.stock-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.stock-status.in-stock {
    background-color: #d1fae5;
    color: #065f46;
}

.stock-status.out-of-stock {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Description */
.description {
    color: var(--gray-700);
    line-height: 1.8;
    position: relative;
    overflow: hidden;
    transition: var(--transition-base);
}

.description.collapsed {
    max-height: var(--desc-collapsed-height, 324px); /* default fallback */
}

.toggle-text {
    display: inline-block;
    color: var(--secondary-color);
    font-weight: 600;
    cursor: pointer;
    margin-top: 1rem;
    transition: var(--transition-base);
}

.toggle-text:hover {
    color: var(--primary-color);
}

/* Floating WhatsApp Button */
.floating-wa {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background-color: #25D366;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
    z-index: 1000;
}

.floating-wa:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    color: white;
}

.floating-wa i {
    font-size: 1.25rem;
}

/* Responsive Design */
@media (max-width: 991px) {
    .product-info {
        margin-top: 1.5rem;
    }
}

@media (max-width: 768px) {
    .product-title {
        font-size: 1.5rem;
    }

    .product-price {
        font-size: 1.75rem;
    }

    .product-info {
        padding: 1.5rem;
    }
}

@media (max-width: 576px) {
    #product-detail {
        padding: 1rem 0;
    }

    .floating-wa {
        bottom: 1rem;
        right: 1rem;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }

    .carousel,
    .carousel-inner,
    .carousel-item,
    .main-image {
        max-width: 250px;
        max-height: 250px;
    }

    .thumbnail {
        width: 64px;
        height: 64px;
    }

    .thumbnails-container {
        padding: 0.5rem 0.25rem;
    }
}

/* Custom WhatsApp Button for Product Detail */
.btn-custom-whatsapp {
    background-color: var(--secondary-color); /* Menggunakan warna dari variabel Anda */
    color: white; /* Teks putih */
    border-color: var(--secondary-color); /* Warna border sesuai background */
    transition: all 0.3s ease; /* Transisi halus untuk efek hover */
}

.btn-custom-whatsapp:hover {
    background-color: darken(var(--secondary-color), 10%); /* Menggelapkan warna sedikit saat hover */
    border-color: darken(var(--secondary-color), 10%); /* Border juga ikut gelap */
    transform: translateY(-2px); /* Sedikit naik saat di-hover */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Menambah bayangan */
}

/* Jika Anda menggunakan SCSS/SASS, fungsi darken() akan bekerja.
   Jika Anda hanya menggunakan CSS biasa, Anda perlu menghitung nilai darken secara manual:
   Warna asli: #EA4E2D
   Warna yang lebih gelap (sekitar 10%): #C53E24 (gunakan color picker online untuk hasil presisi)
*/
/* Contoh jika hanya pakai CSS biasa: */
.btn-custom-whatsapp:hover {
    background-color: #C53E24; /* Nilai warna yang sudah sedikit gelap */
    border-color: #C53E24;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Fade animation for product image */
.fade-image {
    transition: opacity 0.4s cubic-bezier(0.4,0,0.2,1);
    opacity: 1;
}
.fade-image.fade-out {
    opacity: 0;
}

/* Gallery navigation buttons - only show on hover */
.product-image-preview:hover .gallery-nav-btn {
    opacity: 1;
    pointer-events: auto;
}
.gallery-nav-btn {
    opacity: 0.7;
    pointer-events: auto;
    background: rgba(30,30,30,0.12);
    border: none;
    color: #fff;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    z-index: 2;
    transition: background 0.2s, box-shadow 0.2s, opacity 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.product-image-preview:hover .gallery-nav-btn,
.gallery-nav-btn:focus {
    opacity: 1;
}
.gallery-nav-btn:hover {
    background: rgba(234,78,45,0.7);
    color: #fff;
    opacity: 1;
}
.gallery-nav-btn.left { left: 10px; }
.gallery-nav-btn.right { right: 10px; }

/* Sliding animation + zoom + blur */
.slide-image {
    position: absolute;
    left: 0; top: 0; right: 0; bottom: 0;
    width: 100%; height: 100%;
    opacity: 1;
    transition: transform 0.45s cubic-bezier(0.4,0,0.2,1), opacity 0.3s, filter 0.3s;
    z-index: 1;
    filter: blur(0px) scale(1);
}
.slide-image.slide-out-left,
.slide-image.slide-out-right {
    opacity: 0;
    filter: blur(4px) scale(0.98);
}
.slide-image.slide-in-left,
.slide-image.slide-in-right {
    opacity: 1;
    filter: blur(4px) scale(1.04);
    z-index: 2;
}
.slide-image.active {
    transform: translateX(0);
    opacity: 1;
    z-index: 3;
    filter: blur(0px) scale(1.04);
    box-shadow: 0 8px 32px rgba(30,30,30,0.10);
    animation: zoomInActive 0.4s cubic-bezier(0.4,0,0.2,1);
}
@keyframes zoomInActive {
    0% { filter: blur(4px) scale(0.98); }
    100% { filter: blur(0px) scale(1.04); }
}

.product-image-preview {
    position: relative;
    width: 100%;
    max-width: 420px;
    height: 400px;
    margin: 0 auto 1.5rem auto;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (min-width: 992px) {
    .product-image-preview {
        max-width: 420px !important;
        height: 420px;
    }
    .product-image-preview .main-image {
        max-width: 420px !important;
        max-height: 420px !important;
    }
}
@media (max-width: 576px) {
    .product-image-preview {
        max-width: 250px;
        height: 250px;
    }
}

</style>
</head>
<body>
    @include('partials.header')

    <section id="product-detail" style="padding-top: 50px; padding-bottom:50px" >
    <div class="container">
        <div class="row align-items-center gy-2 mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 flex-nowrap">
                    <a href="javascript:history.back()" class="btn btn-light border d-flex align-items-center gap-1 action-btn">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <form method="GET" action="{{ route('product.index') }}" class="flex-grow-1 flex-shrink-1">
                        <div class="input-group action-search w-100">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <!-- KIRI: Gambar Carousel -->
            <div class="col-lg-6 mb-3">
                <div class="product-image-preview text-center mb-3" id="galleryPreview" style="position:relative;">
                    <img id="mainProductImage"
                         src="{{ $mainImageUrl ?? asset('images/placeholder.jpg') }}"
                         alt="{{ $produk->nama_produk }}"
                         class="main-image fade-image img-fluid"
                         style="max-width: 400px; max-height: 400px; aspect-ratio: 1/1; object-fit: contain; border-radius: 12px; background: #fff;">
                </div>
                <div class="d-flex align-items-center justify-content-center gap-2 mt-2 w-100" style="position:relative;">
                    <button type="button" class="gallery-nav-btn left me-2" aria-label="Sebelumnya" onclick="switchThumb(-1)" id="galleryPrevBtn"><i class="bi bi-chevron-left"></i></button>
                    <div class="thumbnails-container mb-0 flex-grow-1">
                        <div class="d-flex flex-nowrap gap-2 thumbnails-scroll" id="thumbsRow">
                        @foreach ($galleryImages as $index => $gambar)
                            <img src="{{ $gambar->url }}"
                                alt="{{ $produk->nama_produk }}"
                                class="img-fluid thumbnail{{ $index === 0 ? ' active' : '' }}"
                                onclick="switchProductImage({{ $index }})"
                                data-index="{{ $index }}">
                        @endforeach
                        </div>
                    </div>
                    <button type="button" class="gallery-nav-btn right ms-2" aria-label="Selanjutnya" onclick="switchThumb(1)" id="galleryNextBtn"><i class="bi bi-chevron-right"></i></button>
                </div>
            </div>

            <!-- KANAN: Detail Produk -->
            <div class="col-lg-6">
                <h2>{{ $produk->nama_produk }}</h2>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <p class="text-danger fw-bold fs-4 mb-2">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</p>
                    {{-- TOMBOL PESAN SEKARANG DI SINI --}}
                    @if ($produk->stok_produk > 0)
                        @if ($linkWA)
                            <a href="{{ $linkWA }}" class="btn btn-lg flex-shrink-0 btn-custom-whatsapp" target="_blank" rel="noopener noreferrer">
                                 Pesan Sekarang
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg flex-shrink-0" disabled>
                                WhatsApp belum disetel
                            </button>
                        @endif
                    @else
                        <button class="btn btn-secondary btn-lg flex-shrink-0" disabled>
                            <i class="bi bi-x-circle me-2"></i> Stok Habis
                        </button>
                    @endif

                </div>
                <p class="text-muted mb-2"><strong>Kode SKU:</strong> {{ $produk->kode_sku }}</p>
                <p class="text-muted mb-2"><strong>Kategori:</strong> {{ $produk->kategori->nama_kategori }}</p>
                <p class="text-muted mb-2"><strong>Status:</strong> {{ $produk->status }}</p>
                <p class="text-muted mb-2">
                    <strong>Stok:</strong>
                    @if ($produk->stok_produk > 0)
                        <span class="text-success">{{ $produk->stok_produk }} tersedia</span>
                    @else
                        <span class="text-danger">Stok habis</span>
                    @endif
                </p>
                <p class="text-muted mb-2"><strong>Deskripsi Produk:</strong></p>
                <p id="descriptionText" class="description collapsed">
                    {!! nl2br(e($produk->deskripsi_produk)) !!}
                </p>
                <span id="toggleText" class="toggle-text" style="display: none;">Baca selengkapnya</span>

                {{-- Tambahkan detail lainnya di sini --}}
            </div>
        </div>
    </div>
</section>


        <!-- Footer -->
    @include('partials.footer')
    @include('partials.floating-wa')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.galleryImages = @json($galleryImageUrls->toArray());
    </script>
    <script src="{{ asset('js/productDetailGallery.js') }}"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Gallery function (existing code)
    let currentGalleryIndex = 0;
    const galleryImages = @json($galleryImageUrls->toArray());
    const mainImg = document.getElementById('mainProductImage');
    const thumbs = document.querySelectorAll('.thumbnail');
    const prevBtn = document.getElementById('galleryPrevBtn');
    const nextBtn = document.getElementById('galleryNextBtn');

    function setMainImage(idx) {
        if(idx === currentGalleryIndex) return;
        mainImg.classList.add('fade-out');
        setTimeout(() => {
            mainImg.src = galleryImages[idx];
            mainImg.classList.remove('fade-out');
        }, 300);
        thumbs.forEach((t,i) => t.classList.toggle('active', i === idx));
        currentGalleryIndex = idx;
        updateNavBtn();
    }

    window.switchProductImage = setMainImage;

    window.switchThumb = function(dir) {
        let next = currentGalleryIndex + dir;
        if(next < 0) next = galleryImages.length - 1;
        if(next >= galleryImages.length) next = 0;
        setMainImage(next);
        const activeThumb = document.querySelector(`.thumbnail[data-index="${next}"]`);
        activeThumb?.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }

    function updateNavBtn() {
        if (galleryImages.length <= 1) {
            prevBtn.setAttribute('disabled', 'disabled');
            nextBtn.setAttribute('disabled', 'disabled');
        } else {
            prevBtn.removeAttribute('disabled');
            nextBtn.removeAttribute('disabled');
        }
    }
    updateNavBtn();

    // Toggle deskripsi hanya jika melebihi batas
    const desc = document.getElementById("descriptionText");
    const toggle = document.getElementById("toggleText");

    if (desc && toggle) {
        const collapsedHeight = 120; // px
        const needsToggle = desc.scrollHeight > collapsedHeight + 5;

        if (needsToggle) {
            toggle.style.display = "inline";
            toggle.style.color = "#007bff";
            toggle.style.cursor = "pointer";
            toggle.style.fontWeight = "500";
            toggle.style.marginTop = "10px";

            desc.style.maxHeight = collapsedHeight + "px";
            desc.style.overflow = "hidden";
            desc.style.transition = "max-height 0.3s ease";
            desc.style.maskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
            desc.style.webkitMaskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";

            toggle.addEventListener("click", function() {
                const isCollapsed = desc.style.maxHeight === collapsedHeight + "px";
                if (isCollapsed) {
                    desc.style.maxHeight = desc.scrollHeight + "px";
                    desc.style.maskImage = "none";
                    desc.style.webkitMaskImage = "none";
                    toggle.textContent = "Tampilkan lebih sedikit";
                } else {
                    desc.style.maxHeight = collapsedHeight + "px";
                    desc.style.maskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
                    desc.style.webkitMaskImage = "linear-gradient(to bottom, black 80%, transparent 100%)";
                    toggle.textContent = "Baca selengkapnya";
                }
            });
        } else {
            toggle.style.display = "none";
            desc.style.maxHeight = "none";
            desc.style.maskImage = "none";
            desc.style.webkitMaskImage = "none";
        }
    }
});
</script>
</body>
</html>
