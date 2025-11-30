<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\EmailService;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profilPengguna');
    }
    
    public function resetPassword()
    {
        return view('admin.profilPenggunaResetPassword');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            // Check if request is AJAX
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama yang Anda masukkan salah!'
                ]);
            }
            return back()->with('error', 'Password lama yang Anda masukkan salah!');
        }

        if (Hash::check($request->new_password, $user->password)) {
            // Check if request is AJAX
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password baru tidak boleh sama dengan password lama!'
                ]);
            }
            return back()->with('error', 'Password baru tidak boleh sama dengan password lama!');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Check if request is AJAX
        if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui!'
            ]);
        }

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    // ========== PUBLIC FORGOT PASSWORD (FROM LOGIN PAGE) ==========
    
    public function showPublicForgotPasswordForm()
    {
        return view('admin.public-forgot-password');
    }

    public function publicForgotPassword(Request $request)
    {
        // Validasi email
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'Email tidak terdaftar dalam sistem.');
        }

        // Generate verification code (6 digit)
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code in session
        session([
            'password_reset_email' => $user->email,
            'password_reset_code' => $verificationCode,
            'password_reset_expiry' => now()->addMinutes(30), // 30 minutes expiry
            'password_reset_type' => 'public' // Mark as public flow
        ]);
        
        // Ensure session is saved
        session()->save();

        // Send verification code via email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendPasswordResetCode($user, $verificationCode);
            
            if ($emailSent) {
                // Log success for debugging
                \Log::info('Public password reset code sent successfully to: ' . $user->email);
                
                // Check if request is AJAX
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Kode verifikasi telah dikirim ke email Anda.',
                        'redirect' => route('public.verify-reset-code')
                    ]);
                }
                
                return redirect()->route('public.verify-reset-code')
                    ->with('success', 'Kode verifikasi telah dikirim ke email Anda.')
                    ->with('reset_email', $user->email)
                    ->with('expiry', 'Kode berlaku selama 30 menit');
            } else {
                \Log::error('Email service returned false for public user: ' . $user->email);
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengirim kode verifikasi. Silakan coba lagi.'
                    ]);
                }
                return back()->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            \Log::error('Exception in publicForgotPassword: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ]);
            }
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function showPublicVerifyResetCodeForm()
    {
        if (!session('password_reset_email') || session('password_reset_type') !== 'public') {
            return redirect()->route('login');
        }

        return view('admin.public-verify-reset-code');
    }

    public function publicVerifyResetCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6'
        ], [
            'verification_code.required' => 'Kode verifikasi wajib diisi',
            'verification_code.digits' => 'Kode verifikasi harus 6 digit'
        ]);

        // Check session
        $storedEmail = session('password_reset_email');
        $storedCode = session('password_reset_code');
        $expiry = session('password_reset_expiry');

        if (!$storedEmail || !$storedCode || !$expiry) {
            return back()->with('error', 'Sesi verifikasi telah kadaluarsa. Silakan mulai ulang.');
        }

        // Check expiry
        if (now()->gt($expiry)) {
            // Clear session
            session()->forget(['password_reset_email', 'password_reset_code', 'password_reset_expiry', 'password_reset_type']);
            return back()->with('error', 'Kode verifikasi telah kadaluarsa. Silakan minta kode baru.');
        }

        // Verify code
        if ($request->verification_code !== $storedCode) {
            return back()->with('error', 'Kode verifikasi tidak valid. Silakan periksa kembali.');
        }

        // Code is valid, redirect to reset password form
        return redirect()->route('public.reset-password')
            ->with('success', 'Kode verifikasi valid. Silakan buat password baru.');
    }

    public function showPublicResetPasswordForm()
    {
        if (!session('password_reset_email') || session('password_reset_type') !== 'public') {
            return redirect()->route('login');
        }

        return view('admin.public-reset-password');
    }

    public function publicResetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai'
        ]);

        $email = session('password_reset_email');
        
        if (!$email) {
            return back()->with('error', 'Sesi telah kadaluarsa. Silakan mulai ulang.');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan. Silakan hubungi administrator.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Clear session
        session()->forget(['password_reset_email', 'password_reset_code', 'password_reset_expiry', 'password_reset_type']);

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    public function resendPublicResetCode(Request $request)
    {
        $email = session('password_reset_email');
        
        if (!$email) {
            return redirect()->route('password.request');
        }

        // Get user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        // Generate new code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update session
        session([
            'password_reset_code' => $verificationCode,
            'password_reset_expiry' => now()->addMinutes(30)
        ]);

        // Send email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendPasswordResetCode($user, $verificationCode);
            
            if ($emailSent) {
                return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
            } else {
                return back()->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    // ========== PROTECTED FORGOT PASSWORD (FROM PROFILE PAGE) ==========

    public function showForgotPasswordForm()
    {
        return view('admin.profile-forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        // Validasi email
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem'
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->with('error', 'Email tidak terdaftar dalam sistem.');
        }

        // Generate verification code (6 digit)
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code in session
        session([
            'password_reset_email' => $user->email,
            'password_reset_code' => $verificationCode,
            'password_reset_expiry' => now()->addMinutes(30), // 30 minutes expiry
            'password_reset_type' => 'protected' // Mark as protected flow
        ]);
        
        // Ensure session is saved
        session()->save();

        // Send verification code via email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendPasswordResetCode($user, $verificationCode);
            
            if ($emailSent) {
                // Log success for debugging
                \Log::info('Password reset code sent successfully to: ' . $user->email);
                
                // Check if request is AJAX
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Kode verifikasi telah dikirim ke email Anda.',
                        'redirect' => route('admin.profile.verify-reset-code')
                    ]);
                }
                
                return redirect()->route('admin.profile.verify-reset-code')
                    ->with('success', 'Kode verifikasi telah dikirim ke email Anda.')
                    ->with('reset_email', $user->email)
                    ->with('expiry', 'Kode berlaku selama 30 menit');
            } else {
                \Log::error('Email service returned false for user: ' . $user->email);
                if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengirim kode verifikasi. Silakan coba lagi.'
                    ]);
                }
                return back()->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            \Log::error('Exception in forgotPassword: ' . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile());
            if ($request->header('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ]);
            }
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function showVerifyResetCodeForm()
    {
        if (!session('password_reset_email')) {
            return redirect()->route('admin.profile');
        }

        return view('admin.verify-reset-code');
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6'
        ]);

        $storedCode = session('password_reset_code');
        $email = session('password_reset_email');
        $expiry = session('password_reset_expiry');

        if ($request->verification_code !== $storedCode) {
            return back()->withErrors([
                'verification_code' => 'Kode verifikasi salah.'
            ]);
        }

        if (now()->isAfter($expiry)) {
            return redirect()->route('admin.profile')
                ->with('error', 'Kode verifikasi sudah kadaluarsar. Silakan coba lagi.');
        }

        // Clear verification session and redirect to reset form
        session()->forget(['password_reset_code', 'password_reset_expiry']);
        
        return redirect()->route('admin.profile.reset-forgotten-password');
    }

    public function resetForgottenPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        $email = session('password_reset_email');
        
        if (!$email) {
            return redirect()->route('admin.profile')
                ->with('error', 'Sesi reset password tidak valid. Silakan coba lagi.');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user || $user->id !== Auth::id()) {
            return redirect()->route('admin.profile')
                ->with('error', 'User tidak valid. Silakan coba lagi.');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Clear reset session
        session()->forget('password_reset_email');

        return redirect()->route('admin.profile')
            ->with('success', 'Password berhasil direset! Gunakan password baru untuk login berikutnya.');
    }

    public function showResetForgottenPasswordForm()
    {
        if (!session('password_reset_email')) {
            return redirect()->route('admin.profile');
        }

        return view('admin.reset-forgotten-password');
    }

    public function resendResetCode(Request $request)
    {
        $email = session('password_reset_email');
        
        if (!$email) {
            return redirect()->route('admin.profile');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user || $user->id !== Auth::id()) {
            return redirect()->route('admin.profile');
        }

        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        session([
            'password_reset_code' => $verificationCode,
            'password_reset_expiry' => now()->addMinutes(30)
        ]);

        // Send new verification code via email
        try {
            $emailService = new EmailService();
            $emailSent = $emailService->sendPasswordResetCode($user, $verificationCode);
            
            if ($emailSent) {
                return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
            } else {
                return back()->with('error', 'Gagal mengirim kode verifikasi. Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to resend password reset code: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengirim ulang kode. Silakan coba lagi.');
        }
    }
}
