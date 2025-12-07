<?php

namespace Tests\Browser;

use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class AdminBrowserTestCase extends DuskTestCase
{
    protected const ADMIN_EMAIL = 'admin@dinoyokamera.com';
    protected const ADMIN_PASSWORD = 'admin123';

    /**
     * Faker instance for generating dummy data.
     */
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Login helper shared across admin browser tests.
     */
    protected function loginAsAdmin(Browser $browser): void
    {
        $browser->visit('/login')
            ->pause(1000)
            ->waitForText('Welcome Back!', 5)
            ->assertSee('Welcome Back!')
            ->assertSee('Silakan masuk ke Sistem Informasi Dinoyo Kamera')
            ->type('input[name="email"]', static::ADMIN_EMAIL)
            ->type('input[name="password"]', static::ADMIN_PASSWORD)
            ->pause(500)
            ->press('button[type="submit"]')
            ->pause(2000)
            ->waitForLocation('/admin/dashboard', 10)
            ->assertPathIs('/admin/dashboard');
    }

    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
