<?php

namespace Tests\Browser;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PenjualanTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_can_visit_penjualan_page(): void
    {
        [$user, $password] = $this->createSaleFixtures();

        $this->browse(function (Browser $browser) use ($user, $password) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', $password)
                ->press('@btn-sign-in')
                ->waitForText('Dashboard')
                ->waitForLocation('/admin/dashboard')
                ->visit('/admin/sales')
                ->assertPathIs('/admin/sales')
                ->waitForText('Data Penjualan');
        });
    }

    public function test_user_can_create_sale_with_valid_data(): void
    {
        [$user, $password, $customer, $branch, $product] = $this->createSaleFixtures();

        $this->browse(function (Browser $browser) use ($user, $password, $customer, $branch, $product) {
            $browser->loginAs($user)
                ->visit('/admin/sales')
                ->waitForText('Data Penjualan')
                ->script('window.localStorage.clear();');

            $browser->click('@btn-direct-katalog')
                ->waitFor("@product-card-{$product->id}")
                ->click("@btn-add-{$product->id}")
                ->waitFor("@btn-inc-{$product->id}")
                ->click("@btn-inc-{$product->id}")
                ->click('@btn-checkout')
                ->waitForText('Informasi Transaksi');

            $browser->type('@customer-search', $customer->nama)
                ->script(sprintf(
                    "document.getElementById('customer_id').value = %s;",
                    json_encode($customer->id)
                ));

            $browser->select('@select-cabang', (string) $branch->id)
                ->select('@select-kas', 'cash')
                ->click('@btn-proses-transaksi');
            $browser->pause(10000);
            $browser->waitForLocation('/admin/sales')
                ->assertPathIs('/admin/sales')
                ->waitUsing(10, 200, function (Browser $browser) {
                    return (bool) ($browser->script('return document.querySelector("[dusk=\"alert-success\"], .ui-alert--success, .alert-success, [role=\"alert\"].ui-alert--success") !== null;')[0] ?? false);
                })
                ->assertSee('Transaksi penjualan berhasil disimpan.');
        });

        // $this->assertDatabaseHas('penjualan', [
        //     'customer_id' => $customer->id,
        //     'perusahaan_cabang_id' => $branch->id,
        // ]);

        // $this->assertDatabaseHas('detail_penjualan', [
        //     'produk_id' => $product->id,
        //     'qty' => 2,
        // ]);
    }

    public function test_user_cannot_create_sale_without_customer(): void
    {
        [$user, $password, , $branch, $product] = $this->createSaleFixtures();
        $initialCount = Penjualan::count();

        $this->browse(function (Browser $browser) use ($user, $password, $branch, $product) {
            $browser->loginAs($user)
                ->visit('/admin/sales')
                ->waitForText('Data Penjualan')
                ->script('window.localStorage.clear();');

            $browser->click('@btn-direct-katalog')
                ->waitFor("@product-card-{$product->id}")
                ->click("@btn-add-{$product->id}")
                ->click('@btn-checkout')
                ->waitForText('Informasi Transaksi');

            $browser->select('@select-cabang', (string) $branch->id)
                ->select('@select-kas', 'cash')
                ->click('@btn-proses-transaksi')
                ->waitUsing(10, 200, function (Browser $browser) {
                    return (bool) ($browser->script('return document.querySelector("[dusk=\"alert-danger\"], .ui-alert--danger, .alert-danger, [role=\"alert\"].ui-alert--danger") !== null;')[0] ?? false);
                })
                ->assertSee('Customer wajib dipilih')
                ->assertPathIs('/admin/sales');
        });

        // $this->assertDatabaseCount('penjualan', $initialCount);
    }

    private function createSaleFixtures(): array
    {
        [$user, $password] = $this->getExistingAdminUser();

        $customer = Customer::orderBy('id')->first();
        if (! $customer) {
            $this->markTestSkipped('Customer tidak tersedia. Mohon seed data customer.');
        }

        $branch = Branch::where('is_active', true)->orderBy('id')->first()
            ?? Branch::orderBy('id')->first();
        if (! $branch) {
            $this->markTestSkipped('Cabang tidak tersedia. Mohon seed data cabang.');
        }

        $product = Produk::where(function ($q) {
                $q->whereNull('stok_produk')->orWhere('stok_produk', '>', 0);
            })
            ->orderBy('id')
            ->first();

        if (! $product) {
            $this->markTestSkipped('Produk dengan stok tersedia tidak ditemukan. Mohon seed produk.');
        }

        return [$user, $password, $customer, $branch, $product];
    }

    private function getExistingAdminUser(): array
    {
        $email = 'admin@dinoyokamera.com';
        $password = 'admin123';

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->markTestSkipped('User admin@dinoyokamera.com tidak tersedia. Seed database terlebih dahulu.');
        }

        return [$user, $password];
    }
}
