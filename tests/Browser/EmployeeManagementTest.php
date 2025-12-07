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
}
