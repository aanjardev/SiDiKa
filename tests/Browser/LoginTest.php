<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_login_page_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->waitForText('SiDiKa');
        });
    }

    public function test_cannot_login_with_invalid_password(): void
    {
        [$user] = $this->ensureActiveUserExists();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'wrong-password')
                ->press('@btn-sign-in')
                ->waitForText('Email atau Password salah.')
                ->assertSee('Email atau Password salah.')
                ->assertPathIs('/login');
        });
    }

    public function test_cannot_login_with_invalid_username(): void
    {
        $this->ensureActiveUserExists();

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'not-exist@example.com')
                ->type('password', 'password123')
                ->press('@btn-sign-in')
                ->waitForText('Email atau Password salah.')
                ->assertSee('Email atau Password salah.')
                ->assertPathIs('/login');
        });
    }

    public function test_cannot_login_without_username(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('password', 'password123')
                ->press('@btn-sign-in')
                ->waitForText('Email wajib diisi')
                ->assertSee('Email wajib diisi')
                ->assertPathIs('/login');
        });
    }

    public function test_cannot_login_without_password(): void
    {
        [$user] = $this->ensureActiveUserExists();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->press('@btn-sign-in')
                ->waitForText('Password wajib diisi')
                ->assertSee('Password wajib diisi')
                ->assertPathIs('/login');
        });
    }

    public function test_can_login_with_valid_credentials(): void
    {
        [$user, $password] = $this->ensureActiveUserExists();

        $this->browse(function (Browser $browser) use ($user, $password) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', $password)
                ->press('@btn-sign-in')
                ->waitForText('Dashboard')
                ->waitForLocation('/admin/dashboard')
                ->assertPathIs('/admin/dashboard');
        });
    }

    public function test_user_can_logout(): void
    {
        [$user, $password] = $this->ensureActiveUserExists();

        $this->browse(function (Browser $browser) use ($user, $password) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', $password)
                ->press('@btn-sign-in')
                ->waitForText('Dashboard')
                ->waitForLocation('/admin/dashboard')
                ->assertPathIs('/admin/dashboard')
                ->visit('/admin/profile')
                ->waitForText('Profil Pengguna')
                ->click('button[data-bs-target="#logoutModal"]')
                ->waitForText('Apakah Anda yakin ingin logout?')
                ->within('#logoutModal', function (Browser $modal) {
                    $modal->press('Ya, Logout');
                })
                ->waitForLocation('/login')
                ->assertPathIs('/login');
        });
    }

    private function ensureActiveUserExists(): array
    {
        $password = 'admin123';
        $email = 'admin@dinoyokamera.com';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->markTestSkipped('User admin@dinoyokamera.com tidak tersedia. Seed database terlebih dahulu.');
        }

        return [$user, $password];
    }
}
