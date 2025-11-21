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
            overflow: hidden;
        }

        /* KOLOM KIRI: FORM */
        .login-form-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: #ffffff;
            position: relative;
        }

        /* Background pattern subtle di kiri atas */
        .login-form-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.05) 100%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            background: transparent;
            position: relative;
            z-index: 1;
        }

        .login-title {
            font-weight: 700;
            font-size: 2rem;
            color: #2F353F;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.85rem 1rem;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #4E6BFF;
            box-shadow: 0 0 0 0.25rem rgba(78, 107, 255, 0.25);
        }

        .input-group {
            position: relative;
        }
        .input-group .form-control {
            padding-right: 3.5rem;
        }
        .input-group .input-group-text {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            z-index: 10;
            border-left: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #dee2e6;
            border-left: 0;
            padding: 0 1rem;
        }
        .form-control:focus + .input-group-text {
            border-color: #4E6BFF;
        }
        .input-group-text i {
            color: #6c757d;
        }

        .form-check-input {
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .form-check-input:checked {
            background-color: #4E6BFF;
            border-color: #4E6BFF;
        }
        .form-check-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .forgot-password-link {
            color: #4E6BFF;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .forgot-password-link:hover {
            color: #3a57e8;
            text-decoration: underline;
        }

        .btn-primary {
            background-color: #4E6BFF;
            border-color: #4E6BFF;
            font-weight: 600;
            padding: 0.85rem;
            border-radius: 8px;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background-color: #3a57e8;
            border-color: #3a57e8;
        }

        /* KOLOM KANAN: BRANDING & GAMBAR */
        .login-image-side {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4facfe 100%);
            overflow: hidden;
        }

        /* Wave pattern overlay - menggunakan Graphic Side.svg jika tersedia */
        .login-image-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('mainIMG/Graphic Side.svg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.4;
            z-index: 1;
        }

        /* Alternatif: jika ingin menggunakan gambar sample sebagai background */
        /* Uncomment baris di bawah jika ingin menggunakan sample image */
        /*
        .login-image-side {
            background-image: url('{{ asset('mainIMG/sample1.png') }}');
            background-size: cover;
            background-position: center;
        }
        */

        .login-branding {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 2rem;
        }

        .login-brand-logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 2rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .login-brand-text {
            font-weight: 700;
            font-size: 2.5rem;
            color: #ffffff;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .login-brand-text .brand-line {
            display: block;
        }

        /* Layout Mobile */
        @media (max-width: 991.98px) {
            .login-image-side {
                display: none;
            }
            .login-form-side::before {
                display: none;
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
                    <h1 class="login-title">Sign In</h1>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

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

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember me?
                                </label>
                            </div>
                            <a href="#" class="forgot-password-link">Forgot Password</a>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN (BRANDING & GAMBAR) --}}
            <div class="col-lg-6 d-none d-lg-block login-image-side">
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
