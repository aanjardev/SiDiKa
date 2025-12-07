<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Sleep;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Faker\Factory as Faker;

class AdminLifecycleTest extends DuskTestCase
{
    /**
     * Test credentials
     */
    private const ADMIN_EMAIL = 'admin@dinoyokamera.com';
    private const ADMIN_PASSWORD = 'admin123';

    /**
     * Faker instance for generating dummy data
     */
    private $faker;

    /**
     * Setup faker before tests
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Test the complete admin lifecycle: Login -> Add Employee -> Add Branch -> Add Category -> Logout
     */
    public function testCompleteAdminLifecycle(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Login Flow
            $this->testLoginFlow($browser);
            
            // 2. Add Employee
            $this->testAddEmployee($browser);
            
            // 3. Add Branch
            $this->testAddBranch($browser);
            
            // 4. Add Category
            $this->testAddCategory($browser);
            
            // 5. Logout Flow
            $this->testLogoutFlow($browser);
        });
    }

    /**
     * Test Login Flow: Ensure user can login and be redirected to dashboard
     */
    private function testLoginFlow(Browser $browser): void
    {
        // Visit login page
        $browser->visit('/login')
                ->pause(1000) // Wait for page to fully load
                ->waitForText('Welcome Back!', 5) // Wait for login form to appear
                ->assertSee('Welcome Back!')
                ->assertSee('Silakan masuk ke Sistem Informasi Dinoyo Kamera');

        // Fill in login form
        $browser->type('input[name="email"]', self::ADMIN_EMAIL)
                ->type('input[name="password"]', self::ADMIN_PASSWORD)
                ->pause(500); // Small pause before clicking

        // Submit login form
        $browser->press('button[type="submit"]')
                ->pause(2000); // Wait for form submission and redirect

        // Wait for redirect to dashboard and verify URL
        $browser->waitForLocation('/admin/dashboard', 10)
                ->assertPathIs('/admin/dashboard');
    }

    /**
     * Test Add Employee: Navigate to add employee page, fill form, and save
     */
    private function testAddEmployee(Browser $browser): void
    {
        // Navigate to employees page via sidebar
        $browser->pause(1000);

        // Click on Master Data menu toggle to expand it
        $browser->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5)
                ->click('button.menu-toggle[data-bs-target="#masterDataMenu"]')
                ->pause(1000); // Wait for submenu to expand

        // Click on "Data Karyawan" link (only visible for manager role)
        $browser->waitFor('a.submenu-link[href*="admin/employees"]', 5)
                ->click('a.submenu-link[href*="admin/employees"]')
                ->pause(2000); // Wait for page navigation

        // Wait for employees index page to load
        $browser->waitForLocation('/admin/employees', 10)
                ->assertPathIs('/admin/employees')
                ->pause(1000);

        // Click on "Tambah Karyawan" button
        $browser->waitFor('a[href*="admin/employees/create"]', 5)
                ->click('a[href*="admin/employees/create"]')
                ->pause(2000); // Wait for page navigation

        // Wait for create employee page to load
        $browser->waitForLocation('/admin/employees/create', 10)
                ->assertPathIs('/admin/employees/create')
                ->pause(1000);

        // Wait for form to appear
        $browser->waitFor('form#employeeForm', 10)
                ->pause(1000);

        // Generate dummy data using Faker
        $namaLengkap = $this->faker->name();
        $nik = $this->faker->numerify('################'); // 16 digits
        $jabatan = $this->faker->jobTitle();
        $nomorTelepon = '08' . $this->faker->numerify('##########'); // 12 digits after 08
        $tanggalMasuk = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');
        $gaji = $this->faker->numberBetween(3000000, 10000000);
        $alamat = $this->faker->address();

        // Fill in employee form
        $browser->type('input[name="nama_lengkap"]', $namaLengkap)
                ->type('input[name="nik"]', $nik)
                ->type('input[name="jabatan"]', $jabatan)
                ->type('input[id="nomor_telepon_display"]', $nomorTelepon) // Use display input
                ->type('input[name="tanggal_masuk"]', $tanggalMasuk)
                ->type('input[name="gaji"]', (string)$gaji)
                ->select('select[name="status"]', 'aktif')
                ->type('textarea[name="alamat"]', $alamat)
                ->pause(1000); // Pause before submit

        // Submit form
        $browser->press('button[type="submit"][form="employeeForm"]')
                ->pause(2000); // Wait for form submission

        // Wait for redirect to employees index page with success message
        $browser->waitForLocation('/admin/employees', 10)
                ->assertPathIs('/admin/employees')
                ->pause(1000);

        // Verify success message (if using alert system)
        $browser->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
    }

    /**
     * Test Add Branch: Navigate to add branch page, fill form, and save
     */
    private function testAddBranch(Browser $browser): void
    {
        // Navigate to branches page via sidebar
        $browser->pause(1000);

        // Ensure Master Data menu is expanded (might already be expanded)
        $browser->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5);
        
        // Check if menu is collapsed, if so expand it
        $browser->script('
            const menu = document.querySelector("#masterDataMenu");
            if (menu && !menu.classList.contains("show")) {
                document.querySelector("button[data-bs-target=\"#masterDataMenu\"]").click();
            }
        ');
        $browser->pause(1000);

        // Click on "Data Cabang" link
        $browser->waitFor('a.submenu-link[href*="admin/branches"]', 5)
                ->click('a.submenu-link[href*="admin/branches"]')
                ->pause(2000); // Wait for page navigation

        // Wait for branches index page to load
        $browser->waitForLocation('/admin/branches', 10)
                ->assertPathIs('/admin/branches')
                ->pause(1000);

        // Click on "Tambah Cabang" button
        $browser->waitFor('a[href*="admin/branches/create"]', 5)
                ->click('a[href*="admin/branches/create"]')
                ->pause(2000); // Wait for page navigation

        // Wait for create branch page to load
        $browser->waitForLocation('/admin/branches/create', 10)
                ->assertPathIs('/admin/branches/create')
                ->pause(1000);

        // Wait for form to appear
        $browser->waitFor('form#branchForm', 10)
                ->pause(1000);

        // Generate dummy data using Faker
        $namaCabang = 'Cabang ' . $this->faker->city();
        $alamat = $this->faker->streetAddress() . ', ' . $this->faker->city();
        $email = $this->faker->companyEmail();
        $deskripsi = $this->faker->sentence(10);
        $nomorTelepon = '08' . $this->faker->numerify('##########');
        $linkMaps = 'https://maps.google.com/?q=' . urlencode($alamat);

        // Fill in branch form
        $browser->type('input[name="nama"]', $namaCabang)
                ->type('textarea[name="alamat"]', $alamat)
                ->type('input[name="email"]', $email)
                ->type('textarea[name="deskripsi"]', $deskripsi)
                ->type('input[id="branch_nomor_telepon_display"]', $nomorTelepon) // Use display input
                ->type('input[name="link_maps"]', $linkMaps)
                ->pause(500);

        // Set status aktif (checkbox)
        $browser->check('input[name="is_active"]')
                ->pause(500);

        // Set jam operasional - use "Buka Semua" button and global time inputs
        // Scroll to jam operasional section first
        $browser->script('
            const jamSection = document.querySelector(".fa-clock");
            if (jamSection) {
                jamSection.closest(".card").scrollIntoView({ behavior: "smooth", block: "center" });
            }
        ');
        $browser->pause(500);

        // Wait for jam operasional section to be visible
        $browser->waitFor('.fa-clock', 5)
                ->pause(500);

        // Click "Buka Semua" button to open all days
        // Try multiple selectors to find the button
        $browser->script('
            // Try to find button by onclick attribute
            let button = document.querySelector("button[onclick=\"bukaSemuaHari()\"]");
            // If not found, try by text content
            if (!button) {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => {
                    if (btn.textContent.includes("Buka Semua")) {
                        button = btn;
                    }
                });
            }
            // If found, click it
            if (button) {
                button.click();
            }
        ');
        $browser->pause(1000); // Wait for JavaScript to enable all day inputs

        // Set global jam buka and jam tutup using script injection
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

        // Click "Samakan Jam Semua Hari" button to apply time to all days
        $browser->script('
            // Try to find button by onclick attribute
            let button = document.querySelector("button[onclick=\"samakanSemuaJam()\"]");
            // If not found, try by text content
            if (!button) {
                const buttons = document.querySelectorAll("button");
                buttons.forEach(btn => {
                    if (btn.textContent.includes("Samakan Jam Semua Hari")) {
                        button = btn;
                    }
                });
            }
            // If found, click it
            if (button) {
                button.click();
            }
        ');
        $browser->pause(1000); // Wait for JavaScript to apply times to all days

        // Submit form
        $browser->press('button[type="submit"][form="branchForm"]')
                ->pause(2000); // Wait for form submission

        // Wait for redirect to branches index page with success message
        $browser->waitForLocation('/admin/branches', 10)
                ->assertPathIs('/admin/branches')
                ->pause(1000);

        // Verify success message
        $browser->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
    }

    /**
     * Test Add Category: Navigate to add category page, fill form (text only, no image), and save
     */
    private function testAddCategory(Browser $browser): void
    {
        // Navigate to categories page via sidebar
        $browser->pause(1000);

        // Ensure Master Data menu is expanded
        $browser->waitFor('button.menu-toggle[data-bs-target="#masterDataMenu"]', 5);
        
        // Check if menu is collapsed, if so expand it
        $browser->script('
            const menu = document.querySelector("#masterDataMenu");
            if (menu && !menu.classList.contains("show")) {
                document.querySelector("button[data-bs-target=\"#masterDataMenu\"]").click();
            }
        ');
        $browser->pause(1000);

        // Click on "Daftar Kategori" link
        $browser->waitFor('a.submenu-link[href*="admin/categories"]', 5)
                ->click('a.submenu-link[href*="admin/categories"]')
                ->pause(2000); // Wait for page navigation

        // Wait for categories index page to load
        $browser->waitForLocation('/admin/categories', 10)
                ->assertPathIs('/admin/categories')
                ->pause(1000);

        // Click on "Tambah Kategori" button
        $browser->waitFor('a[href*="admin/categories/create"]', 5)
                ->click('a[href*="admin/categories/create"]')
                ->pause(2000); // Wait for page navigation

        // Wait for create category page to load
        $browser->waitForLocation('/admin/categories/create', 10)
                ->assertPathIs('/admin/categories/create')
                ->pause(1000);

        // Wait for form to appear
        $browser->waitFor('form#categoryForm', 10)
                ->pause(1000);

        // Generate dummy data using Faker
        $namaKategori = 'Kategori ' . $this->faker->word() . ' ' . $this->faker->randomNumber(3);

        // Fill in category form (only text, no image upload)
        $browser->type('input[name="nama_kategori"]', $namaKategori)
                ->pause(1000); // Pause before submit

        // Note: We intentionally skip the file upload field (gambar) as per requirements

        // Submit form
        $browser->press('button[type="submit"][form="categoryForm"]')
                ->pause(2000); // Wait for form submission

        // Wait for redirect to categories index page with success message
        $browser->waitForLocation('/admin/categories', 10)
                ->assertPathIs('/admin/categories')
                ->pause(1000);

        // Verify success message
        $browser->waitFor('.alert-success, [role="alert"]', 5)
                ->assertSee('berhasil');
    }

    /**
     * Test Logout Flow: Perform logout and ensure user returns to login page
     * Flow: Dashboard -> Profil Pengguna -> Tombol Logout -> Modal -> Confirm Logout
     */
    private function testLogoutFlow(Browser $browser): void
    {
        // Navigate to dashboard first (if not already there)
        $browser->visit('/admin/dashboard')
                ->pause(1000);

        // Scroll to sidebar footer to ensure user profile link is visible
        $browser->script('
            const footer = document.querySelector(".sidebar-footer");
            if (footer) {
                footer.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        ');
        $browser->pause(500);

        // Click on user profile link in sidebar footer to go to profile page
        $browser->waitFor('a.user-profile[href*="admin/profile"]', 5)
                ->click('a.user-profile[href*="admin/profile"]')
                ->pause(2000); // Wait for page navigation

        // Wait for profile page to load
        $browser->waitForLocation('/admin/profile', 10)
                ->assertPathIs('/admin/profile')
                ->pause(1000);

        // Wait for logout button to appear (button that opens modal)
        $browser->waitFor('button[data-bs-target="#logoutModal"]', 5)
                ->pause(500);

        // Click logout button to open modal
        $browser->click('button[data-bs-target="#logoutModal"]')
                ->pause(1000); // Wait for modal to appear

        // Wait for logout modal to be visible
        $browser->waitFor('#logoutModal', 5)
                ->assertVisible('#logoutModal')
                ->pause(500);

        // Click "Ya, Logout" button inside the modal
        $browser->within('#logoutModal', function ($modal) {
            $modal->waitFor('form#logout-form button[type="submit"]', 5)
                  ->press('form#logout-form button[type="submit"]');
        })
        ->pause(2000); // Wait for logout to process

        // Wait for redirect to login page
        $browser->waitForLocation('/login', 10)
                ->assertPathIs('/login');

        // Verify we're on login page
        $browser->waitForText('Welcome Back!', 5)
                ->assertSee('Welcome Back!')
                ->assertSee('Silakan masuk ke Sistem Informasi Dinoyo Kamera');

        // Verify login form is present
        $browser->waitFor('input[name="email"]', 5)
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');
    }

    /**
     * Override shouldStartMaximized to ensure window is maximized
     * This is called by parent driver() method
     */
    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
