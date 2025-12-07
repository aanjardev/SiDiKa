<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class BranchManagementTest extends AdminBrowserTestCase
{
    public function testAdminCanAddBranch(): void
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
                ->waitFor('a.submenu-link[href*="admin/branches"]', 5)
                ->click('a.submenu-link[href*="admin/branches"]')
                ->pause(2000)
                ->waitForLocation('/admin/branches', 10)
                ->assertPathIs('/admin/branches')
                ->pause(1000)
                ->waitFor('a[href*="admin/branches/create"]', 5)
                ->click('a[href*="admin/branches/create"]')
                ->pause(2000)
                ->waitForLocation('/admin/branches/create', 10)
                ->assertPathIs('/admin/branches/create')
                ->pause(1000)
                ->waitFor('form#branchForm', 10)
                ->pause(1000);

            $namaCabang = 'Cabang ' . $this->faker->city();
            $alamat = $this->faker->streetAddress() . ', ' . $this->faker->city();
            $email = $this->faker->companyEmail();
            $deskripsi = $this->faker->sentence(10);
            $nomorTelepon = '08' . $this->faker->numerify('##########');
            $linkMaps = 'https://maps.google.com/?q=' . urlencode($alamat);

            $browser->type('input[name="nama"]', $namaCabang)
                ->type('textarea[name="alamat"]', $alamat)
                ->type('input[name="email"]', $email)
                ->type('textarea[name="deskripsi"]', $deskripsi)
                ->type('input[id="branch_nomor_telepon_display"]', $nomorTelepon)
                ->type('input[name="link_maps"]', $linkMaps)
                ->pause(500)
                ->check('input[name="is_active"]')
                ->pause(500);

            $browser->script('
                const jamSection = document.querySelector(".fa-clock");
                if (jamSection) {
                    jamSection.closest(".card").scrollIntoView({ behavior: "smooth", block: "center" });
                }
            ');
            $browser->pause(500)
                ->waitFor('.fa-clock', 5)
                ->pause(500);

            $browser->script('
                let button = document.querySelector("button[onclick=\"bukaSemuaHari()\"]");
                if (!button) {
                    const buttons = document.querySelectorAll("button");
                    buttons.forEach(btn => {
                        if (btn.textContent.includes("Buka Semua")) {
                            button = btn;
                        }
                    });
                }
                if (button) {
                    button.click();
                }
            ');
            $browser->pause(1000);

            $browser->script('
                const globalJamBuka = document.getElementById("global_jam_buka");
                const globalJamTutup = document.getElementById("global_jam_tutup");

                if (globalJamBuka) {
                    globalJamBuka.value = "08:00";
                    globalJamBuka.dispatchEvent(new Event("change", { bubbles: true }));
                }

                if (globalJamTutup) {
                    globalJamTutup.value = "17:00";
                    globalJamTutup.dispatchEvent(new Event("change", { bubbles: true }));
                }
            ');
            $browser->pause(500);

            $browser->script('
                let button = document.querySelector("button[onclick=\"samakanSemuaJam()\"]");
                if (!button) {
                    const buttons = document.querySelectorAll("button");
                    buttons.forEach(btn => {
                        if (btn.textContent.includes("Samakan Jam Semua Hari")) {
                            button = btn;
                        }
                    });
                }
                if (button) {
                    button.click();
                }
            ');
            $browser->pause(1000)
                ->press('button[type="submit"][form="branchForm"]')
                ->pause(2000)
                ->waitForLocation('/admin/branches', 10)
                ->assertPathIs('/admin/branches')
                ->pause(1000)
                ->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
        });
    }
}
