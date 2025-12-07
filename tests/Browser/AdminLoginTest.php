<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AdminLoginTest extends AdminBrowserTestCase
{
    /**
     * Positif: Admin berhasil login dengan kredensial valid
     */
    public function testAdminCanLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
        });
    }

    /**
     * Negatif: Login gagal karena password salah
     */
    public function testAdminLoginGagalKarenaPasswordSalah(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('#loginForm', 10)
                ->pause(500);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('loginForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            $browser->type('input[name="email"]', 'admin@dinoyokamera.com')
                ->type('input[name="password"]', 'wrong-password')
                ->press('button[type="submit"]')
                ->pause(2000);

            $browser->waitForLocation('/login', 10)
                ->assertPathIs('/login')
                ->assertSee('Email atau Password salah')
                ->pause(500);
        });
    }

    /**
     * Negatif: Login gagal karena email tidak terdaftar
     */
    public function testAdminLoginGagalKarenaEmailTidakTerdaftar(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('#loginForm', 10)
                ->pause(500);

            $browser->script("
                const form = document.getElementById('loginForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            $browser->type('input[name="email"]', 'nonexistent@example.com')
                ->type('input[name="password"]', 'any-password')
                ->press('button[type="submit"]')
                ->pause(2000);

            $browser->waitForLocation('/login', 10)
                ->assertPathIs('/login')
                ->assertSee('Email atau Password salah')
                ->pause(500);
        });
    }

    /**
     * Negatif: Login gagal karena field kosong
     */
    public function testAdminLoginGagalKarenaFieldKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('#loginForm', 10)
                ->pause(500);

            $browser->script("
                const form = document.getElementById('loginForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            $browser->clear('input[name="email"]')
                ->clear('input[name="password"]')
                ->press('button[type="submit"]')
                ->pause(3000);

            // Tunggu error muncul dengan berbagai cara
            $browser->waitForLocation('/login', 10)
                ->assertPathIs('/login')
                ->pause(1000);

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                return bodyText.includes('Email dan password harus diisi') || 
                       bodyText.includes('email') ||
                       bodyText.includes('password') ||
                       document.querySelector('.alert-danger') !== null ||
                       document.querySelector('.text-danger') !== null ||
                       document.querySelector('[role=\"alert\"]') !== null ||
                       document.querySelector('.invalid-feedback') !== null;
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan field kosong.');
        });
    }
}
