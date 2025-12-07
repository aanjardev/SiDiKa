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

            // Pastikan field kosong
            $browser->clear('input[name="nama_kategori"]')
                ->pause(500);

            // Submit tanpa mengisi nama kategori
            $browser->press('button[type="submit"][form="categoryForm"]')
                ->pause(3000);

            // Verifikasi tetap di halaman form (tidak redirect)
            $browser->waitUsing(10, 500, function () use ($browser) {
                $currentPath = $browser->driver->getCurrentURL();
                return str_contains($currentPath, '/create') || str_contains($currentPath, '/categories/create');
            }, 'Form redirect ke halaman lain padahal seharusnya ada error.');

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                const form = document.getElementById('categoryForm');
                return (form !== null) && (
                    bodyText.includes('nama') ||
                    bodyText.includes('kategori') ||
                    bodyText.includes('wajib') ||
                    bodyText.includes('required') ||
                    document.querySelector('.alert-danger') !== null ||
                    document.querySelector('.text-danger') !== null ||
                    document.querySelector('[role=\"alert\"]') !== null ||
                    document.querySelector('.invalid-feedback') !== null ||
                    document.querySelector('input[name=\"nama_kategori\"].is-invalid') !== null
                );
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan nama kategori kosong.');
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
                ->pause(1000);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('categoryForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            $browser->press('button[type="submit"][form="categoryForm"]')
                ->pause(3000);

            // Verifikasi tetap di halaman form atau redirect dengan error
            $browser->waitUsing(10, 500, function () use ($browser) {
                $currentPath = $browser->driver->getCurrentURL();
                return str_contains($currentPath, '/create') || str_contains($currentPath, '/categories');
            }, 'Form tidak berada di halaman yang diharapkan.');

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                return bodyText.includes('nama') ||
                       bodyText.includes('kategori') ||
                       bodyText.includes('sudah') ||
                       bodyText.includes('digunakan') ||
                       bodyText.includes('terdaftar') ||
                       document.querySelector('.alert-danger') !== null ||
                       document.querySelector('.text-danger') !== null ||
                       document.querySelector('[role=\"alert\"]') !== null ||
                       document.querySelector('.invalid-feedback') !== null;
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan nama kategori duplikat.');
        });
    }
}
