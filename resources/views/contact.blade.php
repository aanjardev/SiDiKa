<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - {{ $cat_setting->nama_website}}</title>
    <link rel="shortcut icon" href="{{ $cat_setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">

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
                @foreach($branches as $branch)
                <div class="col-lg-4 col-md-6">
                    <div class="location-card h-100 d-flex flex-column">
                        <div class="map-container">
                            <iframe src="{{ $branch['embed'] }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h3 class="store-name">{{ $branch['nama'] }}</h3>
                            <div class="info-item">
                                <i class="bi bi-clock"></i>
                                <div>
                                    <button class="btn p-0 text-start d-inline-flex align-items-center justify-content-between w-300" style="margin-left:-2px;"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#jamLengkap{{ $loop->index }}"
                                        aria-expanded="false"
                                        aria-controls="jamLengkap{{ $loop->index }}">
                                        <span class="fw-semibold text-dark">Hari ini ({{ $branch['jam']['hari_ini']['hari'] }}): {{ $branch['jam']['hari_ini']['slot'] }}</span>
                                        <i class="fas fa-chevron-down small toggle-icon text-secondary" style="margin-left:10px; margin-top:-2px;"></i>
                                    </button>
                                    @if($branch['jam']['catatan'])
                                        <div class="store-closed text-muted small">{{ $branch['jam']['catatan'] }}</div>
                                    @endif
                                    <div class="collapse mt-1" id="jamLengkap{{ $loop->index }}">
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($branch['jam']['harian'] as $hari)
                                            <li class="d-flex justify-content-between">
                                                <span class="text-muted">{{ $hari['hari'] }}</span>
                                                <span class="text-end">{{ $hari['slot'] }}</span>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/','', $branch['telepon']) }}" class="text-decoration-none text-dark">{{ $branch['telepon'] }}</a>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    {{ $branch['alamat'] }}
                                </div>
                            </div>
                            <div class="mt-auto pt-2">
                                <a href="{{ $branch['link_maps'] ?? $branch['embed'] }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-map"></i> Buka di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
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
