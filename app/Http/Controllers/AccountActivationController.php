<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountActivationController extends Controller
{
    public function showActivationForm()
    {
        return view('admin.activation');
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar dalam sistem.'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->status === 'active') {
            return back()->withErrors([
                'email' => 'Akun ini sudah aktif. Silakan login dengan password Anda.'
            ]);
        }

        // Cek token expiry
        if ($user->token_expiry && now()->isAfter($user->token_expiry)) {
            return back()->withErrors([
                'email' => 'Token aktivasi sudah kadaluarsa. Silakan minta admin untuk mengirim ulang token aktivasi.'
            ]);
        }

        // Generate verification code (6 digit)
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code in session
        session(['activation_email' => $request->email, 'verification_code' => $verificationCode]);

        // Calculate expiry time for display
        $expiryHours = $user->token_expiry ? now()->diffInHours($user->token_expiry) : 72;

        // Send verification code via email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendVerificationCode($user, $verificationCode);
            
            if ($emailSent) {
                return redirect()->route('activation.verify-form')
                    ->with('success', 'Kode verifikasi telah dikirim ke email Anda.')
                    ->with('expiry', "Token berlaku selama {$expiryHours} jam");
            } else {
                return redirect()->route('activation.form')
                    ->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi atau hubungi admin.')
                    ->with('email', $request->email);
            }
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Email sending failed: ' . $e->getMessage());
            
            return redirect()->route('activation.form')
                ->with('error', 'Terjadi kesalahan saat mengirim email. Silakan coba lagi.')
                ->with('email', $request->email);
        }
    }

    public function showVerificationForm()
    {
        if (!session('activation_email')) {
            return redirect()->route('activation.form');
        }

        return view('admin.verification');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6'
        ]);

        $storedCode = session('verification_code');
        $email = session('activation_email');

        if ($request->verification_code !== $storedCode) {
            return back()->withErrors([
                'verification_code' => 'Kode verifikasi salah.'
            ]);
        }

        // Get user and update status
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('activation.form')
                ->withErrors(['email' => 'User tidak ditemukan. Silakan coba lagi.']);
        }

        if (!$user->activation_token) {
            return redirect()->route('activation.form')
                ->withErrors(['email' => 'Token aktivasi tidak valid. Silakan hubungi admin.']);
        }

        // Cek token expiry
        if ($user->token_expiry && now()->isAfter($user->token_expiry)) {
            return redirect()->route('activation.form')
                ->withErrors(['email' => 'Token aktivasi sudah kadaluarsa. Silakan minta admin untuk mengirim ulang token aktivasi.']);
        }
        
        return redirect()->route('activation.setup-password', ['token' => $user->activation_token]);
    }

    public function showPasswordSetupForm($token)
    {
        try {
            $user = User::where('activation_token', $token)
                        ->where('status', 'pending')
                        ->firstOrFail();

            // Cek token expiry
            if ($user->token_expiry && now()->isAfter($user->token_expiry)) {
                return redirect()->route('activation.form')
                    ->withErrors(['email' => 'Token aktivasi sudah kadaluarsa. Silakan minta admin untuk mengirim ulang token aktivasi.']);
            }

            return view('admin.setup-password', compact('user'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('activation.form')
                ->withErrors(['email' => 'Token aktivasi tidak valid atau sudah kadaluarsa. Silakan mulai ulang proses aktivasi.']);
        }
    }

    public function setupPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        try {
            $user = User::where('activation_token', $token)
                        ->where('status', 'pending')
                        ->firstOrFail();

            // Cek token expiry
            if ($user->token_expiry && now()->isAfter($user->token_expiry)) {
                return redirect()->route('activation.form')
                    ->withErrors(['email' => 'Token aktivasi sudah kadaluarsa. Silakan minta admin untuk mengirim ulang token aktivasi.']);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'status' => 'active',
                'activation_token' => null,
                'token_expiry' => null,
                'email_verified_at' => now()
            ]);

            // Clear activation session
            session()->forget(['activation_email', 'verification_code']);

            // Auto login the user
            auth()->login($user);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Akun berhasil diaktifkan! Selamat datang di sistem.');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('activation.form')
                ->withErrors(['email' => 'Token aktivasi tidak valid atau sudah kadaluarsa. Silakan mulai ulang proses aktivasi.']);
        }
    }

    public function resendCode(Request $request)
    {
        $email = session('activation_email');
        
        if (!$email) {
            return redirect()->route('activation.form');
        }

        $user = User::where('email', $email)->first();
        
        if ($user->status === 'active') {
            return redirect()->route('login')
                ->with('info', 'Akun Anda sudah aktif. Silakan login.');
        }

        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        session(['verification_code' => $verificationCode]);

        // Send new verification code via email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendVerificationCode($user, $verificationCode);
            
            if ($emailSent) {
                return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
            } else {
                return back()->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification code: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengirim ulang kode. Silakan coba lagi.');
        }
    }
}
