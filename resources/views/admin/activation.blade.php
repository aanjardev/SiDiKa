<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/form-validation.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
        }

        .activation-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .activation-card {
            width: 100%;
            max-width: 450px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: 0;
            color: #6c757d;
        }

        .form-control {
            background-color: #fff;
            border-left: 0;
            padding: 0.7rem 1rem;
        }

        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
            background-color: #fff;
            color: #0d6efd;
        }

        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .activation-bg-side {
            min-height: 100vh;
            background-image: url('{{ asset('../mainIMG/Graphic Side.svg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .activation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .activation-body {
            padding: 2rem;
        }
    </style>
</head>

<body>

    {{-- Include Alerts --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 bg-white activation-wrapper">
                <div class="activation-card">
                    
                    <div class="activation-header">
                        <i class="fa-solid fa-user-plus fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-2">Aktivasi Akun</h3>
                        <p class="mb-0 opacity-90">Masukkan email yang terdaftar untuk mengaktifkan akun Anda</p>
                    </div>

                    <div class="activation-body bg-light">
                        <form method="POST" action="{{ route('activation.verify-email') }}" id="activationForm">
                            @csrf

                            {{-- Input Email --}}
                            <div class="mb-4">
                                <label for="email" class="form-label fw-medium text-secondary small">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-envelope"></i>
                                    </span>
                                    <input type="email"
                                        class="form-control required-field @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') ?? session('email') }}"
                                        placeholder="contoh@email.com"
                                        required
                                        autofocus
                                        data-error-message="Email wajib diisi"
                                        data-validate="email">
                                </div>
                                <div class="invalid-feedback">
                                    @error('email')
                                        {{ $message }}
                                    @else
                                        Email wajib diisi dengan format yang benar
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Kirim Kode Verifikasi
                                </button>
                            </div>

                            {{-- Link ke Login --}}
                            <div class="text-center">
                                <small class="text-muted">
                                    Sudah punya akun? 
                                    <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">
                                        Login di sini
                                    </a>
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (IMAGE) --}}
            <div class="col-lg-6 d-none d-lg-flex activation-bg-side"></div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Form Validation JS --}}
    <script src="{{ asset('js/form-validation.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize form validation
            const activationForm = document.getElementById('activationForm');
            if (activationForm && window.FormValidator) {
                FormValidator.initForm(activationForm);
            }
        });
    </script>
</body>

</html>
