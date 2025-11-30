<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SiDiKa</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">

    {{-- Bootstrap CSS --}}
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
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .login-card {
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

        .btn-outline-primary {
            color: var(--bs-primary);
            border-color: var(--bs-primary);
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
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

        /* Responsive */
        @media (max-width: 991px) {
            .login-bg-side {
                display: none;
            }
            
            .login-wrapper {
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

        /* Form Label Styling */
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Link Styling */
        .text-primary {
            color: var(--bs-primary) !important;
            text-decoration: none;
            font-weight: 500;
        }

        .text-primary:hover {
            text-decoration: underline;
        }

        /* Password Strength Indicator */
        .progress {
            height: 6px;
        }

        .progress-bar {
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .progress-bar.bg-danger { background-color: #dc3545 !important; }
        .progress-bar.bg-warning { background-color: #ffc107 !important; }
        .progress-bar.bg-info { background-color: #17a2b8 !important; }
        .progress-bar.bg-success { background-color: #28a745 !important; }

        .form-control.is-valid {
            border-color: #198754;
            background-color: #f0fff4;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-color: #fff5f5;
        }
    </style>
</head>

<body>

    {{-- Include Alerts jika ada --}}
    @include('partials.alerts')

    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="col-lg-6 login-wrapper">
                <div class="login-card">
                    <div class="auth-card">
                        
                        <div class="auth-logo">
                            <i class="fas fa-unlock-alt"></i>
                        </div>

                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark mb-2">Buat Password Baru</h2>
                            <p class="text-muted small">Verifikasi berhasil! Silakan buat password baru</p>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-shield-check fa-2x text-success"></i>
                            </div>
                            <p class="text-muted small mb-0">
                                Email: <strong>{{ session('reset_email') }}</strong>
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.profile.reset-forgotten-password.post') }}" class="needs-validation" novalidate>
                            @csrf
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           placeholder="Masukkan password baru" 
                                           required 
                                           minlength="6"
                                           autocomplete="new-password">
                                    <button class="btn btn-toggle-password" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text text-muted">
                                    Minimal 6 karakter
                                </div>
                                <div class="invalid-feedback">
                                    Password minimal 6 karakter
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           placeholder="Ulangi password baru" 
                                           required 
                                           minlength="6"
                                           autocomplete="new-password">
                                    <button class="btn btn-toggle-password" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Konfirmasi password harus sama
                                </div>
                            </div>

                            <!-- Password Strength Indicator -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Kekuatan Password:</span>
                                    <span id="strengthText" class="small fw-bold">-</span>
                                </div>
                                <div class="progress">
                                    <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Simpan Password Baru
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <div class="border-top pt-3">
                                <a href="{{ route('admin.profile') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Kembali ke Profil
                                </a>
                            </div>
                        </div>
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

    {{-- Logic Password --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Toggle password visibility
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPasswordInput = document.getElementById('new_password');
            const newIcon = toggleNewPassword.querySelector('i');
            
            toggleNewPassword.addEventListener('click', function() {
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    newIcon.classList.remove('fa-eye-slash');
                    newIcon.classList.add('fa-eye');
                } else {
                    newIcon.classList.remove('fa-eye');
                    newIcon.classList.add('fa-eye-slash');
                }
            });

            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('new_password_confirmation');
            const confirmIcon = toggleConfirmPassword.querySelector('i');
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                
                if (type === 'password') {
                    confirmIcon.classList.remove('fa-eye-slash');
                    confirmIcon.classList.add('fa-eye');
                } else {
                    confirmIcon.classList.remove('fa-eye');
                    confirmIcon.classList.add('fa-eye-slash');
                }
            });

            // Password strength checker
            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 6) strength++;
                if (password.length >= 10) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;
                
                const strengthBar = document.getElementById('strengthBar');
                const strengthText = document.getElementById('strengthText');
                
                // Remove all color classes
                strengthBar.className = 'progress-bar';
                
                switch(strength) {
                    case 0:
                    case 1:
                        strengthBar.style.width = '20%';
                        strengthBar.classList.add('bg-danger');
                        strengthText.textContent = 'Sangat Lemah';
                        strengthText.className = 'small fw-bold text-danger';
                        break;
                    case 2:
                        strengthBar.style.width = '40%';
                        strengthBar.classList.add('bg-warning');
                        strengthText.textContent = 'Lemah';
                        strengthText.className = 'small fw-bold text-warning';
                        break;
                    case 3:
                        strengthBar.style.width = '60%';
                        strengthBar.classList.add('bg-info');
                        strengthText.textContent = 'Sedang';
                        strengthText.className = 'small fw-bold text-info';
                        break;
                    case 4:
                        strengthBar.style.width = '80%';
                        strengthBar.classList.add('bg-success');
                        strengthText.textContent = 'Kuat';
                        strengthText.className = 'small fw-bold text-success';
                        break;
                    case 5:
                        strengthBar.style.width = '100%';
                        strengthBar.classList.add('bg-success');
                        strengthText.textContent = 'Sangat Kuat';
                        strengthText.className = 'small fw-bold text-success';
                        break;
                }
            }

            newPasswordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });

            // Password confirmation validation
            confirmPasswordInput.addEventListener('input', function() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword === newPassword && confirmPassword.length > 0) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else if (confirmPassword.length > 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });

            // Initialize form validation
            const resetForm = document.querySelector('form');
            if (resetForm && window.FormValidator) {
                FormValidator.initForm(resetForm);
            }

            // Add subtle animations
            const authCard = document.querySelector('.auth-card');
            if (authCard) {
                authCard.style.animation = 'slideInUp 0.6s ease-out';
            }
        });

        // Add slideInUp animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
