<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Kita butuh Font Awesome untuk ikon mata (eye) --}}
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
            overflow: hidden; /* Mencegah scroll di layout utama */
        }

        /* LAYOUT BARU (SISI KANAN): GAMBAR
          (Sesuai permintaan Anda, gambar di kanan)
        */
        .login-image-side {
            background-image: url('{{ asset('mainIMG/bg-login-admin.jpg') }}'); /* <-- GANTI DENGAN PATH GAMBAR ANDA */
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        }

        /* LAYOUT BARU (SISI KIRI): FORM */
        .login-form-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: #ffffff; /* Background putih untuk form */
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            background: transparent;
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .login-logo img {
            max-width: 60px;
            height: auto;
        }

        .login-brand-title {
            font-weight: 700;
            font-size: 1.75rem;
            color: #2F353F;
            line-height: 1.2;
        }

        .login-greeting {
            font-weight: 700;
            font-size: 2rem;
            color: #2F353F;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            border-radius: 8px !important; /* Paksa radius */
            padding: 0.85rem 1rem;
        }
        .form-control:focus {
            border-color: #4E6BFF;
            box-shadow: 0 0 0 0.25rem rgba(78, 107, 255, 0.25);
        }

        /* FIX TOGGLE PRESISI:
          Menggunakan Input Group agar ikon pas di tengah
        */
        .input-group {
            position: relative;
        }
        .input-group .form-control {
            /* Pastikan input password tidak tumpang tindih dengan tombol */
            padding-right: 3.5rem;
        }
        .input-group .input-group-text {
            /* Ini membuat tombol pas di dalam input */
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
            border-left: none;
            background: transparent;
            border-radius: 8px; /* Samakan dengan form-control */
            cursor: pointer;
            border: 1px solid #dee2e6; /* Samakan border */
            border-left: 0;
        }
        .form-control:focus + .input-group-text {
             /* Style saat input-nya fokus */
            border-color: #4E6BFF;
        }
        .input-group-text i {
            color: #6c757d;
        }

        .btn-primary {
            background-color: #4E6BFF;
            border-color: #4E6BFF;
            font-weight: 600;
            padding: 0.85rem;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background-color: #3a57e8;
            border-color: #3a57e8;
        }

        /* Layout Mobile */
        @media (max-width: 991.98px) {
            .login-image-side {
                display: none; /* Sembunyikan kolom gambar di mobile */
            }
        }
    </style>
</head>
<body>
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 col-md-12 login-form-side">
                <div class="login-card">

                    <div class="login-logo">
                        <img src="{{ asset('mainIMG/logoDinoyo.png') }}" alt="Dinoyo Kamera Logo">
                        <h1 class="login-brand-title">Dinoyo Kamera</h1>
                    </div>

                    <h2 class="login-greeting">Welcome, Admin!</h2>
                    <p class="login-subtitle">Please login to continue.</p>

                    <form method="POST" action="{{ route('login') }}"> {{-- --}}
                        @csrf

                        {{-- PERUBAHAN: Input 'email' (bukan 'username' atau 'login') --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- PERBAIKAN: Input Password dengan Input Group --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <button type="button" class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN (GAMBAR) - Otomatis hilang di mobile --}}
            <div class="col-lg-6 d-none d-lg-block login-image-side">
                {{-- Gambar diatur via CSS background-image --}}
            </div>

        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JavaScript untuk Toggle Password --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.querySelector("#togglePassword");
            const passwordInput = document.querySelector("#password");
            const icon = togglePassword.querySelector("i");

            togglePassword.addEventListener("click", function () {
                const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
                passwordInput.setAttribute("type", type);

                if (type === "password") {
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                } else {
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                }
            });
        });
    </script>
</body>
</html>
