{{-- ini yg lama --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="shortcut icon" href="{{ asset('mainIMG/logoDK.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Montserrat', Arial, sans-serif;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            width: 100%;
        }
        .login-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .login-logo img {
            max-width: 80px;
            height: auto;
        }
        .login-title {
            font-weight: 700;
            font-size: 1.5rem;
            text-align: center;
            color: var(--header-primary, #020405);
            margin-bottom: 0.25rem;
        }
        .login-brand {
            text-align: center;
            color: var(--header-secondary, #EA4E2D);
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--header-secondary, #EA4E2D);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background-color: #d23f1f;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <img src="{{ asset('mainIMG/logoDinoyo.png') }}" alt="Dinoyo Kamera Logo">
            </div>
            <div class="login-title">Login Admin</div>
            <div class="login-brand">Dinoyo Kamera</div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required autofocus>
                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
