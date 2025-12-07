<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class EmployeeManagementTest extends AdminBrowserTestCase
{
    public function testAdminCanAddEmployee(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->pause(1000)
                ->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5)
                ->click('button.menu-toggle[data-bs-target="#masterDataMenu"]')
                ->pause(1000)
                ->waitFor('a.submenu-link[href*="admin/employees"]', 5)
                ->click('a.submenu-link[href*="admin/employees"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees', 10)
                ->assertPathIs('/admin/employees')
                ->pause(1000)
                ->waitFor('a[href*="admin/employees/create"]', 5)
                ->click('a[href*="admin/employees/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees/create', 10)
                ->assertPathIs('/admin/employees/create')
                ->pause(1000)
                ->waitFor('form#employeeForm', 10)
                ->pause(1000);

            $namaLengkap = $this->faker->name();
            $nik = $this->faker->numerify('################');
            $jabatan = $this->faker->jobTitle();
            $nomorTelepon = '08' . $this->faker->numerify('##########');
            $tanggalMasuk = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
            $gaji = $this->faker->numberBetween(3000000, 10000000);
            $alamat = $this->faker->address();

            $browser->type('input[name="nama_lengkap"]', $namaLengkap)
                ->type('input[name="nik"]', $nik)
                ->type('input[name="jabatan"]', $jabatan)
                ->type('input[id="nomor_telepon_display"]', $nomorTelepon)
                ->type('input[name="tanggal_masuk"]', $tanggalMasuk)
                ->type('input[name="gaji"]', (string) $gaji)
                ->select('select[name="status"]', 'aktif')
                ->type('textarea[name="alamat"]', $alamat)
                ->pause(1000)
                ->press('button[type="submit"][form="employeeForm"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees', 10)
                ->assertPathIs('/admin/employees')
                ->pause(1000)
                ->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
        });
    }

    /**
     * Negatif: Form karyawan harus menolak submit jika field required kosong
     */
    public function testTambahKaryawanGagalKarenaFieldRequiredKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->pause(1000)
                ->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5)
                ->click('button.menu-toggle[data-bs-target="#masterDataMenu"]')
                ->pause(1000)
                ->waitFor('a.submenu-link[href*="admin/employees"]', 5)
                ->click('a.submenu-link[href*="admin/employees"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees', 10)
                ->pause(1000)
                ->waitFor('a[href*="admin/employees/create"]', 5)
                ->click('a[href*="admin/employees/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees/create', 10)
                ->pause(1000)
                ->waitFor('form#employeeForm', 10)
                ->pause(1000);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('employeeForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Submit tanpa mengisi field required
            $browser->press('button[type="submit"][form="employeeForm"]')
                ->pause(2000);

            // Verifikasi tetap di halaman form dengan error
            $browser->assertPresent('#employeeForm')
                ->assertSee('nama lengkap')
                ->pause(500);
        });
    }

    /**
     * Negatif: Form karyawan harus menolak submit jika NIK duplikat
     */
    public function testTambahKaryawanGagalKarenaNIKDuplikat(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->pause(1000)
                ->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5)
                ->click('button.menu-toggle[data-bs-target="#masterDataMenu"]')
                ->pause(1000)
                ->waitFor('a.submenu-link[href*="admin/employees"]', 5)
                ->click('a.submenu-link[href*="admin/employees"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees', 10)
                ->pause(1000);

            // Ambil NIK yang sudah ada dari karyawan pertama di tabel
            $existingNik = $browser->script("
                const firstRow = document.querySelector('table tbody tr');
                if (!firstRow) return null;
                const cells = firstRow.querySelectorAll('td');
                // NIK biasanya di kolom kedua atau ketiga
                for (let i = 0; i < Math.min(4, cells.length); i++) {
                    const text = cells[i].textContent.trim();
                    // NIK biasanya 16 digit
                    if (text && /^[0-9]{16}$/.test(text)) {
                        return text;
                    }
                }
                return null;
            ")[0];

            // Jika tidak ada karyawan, skip test
            if (empty($existingNik)) {
                $this->markTestSkipped('Tidak ada karyawan yang ada untuk test duplikasi NIK');
                return;
            }

            $browser->waitFor('a[href*="admin/employees/create"]', 5)
                ->click('a[href*="admin/employees/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees/create', 10)
                ->pause(1000)
                ->waitFor('form#employeeForm', 10)
                ->pause(1000);

            // Isi form dengan data valid kecuali NIK yang duplikat
            $namaLengkap = $this->faker->name();
            $jabatan = $this->faker->jobTitle();
            $nomorTelepon = '08' . $this->faker->numerify('##########');
            $tanggalMasuk = date('Y-m-d');

            $browser->type('input[name="nama_lengkap"]', $namaLengkap)
                ->type('input[name="nik"]', $existingNik) // NIK duplikat
                ->type('input[name="jabatan"]', $jabatan)
                ->type('input[id="nomor_telepon_display"]', $nomorTelepon)
                ->type('input[name="tanggal_masuk"]', $tanggalMasuk)
                ->select('select[name="status"]', 'aktif')
                ->pause(1000);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('employeeForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Submit form
            $browser->press('button[type="submit"][form="employeeForm"]')
                ->pause(2000);

            // Verifikasi error muncul
            $browser->assertPresent('#employeeForm')
                ->assertSee('NIK sudah terdaftar')
                ->pause(500);
        });
    }

    /**
     * Negatif: Form karyawan harus menolak submit jika nomor telepon tidak valid
     */
    public function testTambahKaryawanGagalKarenaNomorTeleponTidakValid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->pause(1000)
                ->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5)
                ->click('button.menu-toggle[data-bs-target="#masterDataMenu"]')
                ->pause(1000)
                ->waitFor('a.submenu-link[href*="admin/employees"]', 5)
                ->click('a.submenu-link[href*="admin/employees"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees', 10)
                ->pause(1000)
                ->waitFor('a[href*="admin/employees/create"]', 5)
                ->click('a[href*="admin/employees/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/employees/create', 10)
                ->pause(1000)
                ->waitFor('form#employeeForm', 10)
                ->pause(1000);

            // Isi form dengan data valid kecuali nomor telepon yang tidak valid
            $namaLengkap = $this->faker->name();
            $nik = $this->faker->numerify('################');
            $jabatan = $this->faker->jobTitle();
            $tanggalMasuk = date('Y-m-d');

            $browser->type('input[name="nama_lengkap"]', $namaLengkap)
                ->type('input[name="nik"]', $nik)
                ->type('input[name="jabatan"]', $jabatan)
                ->type('input[id="nomor_telepon_display"]', '12345') // Nomor telepon tidak valid
                ->type('input[name="tanggal_masuk"]', $tanggalMasuk)
                ->select('select[name="status"]', 'aktif')
                ->pause(1000);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('employeeForm');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Submit form
            $browser->press('button[type="submit"][form="employeeForm"]')
                ->pause(2000);

            // Verifikasi error muncul
            $browser->assertPresent('#employeeForm')
                ->assertSee('Nomor telepon')
                ->pause(500);
        });
    }
}
