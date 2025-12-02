<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password | Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/form-validation.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --bs-primary: #3B8AFF;
            --bs-primary-rgb: 59, 138, 255;
            --bs-secondary: #0048B3;
            --bs-secondary-rgb: 0, 72, 179;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            overflow-x: hidden;
            height: 100vh;
        }

        /* KOLOM KIRI: FORM */
        .setup-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .setup-card {
            width: 100%;
            max-width: 420px;
        }

        /* Hope UI Card Styling */
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            padding: 2.5rem;
        }

        /* Hope UI Input Styling */
        .form-control {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 138, 255, 0.15);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .input-group:focus-within .input-group-text {
            background-color: #fff;
            border-color: var(--bs-primary);
            color: var(--bs-primary);
        }

        .input-group:focus-within .form-control {
            background-color: #fff;
            border-color: var(--bs-primary);
        }

        /* Toggle Password Button */
        .btn-toggle-password {
            border: 1px solid #e9ecef;
            background-color: #f8f9fa;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .btn-toggle-password:hover {
            background-color: #fff;
            color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        /* Hope UI Button Styling */
        .btn-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(59, 138, 255, 0.3);
        }

        /* KOLOM KANAN: BACKGROUND dengan SVG */
        .login-bg-side {
            min-height: 100vh;
            background: linear-gradient(135deg, #3B8AFF 0%, #0048B3 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Animated Background Elements */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            animation: float 6s ease-in-out infinite;
        }

        .bg-element-1 {
            width: 300px;
            height: 300px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .bg-element-2 {
            width: 200px;
            height: 200px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .bg-element-3 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Content di tengah background */
        .bg-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 2rem;
        }

        /* Logo Perusahaan Styling */
        .brand-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 2rem;
        }

        .company-logo {
            flex-shrink: 0;
        }

        .logo-img {
            width: 200px;
            height: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .brand-text {
            text-align: left;
        }

        .company-name {
            font-size: 5rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.9) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 10px;
        }

        .company-tagline {
            font-size: 1.5em;
            opacity: 0.8;
            margin-bottom: 40px;
            font-weight: 300;
        }

        /* Password Strength Styling */
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .login-bg-side {
                display: none;
            }
            
            .setup-wrapper {
                background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            }
            
            .auth-card {
                background: rgba(255, 255, 255, 0.98);
            }
        }

        /* Logo Styling */
        .auth-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>

<body>

    {{-- Include Alerts --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 setup-wrapper">
                <div class="setup-card">
                    <div class="auth-card">
                        {{-- Logo --}}
                        <div class="auth-logo">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        {{-- Header --}}
                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-2">Buat Password</h3>
                            <p class="text-muted mb-0">Buat password untuk akun <strong>{{ $user->email }}</strong></p>
                        </div>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('activation.setup-password', $user->activation_token) }}" id="setupForm">
                            @csrf

                            {{-- Password Input --}}
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium text-secondary small">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control required-field @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="Masukkan password"
                                        required
                                        autofocus
                                        data-error-message="Password wajib diisi"
                                        minlength="6">
                                    <button class="btn btn-toggle-password" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    @error('password')
                                        {{ $message }}
                                    @else
                                        Password wajib diisi minimal 6 karakter
                                    @enderror
                                </div>
                                <div class="password-strength" id="passwordStrength"></div>
                                <div class="form-text small text-muted mt-2">
                                    Password minimal 6 karakter. Gunakan kombinasi huruf dan angka untuk keamanan.
                                </div>
                            </div>

                            {{-- Password Confirmation --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-medium text-secondary small">
                                    Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password"
                                        class="form-control required-field @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Ulangi password"
                                        required
                                        data-error-message="Konfirmasi password wajib diisi">
                                    <button class="btn btn-toggle-password" type="button" id="togglePasswordConfirm">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    @error('password_confirmation')
                                        {{ $message }}
                                    @else
                                        Konfirmasi password harus sama dengan password
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    <i class="fa-solid fa-check-circle me-2"></i> Aktifkan Akun & Login
                                </button>
                            </div>

                            {{-- Info --}}
                            <div class="alert alert-info small">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                Setelah aktivasi, Anda akan langsung login ke sistem.
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (BACKGROUND DESIGN) --}}
            <div class="col-lg-6 d-none d-lg-flex login-bg-side">
                {{-- Animated Background Elements --}}
                <div class="bg-element bg-element-1"></div>
                <div class="bg-element bg-element-2"></div>
                <div class="bg-element bg-element-3"></div>
                
                {{-- Content di tengah --}}
                <div class="bg-content">
                    <div class="brand-container">
                        {{-- Logo Perusahaan --}}
                        <div class="company-logo">
                            <img src="{{ asset('mainIMG/logoDK.png') }}" alt="Dinoyo Kamera" class="logo-img">
                        </div>
                        
                        {{-- Brand Text --}}
                        <div class="brand-text">
                            <h1 class="company-name">SiDiKa</h1>
                            <p class="company-tagline">Sistem Informasi Dinoyo Kamera</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Form Validation JS --}}
    <script src="{{ asset('js/form-validation.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const setupForm = document.getElementById('setupForm');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('password_confirmation');
            const passwordStrength = document.getElementById('passwordStrength');
            const togglePassword = document.getElementById('togglePassword');
            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');

            if (setupForm && window.FormValidator) {
                FormValidator.initForm(setupForm);
            }

            // Toggle password visibility
            function setupTogglePassword(toggleBtn, inputField) {
                if (toggleBtn && inputField) {
                    toggleBtn.addEventListener('click', function() {
                        const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                        inputField.setAttribute('type', type);
                        
                        const icon = toggleBtn.querySelector('i');
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    });
                }
            }

            setupTogglePassword(togglePassword, passwordInput);
            setupTogglePassword(togglePasswordConfirm, passwordConfirmInput);

            // Password strength indicator
            if (passwordInput && passwordStrength) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 6) strength++;
                    if (password.length >= 10) strength++;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^a-zA-Z0-9]/.test(password)) strength++;

                    passwordStrength.className = 'password-strength';
                    
                    if (password.length === 0) {
                        passwordStrength.style.display = 'none';
                    } else {
                        passwordStrength.style.display = 'block';
                        if (strength <= 2) {
                            passwordStrength.classList.add('strength-weak');
                        } else if (strength <= 4) {
                            passwordStrength.classList.add('strength-medium');
                        } else {
                            passwordStrength.classList.add('strength-strong');
                        }
                    }
                });
            }

            // Real-time password confirmation validation
            if (passwordConfirmInput && passwordInput) {
                passwordConfirmInput.addEventListener('input', function() {
                    if (this.value !== passwordInput.value) {
                        this.setCustomValidity('Password tidak sama');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>

</html>
