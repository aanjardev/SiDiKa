<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AdminLogoutTest extends AdminBrowserTestCase
{
    public function testAdminCanLogoutFromProfile(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/dashboard')
                ->pause(1000);

            $browser->script('
                const footer = document.querySelector(".sidebar-footer");
                if (footer) {
                    footer.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            ');
            $browser->pause(500)
                ->waitFor('a.user-profile[href*="admin/profile"]', 5)
                ->click('a.user-profile[href*="admin/profile"]')
                ->pause(2000)
                ->waitForLocation('/admin/profile', 10)
                ->assertPathIs('/admin/profile')
                ->pause(1000)
                ->waitFor('button[data-bs-target="#logoutModal"]', 5)
                ->pause(500)
                ->click('button[data-bs-target="#logoutModal"]')
                ->pause(1000)
                ->waitFor('#logoutModal', 5)
                ->assertVisible('#logoutModal');

            $browser->within('#logoutModal', function ($modal) {
                $modal->waitFor('form#logout-form button[type="submit"]', 5)
                    ->press('form#logout-form button[type="submit"]');
            })
                ->pause(2000)
                ->waitForLocation('/login', 10)
                ->assertPathIs('/login')
                ->waitForText('Welcome Back!', 5)
                ->assertSee('Welcome Back!')
                ->assertSee('Silakan masuk ke Sistem Informasi Dinoyo Kamera')
                ->waitFor('input[name="email"]', 5)
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');
        });
    }
}
