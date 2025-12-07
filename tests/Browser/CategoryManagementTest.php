<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class CategoryManagementTest extends AdminBrowserTestCase
{
    public function testAdminCanAddCategory(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->pause(1000)
                ->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5);

            $browser->script('
                const menu = document.querySelector("#masterDataMenu");
                if (menu && !menu.classList.contains("show")) {
                    document.querySelector("button[data-bs-target=\"#masterDataMenu\"]").click();
                }
            ');

            $browser->pause(1000)
                ->waitFor('a.submenu-link[href*="admin/categories"]', 5)
                ->click('a.submenu-link[href*="admin/categories"]')
                ->pause(2000)
                ->waitForLocation('/admin/categories', 10)
                ->assertPathIs('/admin/categories')
                ->pause(1000)
                ->waitFor('a[href*="admin/categories/create"]', 5)
                ->click('a[href*="admin/categories/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/categories/create', 10)
                ->assertPathIs('/admin/categories/create')
                ->pause(1000)
                ->waitFor('form#categoryForm', 10)
                ->pause(1000);

            $namaKategori = 'Kategori ' . $this->faker->word() . ' ' . $this->faker->randomNumber(3);

            $browser->type('input[name="nama_kategori"]', $namaKategori)
                ->pause(1000)
                ->press('button[type="submit"][form="categoryForm"]')
                ->pause(2000)
                ->waitForLocation('/admin/categories', 10)
                ->assertPathIs('/admin/categories')
                ->pause(1000)
                ->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
        });
    }
}
