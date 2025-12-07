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

    /**
     * Negatif: Form kategori harus menolak submit jika nama kategori kosong
     */
    public function testTambahKategoriGagalKarenaNamaKosong(): void
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
                ->pause(1000)
                ->waitFor('a[href*="admin/categories/create"]', 5)
                ->click('a[href*="admin/categories/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/categories/create', 10)
                ->pause(1000)
                ->waitFor('form#categoryForm', 10)
                ->pause(1000);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('categoryForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Submit tanpa mengisi nama kategori
            $browser->press('button[type="submit"][form="categoryForm"]')
                ->pause(2000);

            // Verifikasi tetap di halaman form dengan error
            $browser->assertPresent('#categoryForm')
                ->assertSee('nama kategori')
                ->pause(500);
        });
    }

    /**
     * Negatif: Form kategori harus menolak submit jika nama kategori duplikat
     */
    public function testTambahKategoriGagalKarenaNamaDuplikat(): void
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
                ->pause(1000);

            // Ambil nama kategori pertama yang sudah ada
            $existingCategory = $browser->script("
                const firstRow = document.querySelector('table tbody tr');
                if (!firstRow) return null;
                const cells = firstRow.querySelectorAll('td');
                // Nama kategori biasanya di kolom pertama atau kedua
                for (let i = 0; i < Math.min(3, cells.length); i++) {
                    const text = cells[i].textContent.trim();
                    if (text && text.length > 0 && text.length < 100) {
                        return text;
                    }
                }
                return null;
            ")[0];

            // Jika tidak ada kategori, skip test
            if (empty($existingCategory)) {
                $this->markTestSkipped('Tidak ada kategori yang ada untuk test duplikasi');
                return;
            }

            $browser->waitFor('a[href*="admin/categories/create"]', 5)
                ->click('a[href*="admin/categories/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/categories/create', 10)
                ->pause(1000)
                ->waitFor('form#categoryForm', 10)
                ->pause(1000);

            // Isi dengan nama kategori yang sudah ada
            $browser->type('input[name="nama_kategori"]', $existingCategory)
                ->pause(1000)
                ->press('button[type="submit"][form="categoryForm"]')
                ->pause(2000);

            // Verifikasi error muncul
            $browser->assertPresent('#categoryForm')
                ->assertSee('nama kategori')
                ->pause(500);
        });
    }
}
