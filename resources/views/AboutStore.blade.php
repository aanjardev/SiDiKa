<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - {{ $cat_setting->nama_website}}</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/AboutStore.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    @include('partials.header')

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h1 class="hero-title">
                        {{ $cat_setting->nama_website}}
                        Passion Fotografi Anda
                    </h1>
                    <p class="hero-description">Kami adalah destinasi terpercaya untuk semua kebutuhan fotografi Anda. Dengan pengalaman lebih dari 10 tahun, kami menyediakan peralatan berkualitas dan layanan profesional untuk membantu mewujudkan visi kreatif Anda.</p>
                    <div class="d-flex gap-3 py-3">
                        <a href="#about" class="btn btn-primary" style="background-color: var(--secondary-color); border: none;">Pelajari Lebih Lanjut</a>
                        <a href="#contact" class="btn btn-outline-light">Hubungi Kami</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="https://i.pinimg.com/736x/80/66/bf/8066bf936d8d61b7512c815e2f7bdcc5.jpg" alt="Camera equipment" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Values Section -->
        <section class="py-5" id="about">
            <div class="container">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Nilai Kami</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Mengapa Memilih Kami?</h2>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6" data-aos="fade-up">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/be/a0/3f/bea03f380b77cc4e04fd64072fbbdef0.jpg" alt="Reputasi" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Reputasi Terpercaya</h3>
                                        <p class="text-muted">{{ $cat_setting->nama_website}} telah menjadi komunitas penggemar kamera, menawarkan keaslian dan akurasi dalam setiap produk dan layanan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="{{ asset('mAboutIMG/1About.png') }}" alt="Kepercayaan" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kepercayaan</h3>
                                        <p class="text-muted">Dengan lebih dari 10.000 item dalam stok, kami menawarkan pilihan untuk setiap fotografer, dari pemula hingga profesional, sesuai dengan visi dan anggaran Anda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/0a/f3/29/0af329768bfdb55522a37a064af27ff9.jpg" alt="Kesetiaan" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kesetiaan</h3>
                                        <p class="text-muted">Kami menawarkan transparansi penuh dengan foto individual untuk setiap item. Tim kami siap membantu menjawab pertanyaan Anda kapan saja.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-card">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5">
                                    <img src="https://i.pinimg.com/736x/6a/9c/3b/6a9c3b87e31d4d3e76a536af1e8a6138.jpg" alt="Kualitas" class="img-fluid">
                                </div>
                                <div class="col-lg-7">
                                    <div class="card-body">
                                        <h3 class="h4">Kualitas</h3>
                                        <p class="text-muted">Setiap item diuji dengan peralatan profesional untuk memastikan kecepatan rana, pengukuran, dan akurasi eksposur memenuhi standar tinggi kami.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="feature-section" id="features">
            <div class="container">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Layanan Kami</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Apa yang Kami Tawarkan</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-camera"></i>
                            </div>
                            <h3 class="h5 mb-3">Peralatan Berkualitas</h3>
                            <p class="text-muted">Kami menyediakan berbagai peralatan fotografi berkualitas tinggi untuk memenuhi kebutuhan Anda.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <h3 class="h5 mb-3">Layanan Tambahan</h3>
                            <p class="text-muted">Layanan tambahan seperti rental kamera, jasa servis, konsultasi perlengkapan, dan pelatihan dasar fotografi untuk pemula hingga profesional.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="feature-card text-center">
                            <div class="feature-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3 class="h5 mb-3">Tim Berpengalaman</h3>
                            <p class="text-muted">Tim kami yang ramah dan berpengalaman selalu berkomitmen memberikan produk dan layanan terbaik untuk passion Anda dalam dunia visual.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-5" id="testimonials">
            <div class="container">
                <div class="text-center mb-5">
                    <p class="section-subtitle" data-aos="fade-up">Testimoni</p>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Apa Kata Mereka?</h2>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="testimonial-card text-center">
                            <img src="https://images.pexels.com/photos/21852309/pexels-photo-21852309/free-photo-of-pria-laki-laki-lelaki-kedudukan.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500" alt="Customer" class="testimonial-img rounded-circle mx-auto">
                            <h4 class="h5 mb-3">Ferdiansyah</h4>
                            <p class="text-muted">"Pelayanan di {{ $cat_setting->nama_website}} sangat memuaskan! Tim mereka sangat membantu dalam memilih peralatan yang tepat."</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="testimonial-card text-center">
                            <img src="https://static.promediateknologi.id/crop/0x0:0x0/750x500/webp/photo/p1/983/2024/11/16/unnamed-355406532.png" alt="Customer" class="testimonial-img rounded-circle mx-auto">
                            <h4 class="h5 mb-3">Rosul</h4>
                            <p class="text-muted">"Saya menyewa kamera untuk proyek fotografi dan sangat puas dengan kondisi peralatannya. Harga sewa juga terjangkau, dan stafnya ramah sekali!"</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="testimonial-card text-center">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDLcaoMqauKXsMZwJOio8tds2bjfB3WK3HnQ&s" alt="Customer" class="testimonial-img rounded-circle mx-auto">
                            <h4 class="h5 mb-3">Imtiaz Hussain</h4>
                            <p class="text-muted">"{{ $cat_setting->nama_website}} adalah tempat terbaik untuk servis kamera di Malang. Kamera saya yang bermasalah jadi seperti baru lagi setelah diperbaiki di sini."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="about-banner" id="contact">
            <div class="about-banner-content">
                <div class="container text-center">
                    <h2 class="display-4 fw-bold mb-4" data-aos="fade-up">Siap Memulai Perjalanan Fotografi Anda?</h2>
                    <p class="lead mb-5" data-aos="fade-up" data-aos-delay="100">Hubungi kami sekarang untuk konsultasi gratis dan temukan solusi terbaik untuk kebutuhan fotografi Anda.</p>
                    <a href="/contact"  class="btn btn-primary btn-lg" style="background-color: var(--secondary-color); border: none;" data-aos="fade-up" data-aos-delay="200">Mulai Sekarang</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('partials.footer')
    @include('partials.floating-wa')

    <!-- Back to Top -->
    <button id="backToTop" class="btn">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/about.js') }}"></script>
</body>
</html>
