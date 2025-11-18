<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - {{ $cat_setting->nama_website}}</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/contactStore.css') }}">
</head>
<body>
    <!-- Navigation -->
    @include('partials.header')

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="section-title">Hubungi Kami</h1>
                <p class="section-subtitle">{{ $cat_setting->nama_website}} dengan senang hati menerima dukungan dan pertanyaan Anda. Kami akan berusaha untuk menjawab pertanyaan Anda segera setelah kami menerimanya.</p>
            </div>

            <!-- Store Locations -->
            <div class="row g-4">
                <!-- Dinoyo Kamera 1 -->
                <div class="col-lg-4">
                    <div class="location-card">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.6400010975767!2d112.6038165!3d-7.932615299999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78821258558e79%3A0x329993e281b05187!2sDinoyo%20Kamera!5e0!3m2!1sid!2sid!4v1747554465125!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="card-body">
                            <h3 class="store-name">Dinoyo Kamera 1</h3>
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <div class="store-hours">Setiap Hari - 10.00 - 20.00 WIB</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <a href="tel:+6282345670014" class="text-decoration-none text-dark">+62 823-4567-0014</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    RUKO TLOGOMAS SQUARE kav 9 lantai 1, Tlogomas, Kec Lowokwaru, Kota Malang, Jawa Timur, 65144
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dinoyo Kamera 2 -->
                <div class="col-lg-4">
                    <div class="location-card">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.557519855936!2d112.63811319999999!3d-7.941193300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6291d8fa42637%3A0xc8390745e1480bc!2sJual%20beli%20kamera%20bekas%20indonesia!5e0!3m2!1sid!2sid!4v1747554514731!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="card-body">
                            <h3 class="store-name">Dinoyo Kamera 2</h3>
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <div class="store-hours">Rabu - Senin (08.00 - 18.00)</div>
                                    <div class="store-closed">Selasa Tutup</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <a href="tel:+6281230551552" class="text-decoration-none text-dark">+62 812-3055-1552</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    Jl. C.Trowulan No.65B, Mojolangu, Kec. Lowokwaru, Kota Malang, Jawa Timur 65142
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toko Kamera Pasuruan -->
                <div class="col-lg-4">
                    <div class="location-card">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7908.479203849883!2d112.9089841886383!3d-7.657367309665906!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7c7118f8ee425%3A0x2e6af9e52800eab1!2sToko%20Kamera%20Pasuruan%20%2F%20Studio%20Foto%20Pasuruan%20%2F%20Servis%20Kamera%20%2F%20Uno%20Studio%20Pasuruan%20%2F%20Self%20Studio%20%2F%20Pas%20Foto!5e0!3m2!1sid!2sid!4v1747554559733!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="card-body">
                            <h3 class="store-name">Toko Kamera Pasuruan</h3>
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <div class="store-hours">Setiap Hari - 10.00 - 20.00 WIB</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <a href="tel:+6282345670014" class="text-decoration-none text-dark">+62 823-4567-0014</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    Jl. Sunan Ampel, Petamanan, Kec. Bugul Kidul, Kota Pasuruan, Jawa Timur
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <h2 class="text-center mb-4">Kirim Pesan</h2>
                        <form id="whatsappContactForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" name="name" class="form-label">Nama Lengkap*</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="name" required>
                                        <div class="invalid-feedback">Mohon isi nama lengkap Anda</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email*</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" required>
                                        <div class="invalid-feedback">Mohon isi alamat email yang valid</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Pesan*</label>
                                    <textarea class="form-control" id="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">Mohon isi pesan Anda</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-submit w-100">
                                        <i class="bi bi-send me-2"></i>
                                        Kirim Pesan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- WhatsApp Float Icon -->
    @include('partials.floating-wa')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

    document.getElementById('whatsappContactForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = this;

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const message = document.getElementById('message').value;

        const phoneNumber = '62895411200308'; // Ganti dengan nomor WhatsApp Anda!

        // Buat teks pesan
        const whatsappMessage = `
Halo Dinoyo Kamera!%0A
Saya *${encodeURIComponent(name)}*, ingin bertanya:%0A%0A
*Pesan:*%0A${encodeURIComponent(message)}%0A%0A
*Email:* ${encodeURIComponent(email)}%0A%0A
        `.trim();

        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${whatsappMessage}`;

        window.open(whatsappUrl, '_blank');

        form.reset();
        form.classList.remove('was-validated');
    });
    </script>
</body>
</html>
