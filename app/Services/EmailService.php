<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivationMail;
use App\Mail\PasswordResetMail;

class EmailService
{
    /**
     * Send account activation email
     */
    public function sendActivationEmail($user, $token)
    {
        try {
            Mail::to($user->email)->send(new AccountActivationMail($user, $token));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send activation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send verification code email
     */
    public function sendVerificationCode($user, $verificationCode)
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\VerificationCodeMail($user, $verificationCode));
            return true;
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            \Log::error('SMTP Transport Error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to send verification code email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($user, $token)
    {
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test email configuration
     */
    public function testEmailConfig()
    {
        try {
            $testEmail = 'test@example.com'; // Ganti dengan email test Anda
            Mail::raw('This is a test email from SiDiKa system.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                    ->subject('SiDiKa Email Test')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());
            return false;
        }
    }
}
