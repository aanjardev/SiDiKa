@php
$setting = \App\Models\CatalogSettings::first();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animation Libraries -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/typeit@8.7.1/dist/index.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/mainPage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="{{ asset('js/loadingScreen.js') }}"></script>
    <script type="module" src="{{ asset('js/productHover.js') }}"></script>
    <script type="module" src="{{ asset('js/scrollNavigation.js') }}"></script>
</head>
<body>
    @include('partials.header')

        <!-- Promotion Carousel -->
        <section id="promotion" class="py-4" data-aos="fade-up">
            <div class="container">
                <div id="demo" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach ($cat_banners as $index => $banner)
                            <button type="button" data-bs-target="#demo" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index == 0 ? 'active' : '' }}" 
                                aria-current="{{ $index == 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach ($cat_banners as $index => $banner)
                            @php
                                $path = $banner->banner_url;
                                $url = $path;
                                $alt = basename($path);
                            @endphp
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <img src="{{ $url }}" alt="{{ $alt }}" class="d-block w-100">
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </section>

    <!-- Categories -->
    <section id="Kategori" class="py-4" data-aos="fade-up" data-aos-delay="100">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="section-title mb-0">Kategori</h2>
            <a href="/katalog" class="text-decoration-none">Lihat Selengkapnya</a>
        </div>
    </section>
    <section id="Kategori-Display" class="py-4" data-aos="fade-up" data-aos-delay="150">
        <div class="container">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 1]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/1.png') }}" alt="Kamera DSLR" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 2]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/2.png') }}" alt="Kamera Mirrorless" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 3]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/3.png') }}" alt="Kamera Digital" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 4]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/4.png') }}" alt="Handycam" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 5]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/5.png') }}" alt="Kamera Instan" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 6]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/6.png') }}" alt="Kamera Lain" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 7]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/7.png') }}" alt="Lensa" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 8]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/8.png') }}" alt="Baterai/Charger" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 9]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/9.png') }}" alt="Kartu Memori" class="category-image">
                    </a>
                </div>
                <div class="col">
                    <a href="{{ route('product.index', ['kategori' => 10]) }}" class="category-link">
                        <img src="{{ asset('mCategoryIMG/10.png') }}" alt="Aksesoris Lain" class="category-image">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="Kategori2" class="py-4" data-aos="fade-up">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="section-title mb-0">Produk Terbaru</h2>
            <a href="{{ route('product.index', ['sort' => 'terbaru']) }}" class="text-decoration-none">Lihat Selengkapnya</a>
        </div>
    </section>

    <section class="py-4" data-aos="fade-up" data-aos-delay="200">
        <div class="container">
            @if ($latestProducts->isEmpty())
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="bi bi-box display-1 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada produk terbaru saat ini.</p>
                </div>
            @else
                <div class="d-flex justify-content-center row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                    @foreach ($latestProducts as $index => $product)
                        <div class="col" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                <div class="product-card @if ($product->stok_produk == 0) sold-out @endif">
                                    <div class="product-image-wrapper">
                                        @if ($product->grade === 'Unggulan')
                                            <span class="featured-badge">
                                                <i class="bi bi-star-fill me-1"></i>Unggulan
                                            </span>
                                        @endif
                                        @if ($product->stok_produk == 0)
                                                <span class="sold-out-badge">SOLD OUT</span>
                                        @endif
                                        @if ($product->gambarUtama)
                                            <img src="{{ $product->gambarUtama->url }}"
                                                 alt="{{ $product->nama_produk }}"
                                                 class="product-image"
                                                 loading="lazy">
                                        @else
                                            <img src="{{ asset('images/placeholder.jpg') }}"
                                                 alt="No Image"
                                                 class="product-image">
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title" title="{{ $product->nama_produk }}">
                                            {{ $product->nama_produk }}
                                        </h3>
                                        <p class="product-price mb-0">
                                            Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section id="Kategori2" class="py-4" data-aos="fade-up">
        <div class="container d-flex justify-content-between align-items-center">
            <h2 class="section-title mb-0">Rekomendasi</h2>
            <a href="{{ route('product.index', ['sort' => 'rekomendasi']) }}" class="text-decoration-none">Lihat Selengkapnya</a>
        </div>
    </section>

    <section class="py-4" data-aos="fade-up" data-aos-delay="250">
        <div class="container">
            @if ($produkUnggulan->isEmpty())
                <div class="text-center py-5" data-aos="fade-up">
                    <i class="bi bi-box display-1 text-muted"></i>
                    <p class="text-muted mt-3">Tidak ada produk rekomendasi saat ini.</p>
                </div>
            @else
                <div class="d-flex justify-content-center row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                    @foreach ($produkUnggulan as $index => $product)
                        <div class="col" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <a href="{{ route('product.show', $product->id) }}" class="text-decoration-none">
                                <div class="product-card @if ($product->stok_produk == 0) sold-out @endif">
                                    <div class="product-image-wrapper">
                                        @if ($product->grade === 'Unggulan')
                                            <span class="featured-badge">
                                                <i class="bi bi-star-fill me-1"></i>Unggulan
                                            </span>
                                        @endif
                                        @if ($product->stok_produk == 0)
                                                <span class="sold-out-badge">SOLD OUT</span>
                                        @endif
                                        @if ($product->gambarUtama)
                                            <img src="{{ $product->gambarUtama->url }}"
                                                 alt="{{ $product->nama_produk }}"
                                                 class="product-image"
                                                 loading="lazy">
                                        @else
                                            <img src="{{ asset('images/placeholder.jpg') }}"
                                                 alt="No Image"
                                                 class="product-image">
                                        @endif
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title" title="{{ $product->nama_produk }}">
                                            {{ $product->nama_produk }}
                                        </h3>
                                        <p class="product-price mb-0">
                                            Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Brands -->
    <section id="Brand" class="py-4" data-aos="fade-up">
        <div class="container">
            <h2 class="section-title">Brands</h2>
        </div>
    </section>
    <section id="Kategori-Display-Brand" class="py-4" data-aos="fade-up" data-aos-delay="300">
        <div class="container">
            <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-3">
            @foreach ($cat_partner as $index => $partner)
                @php
                    $path = $partner->url;
                    $url = $path;
                    $alt = basename($path);
                @endphp
                <div class="col">
                    <img src="{{ $url }}" alt="{{ $alt }}" class="img-fluid">
                </div>
            @endforeach
            </div>
        </div>
    </section>

    <!-- YouTube Section -->
    <section class="youtube-section" data-aos="fade-up" data-aos-delay="350">
        <div class="container">
            <div class="youtube-title">
                <h2>{{$cat_setting->nama_website}} Channel</h2>
                <p>Temukan tips dan review produk terbaru dari tim kami di channel YouTube {{$cat_setting->nama_website}}</p>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="youtube-container">
                        <iframe src="https://www.youtube.com/embed/3o2IrQrb7ws?si=t5g5UGYrifjmhKxD" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits -->
    <section id="Benefit" class="pt-4" data-aos="fade-up" data-aos-delay="400">
        <div class="container">
            <h2 class="section-title">Benefit</h2>
        </div>
    </section>
    <section id="Benefit-Item" class="py-4" data-aos="fade-up" data-aos-delay="450">
        <div class="container">
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3 text-center">
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/100___shopping_with_trust___comfort_Benefit_Customer-02.png" alt="Terpercaya" class="img-fluid mb-2">
                    <h2 class="benefit-title">100% Terpercaya</h2>
                </div>
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Tax-Free.png" alt="Bebas Pajak" class="img-fluid mb-2">
                    <h2 class="benefit-title">Bebas Pajak</h2>
                </div>
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Good_Experience_Shopping_at_Focus_Nusantara_Benefit_Customer-09.png" alt="Pengalaman Terbaik" class="img-fluid mb-2">
                    <h2 class="benefit-title">Pengalaman Terbaik</h2>
                </div>
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/quality___friendly_service_Benefit_Customer-04.png" alt="Pelayanan Baik" class="img-fluid mb-2">
                    <h2 class="benefit-title">Kualitas Pelayanan Baik</h2>
                </div>
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/GPP_Guaranteed_3_Years_Benefit_Customer-05.png" alt="Bergaransi" class="img-fluid mb-2">
                    <h2 class="benefit-title">Bergaransi</h2>
                </div>
                <div class="col">
                    <img src="https://admin.focusnusantara.com/media/wysiwyg/benefit/compressed_img/Free_Fast___Safe_to_Destination_Benefit_Customer-07.png" alt="Aman Sampai Tujuan" class="img-fluid mb-2">
                    <h2 class="benefit-title">Aman Sampai Tujuan</h2>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('partials.footer')

    <!-- WhatsApp Float Icon -->
    @include('partials.floating-wa')

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typeit@8.7.1/dist/index.umd.min.js"></script>
    <script src="{{ asset('js/animations.js') }}"></script>
</body>
</html>
