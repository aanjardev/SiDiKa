<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AdminLoginTest extends AdminBrowserTestCase
{
    public function testAdminCanLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
        });
    }
}
