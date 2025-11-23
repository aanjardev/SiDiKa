<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
        }

        /* KOLOM KIRI: FORM */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
        }

        /* Styling Input agar sama dengan halaman Admin lainnya */
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

        /* Fix focus state */
        .input-group:focus-within .input-group-text {
            border-color: #86b7fe;
            background-color: #fff;
            color: #0d6efd;
        }

        .input-group:focus-within .form-control {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Toggle Password Button */
        .btn-toggle-password {
            border-left: 0;
            border-color: #dee2e6;
            background-color: #fff;
            color: #6c757d;
            z-index: 10;
        }

        .btn-toggle-password:hover {
            background-color: #f8f9fa;
            color: #0d6efd;
            border-color: #dee2e6;
        }

        .btn-toggle-password:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }

        /* KOLOM KANAN: BACKGROUND */
        /* KOLOM KANAN: PURE FOTO */
        .login-bg-side {
            min-height: 100vh;
            background-image: url('{{ asset('../mainIMG/Graphic Side.svg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body>

    {{-- Include Alerts jika ada --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 bg-white login-wrapper">
                <div class="login-card">

                    <div class="mb-4 text-center text-lg-start">
                        {{-- Logo Mobile (Opsional) --}}
                        {{-- <img src="{{ asset('mainIMG/logoDK.png') }}" alt="Logo" height="50" class="d-lg-none mb-3"> --}}

                        <h2 class="fw-bold text-dark">Welcome Back!</h2>
                        <p class="text-muted small">Silakan masuk untuk mengelola data Dinoyo Kamera.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Input Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-secondary small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="contoh@email.com"
                                    required autofocus>
                            </div>
                            @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Input Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium text-secondary small">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password"
                                    class="form-control border-end-0 @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    required>
                                <button class="btn btn-outline-secondary btn-toggle-password border-start-0" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-secondary" for="remember">
                                    Ingat Saya
                                </label>
                            </div>
                            {{-- Jika ada route forgot password --}}
                            @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-medium">Lupa Password?</a>
                            @else
                            <a href="#" class="small text-primary text-decoration-none fw-medium">Lupa Password?</a>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
                            </button>
                        </div>

                    </form>

                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-0">&copy; {{ date('Y') }} Dinoyo Kamera Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (IMAGE) --}}
            <div class="col-lg-6 d-none d-lg-flex login-bg-side"></div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Logic Toggle Password (Tidak diubah) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.querySelector("#togglePassword");
            const passwordInput = document.querySelector("#password");
            const icon = togglePassword.querySelector("i");

            togglePassword.addEventListener("click", function() {
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