<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    private const VALID_EMAIL = 'admin@dinoyokamera.com';
    private const VALID_PASSWORD = 'admin123';
    private const INVALID_EMAIL = 'invalid-email';
    private const WRONG_PASSWORD = 'wrong-password';

    /**
     * Positive: kredensial valid harus mengarah ke dashboard admin.
     * Langkah: buka halaman login -> isi email & password benar -> submit -> pastikan URL /admin/dashboard.
     */
    public function testLoginBerhasilDenganKredensialValid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginPage($browser);

            $this->submitLogin($browser, self::VALID_EMAIL, self::VALID_PASSWORD);

            $browser->waitForLocation('/admin/dashboard', 10)
                    ->assertPathIs('/admin/dashboard');
        });
    }

    /**
     * Negatif: password salah harus menampilkan pesan error dan tetap di halaman login.
     * Langkah: buka login -> isi email valid & password salah -> submit -> cek pesan "Email atau Password salah.".
     */
    public function testLoginGagalKarenaPasswordSalah(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginPage($browser);

            $this->submitLogin($browser, self::VALID_EMAIL, self::WRONG_PASSWORD);

            $browser->waitForLocation('/login', 10)
                    ->waitForText('Email atau Password salah.', 5)
                    ->assertSee('Email atau Password salah.')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Negatif: format email tidak valid harus ditolak dengan pesan error.
     * Langkah: buka login -> isi email tanpa format benar -> submit -> cek pesan "Format email tidak valid.".
     */
    public function testLoginGagalKarenaFormatEmailTidakValid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginPage($browser);

            $this->submitLogin($browser, self::INVALID_EMAIL, 'password-apa-saja');

            $browser->waitForLocation('/login', 10)
                    ->waitForText('Format email tidak valid.', 5)
                    ->assertSee('Format email tidak valid.')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Negatif: email dan password kosong harus ditolak.
     * Langkah: buka login -> biarkan kedua field kosong -> submit -> cek pesan "Email dan password harus diisi.".
     */
    public function testLoginGagalKarenaFieldKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginPage($browser);

            $this->submitLogin($browser, '', '');

            $browser->waitForLocation('/login', 10)
                    ->waitForText('Email dan password harus diisi.', 5)
                    ->assertSee('Email dan password harus diisi.')
                    ->assertPathIs('/login');
        });
    }

    /**
     * Muat ulang halaman login dari kondisi bersih dan matikan validasi klien agar skenario negatif dikirim ke server.
     */
    private function openLoginPage(Browser $browser): void
    {
        $browser->visit('/login');

        // Pastikan sesi bersih sebelum memulai skenario baru.
        $browser->driver->manage()->deleteAllCookies();

        $browser->visit('/login')
                ->waitFor('#loginForm', 5)
                ->waitForText('Welcome Back!', 5)
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');

        // Matikan validasi sisi klien agar tes negatif bisa menembus ke backend.
        $browser->script('
            const form = document.getElementById("loginForm");
            if (form) {
                form.setAttribute("novalidate", "true");
                const submitBtn = form.querySelector(\'button[type="submit"]\');
                if (submitBtn) {
                    submitBtn.dataset.skipValidation = "true";
                }
            }
        ');
        $browser->pause(200);
    }

    /**
     * Isi dan submit form login.
     */
    private function submitLogin(Browser $browser, string $email, string $password): void
    {
        $browser->type('input[name="email"]', $email)
                ->type('input[name="password"]', $password)
                ->press('button[type="submit"]')
                ->pause(500);
    }

    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
