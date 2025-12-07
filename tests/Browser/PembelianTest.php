<?php

namespace Tests\Browser;

use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PembelianTest extends DuskTestCase
{
    private const ADMIN_EMAIL = 'admin@dinoyokamera.com';
    private const ADMIN_PASSWORD = 'admin123';

    private $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create('id_ID');
    }

    /**
     * Negatif: tombol tambah item harus menolak saat customer belum dipilih.
     */
    public function test01_TidakBisaBukaModalItemTanpaCustomer(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);

            // Paksa customer kosong supaya validasi dipicu
            $browser->script("
                const input = document.getElementById('customer_search');
                const hidden = document.getElementById('customer_id');
                if (input) { input.value = ''; input.classList.remove('is-valid', 'is-invalid'); }
                if (hidden) { hidden.value = ''; }
            ");

            $browser->click('#btnBukaModalItem')
                ->pause(300)
                ->assertAttributeContains('#customer_search', 'class', 'is-invalid')
                ->assertSeeIn('#customer_search_error', 'Customer wajib dipilih')
                ->assertMissing('#modalTambahItem.show');
        });
    }

    /**
     * Negatif: modal item menolak simpan ketika nama dan kategori kosong.
     */
    public function test02_ValidasiModalItemWajibNamaDanKategori(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);
            $this->ensureBranchSelected($browser);
            $this->createCustomerViaModal($browser);

            $browser->click('#btnBukaModalItem')
                ->waitFor('#modalTambahItem.show', 8)
                ->click('#btnSimpanItem')
                ->pause(300)
                ->assertAttributeContains('#item_nama_item', 'class', 'is-invalid')
                ->assertAttributeContains('#item_kategori_id', 'class', 'is-invalid');
        });
    }

    /**
     * Negatif: tombol DEAL tetap terkunci saat harga deal kosong walau item sudah ada.
     * Positif parsial: tombol aktif setelah harga deal diisi.
     */
    public function test03_TombolDealMenyalaSetelahHargaDiisi(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);
            $this->ensureBranchSelected($browser);
            $this->createCustomerViaModal($browser);

            $this->addItemViaModal($browser);

            $browser->waitFor('#btnDeal', 5);
            $this->assertButtonDisabled($browser, '#btnDeal', 'Tombol DEAL harus non-aktif ketika harga deal kosong.');

            $browser->type('#display_harga_deal', '2500000')
                ->pause(500);

            $this->assertButtonEnabled($browser, '#btnDeal', 'Tombol DEAL tidak aktif setelah harga deal diisi.');
        });
    }

    /**
     * Negatif: tidak boleh mengubah status (Draft/No-Deal/Deal) jika belum ada item.
     */
    public function test04_TombolStatusTerkunciJikaBelumAdaItem(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);
            $this->ensureBranchSelected($browser);

            $browser->waitFor('#btnDraft', 5);
            $this->assertButtonDisabled($browser, '#btnDraft', 'Draft harus terkunci ketika keranjang kosong.');
            $this->assertButtonDisabled($browser, '#btnNoDeal', 'No-Deal harus terkunci ketika keranjang kosong.');
            $this->assertButtonDisabled($browser, '#btnDeal', 'Deal harus terkunci ketika keranjang kosong.');
        });
    }

    /**
     * Positif: berhasil menambah customer baru dari modal dan terisi ke form.
     */
    public function test10_BerhasilTambahCustomerBaruViaModal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);
            [$name] = $this->createCustomerViaModal($browser);

            $browser->waitFor('.ui-alert.ui-alert--success', 8)
                ->assertSeeIn('.ui-alert.ui-alert--success', 'Customer berhasil disimpan.');

            $browser->waitUsing(8, 300, function () use ($browser, $name) {
                $value = $browser->value('#customer_search');
                return is_string($value) && str_contains($value, $name);
            }, 'Nama customer baru belum terisi di input pencarian.');

            $this->assertNotEmpty($browser->value('#customer_id'), 'Customer ID tidak terisi setelah simpan.');
        });
    }

    /**
     * Positif: alur lengkap membuat pembelian deal lalu muncul di halaman index.
     */
    public function test11_BerhasilMenyelesaikanPembelianDeal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->openPurchaseForm($browser);
            $this->ensureBranchSelected($browser);

            [$customerName] = $this->createCustomerViaModal($browser);
            [$itemName] = $this->addItemViaModal($browser);

            $browser->type('#display_harga_tawaran_customer', '1500000')
                ->type('#display_harga_tawaran_toko', '1200000')
                ->type('#display_harga_deal', '1250000')
                ->pause(500);

            $this->assertButtonEnabled($browser, '#btnDeal', 'Tombol DEAL masih terkunci padahal data lengkap.');

            $browser->click('#btnDeal')
                ->waitFor('@alert-success', 15)
                ->assertSeeIn('@alert-success', 'Transaksi pembelian telah difinalisasi.')
                ->assertPathIs('/admin/purchases')
                ->waitFor('.purchase-row', 10);

            $browser->waitUsing(8, 400, function () use ($browser, $customerName, $itemName) {
                $text = $browser->text('#purchase-list-container');
                return str_contains($text, $customerName) || str_contains($text, $itemName);
            }, 'Data pembelian baru belum muncul pada tabel.');
        });
    }

    /**
     * Positif: simpan sebagai Draft dan pastikan link bagikan tersedia di halaman detail.
     */
    public function test12_BerhasilSimpanDraftPembelian(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            [$pembelianId, $customerName, $itemName] = $this->startPurchaseWithItem($browser);

            $browser->type('#display_harga_tawaran_customer', '850000')
                ->pause(200)
                ->click('#btnDraft')
                ->waitFor('@alert-success', 12)
                ->assertSeeIn('@alert-success', 'Draft berhasil disimpan')
                ->waitForLocation("/admin/purchases/{$pembelianId}", 10)
                ->assertSee('Detail Pembelian')
                ->assertSee($customerName)
                ->assertSee($itemName)
                ->assertSee('DRAFT');

            $shareLink = (string) ($browser->value('#shareable-link') ?? '');
            $this->assertStringContainsString("/admin/purchases/{$pembelianId}", $shareLink, 'Link bagikan tidak berisi ID pembelian.');
        });
    }

    /**
     * Positif: menetapkan status pembelian menjadi Tidak Deal.
     */
    public function test13_BerhasilSetMenjadiTidakDeal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            [, , $itemName] = $this->startPurchaseWithItem($browser);

            $browser->type('#display_harga_tawaran_customer', '500000')
                ->type('#display_harga_tawaran_toko', '400000')
                ->pause(200)
                ->click('#btnNoDeal')
                ->waitFor('@alert-success', 15)
                ->assertSeeIn('@alert-success', 'Transaksi pembelian telah difinalisasi.')
                ->assertPathIs('/admin/purchases')
                ->waitFor('.purchase-row', 10);

            $badgeText = $browser->script("
                const rows = Array.from(document.querySelectorAll('tr.purchase-row'));
                const target = rows.find(r => r.textContent.includes({$this->jsonEncode($itemName)}));
                if (!target) return null;
                const badge = target.querySelector('.badge');
                return badge ? badge.textContent.trim() : null;
            ")[0] ?? null;

            $this->assertSame('No-Deal', $badgeText, 'Status No-Deal tidak tampil pada baris pembelian.');
        });
    }

    /**
     * Positif: melihat detail pembelian (read-only) dan link bagikan readonly tersedia.
     */
    public function test14_BisaMelihatDetailPembelianDanBagikan(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            [, $customerName, $itemName] = $this->startPurchaseWithItem($browser);

            $browser->type('#display_harga_deal', '990000')
                ->pause(200)
                ->click('#btnDeal')
                ->waitFor('@alert-success', 15)
                ->assertPathIs('/admin/purchases')
                ->waitFor('.purchase-row', 10);

            $detailUrl = $browser->script("
                const rows = Array.from(document.querySelectorAll('tr.purchase-row'));
                const target = rows.find(r => r.textContent.includes({$this->jsonEncode($itemName)}));
                return target ? target.dataset.detailUrl : null;
            ")[0] ?? null;
            $this->assertNotEmpty($detailUrl, 'URL detail pembelian tidak ditemukan.');

            $browser->visit($detailUrl)
                ->waitForText('Informasi Transaksi', 10)
                ->assertSee('Detail Pembelian')
                ->assertSee($customerName)
                ->assertSee($itemName)
                ->assertSee('DEAL')
                ->assertPresent('#shareable-link');

            $shareValue = (string) ($browser->value('#shareable-link') ?? '');
            $this->assertStringContainsString('/admin/purchases/', $shareValue, 'Link share tidak sesuai format.');
        });
    }

    /**
     * Positif: tombol cetak nota tersedia untuk transaksi Deal.
     */
    public function test15_TombolCetakNotaTersediaUntukDeal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            [, , $itemName] = $this->startPurchaseWithItem($browser);

            $browser->type('#display_harga_deal', '1110000')
                ->pause(200)
                ->click('#btnDeal')
                ->waitFor('@alert-success', 15)
                ->assertPathIs('/admin/purchases')
                ->waitFor('.purchase-row', 10);

            $detailUrl = $browser->script("
                const rows = Array.from(document.querySelectorAll('tr.purchase-row'));
                const target = rows.find(r => r.textContent.includes({$this->jsonEncode($itemName)}));
                return target ? target.dataset.detailUrl : null;
            ")[0] ?? null;
            $this->assertNotEmpty($detailUrl, 'Detail URL tidak ditemukan untuk transaksi Deal.');

            $browser->visit($detailUrl)
                ->waitForText('Informasi Transaksi', 10);

            $browser->waitFor("a[href*=\"/admin/purchases/\"]", 5)
                ->assertPresent("a[href*=\"/admin/purchases/\"][href$=\"/print\"]")
                ->click("a[href*=\"/admin/purchases/\"][href$=\"/print\"]");

            // Tidak memeriksa output PDF, hanya memastikan tombol dapat diklik.
        });
    }

    /**
     * Negatif: tombol cetak nota tidak tersedia untuk transaksi yang bukan Deal.
     */
    public function test16_TidakAdaTombolCetakUntukNonDeal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            [, , $itemName] = $this->startPurchaseWithItem($browser);

            $browser->type('#display_harga_tawaran_customer', '750000')
                ->pause(200)
                ->click('#btnNoDeal')
                ->waitFor('@alert-success', 15)
                ->assertPathIs('/admin/purchases')
                ->waitFor('.purchase-row', 10);

            $detailUrl = $browser->script("
                const rows = Array.from(document.querySelectorAll('tr.purchase-row'));
                const target = rows.find(r => r.textContent.includes({$this->jsonEncode($itemName)}));
                return target ? target.dataset.detailUrl : null;
            ")[0] ?? null;
            $this->assertNotEmpty($detailUrl, 'Detail URL tidak ditemukan untuk transaksi No-Deal.');

            $browser->visit($detailUrl)
                ->waitForText('Informasi Transaksi', 10)
                ->assertSee('TIDAK DEAL')
                ->assertMissing("a[href$=\"/print\"]");
        });
    }
    private function loginAsAdmin(Browser $browser): void
    {
        $browser->driver->manage()->deleteAllCookies();

        $browser->visit('/login')
            ->waitFor('#loginForm', 10);

        $browser->script([
            'localStorage.clear();',
            'sessionStorage.clear();',
        ]);

        $browser->type('input[name="email"]', self::ADMIN_EMAIL)
            ->type('input[name="password"]', self::ADMIN_PASSWORD)
            ->press('button[type="submit"]')
            ->waitForLocation('/admin/dashboard', 15)
            ->assertPathIs('/admin/dashboard');
    }

    private function openPurchaseForm(Browser $browser): void
    {
        $browser->visit('/admin/purchases/create')
            ->waitFor('#formPembelian', 15)
            ->assertSee('Informasi Transaksi');
    }

    private function ensureBranchSelected(Browser $browser): string
    {
        $branchId = $browser->script("
            const select = document.getElementById('perusahaan_cabang_id');
            if (!select) return null;
            if (!select.value && select.options.length) {
                select.value = select.options[0].value;
            }
            select.classList.remove('is-invalid');
            return select.value || null;
        ")[0] ?? null;

        $this->assertNotEmpty($branchId, 'Tidak ada cabang aktif yang bisa dipilih.');
        $browser->select('#perusahaan_cabang_id', (string) $branchId);

        return (string) $branchId;
    }

    private function createCustomerViaModal(Browser $browser): array
    {
        $name = 'Dusk Customer ' . $this->faker->firstName();
        $phone = '08' . $this->faker->numerify('##########');
        $gender = $this->faker->randomElement(['L', 'P']);

        $browser->click('a[data-bs-target="#modalTambahCustomer"]')
            ->waitFor('#modalTambahCustomer.show', 8)
            ->within('#modalTambahCustomer', function (Browser $modal) use ($name, $phone, $gender) {
                $modal->type('#customer_nama_modal', $name)
                    ->type('#customer_no_telp_modal', $phone)
                    ->select('#customer_jenis_kelamin_modal', $gender)
                    ->click('#btnSimpanCustomer');
            });

        $browser->waitUntilMissing('#modalTambahCustomer.show', 8);

        $browser->waitUsing(12, 400, function () use ($browser) {
            return !empty($browser->value('#customer_id'));
        }, 'Customer baru tidak tersimpan atau tidak terpilih.');

        $browser->waitUsing(8, 300, function () use ($browser, $name) {
            $value = $browser->value('#customer_search');
            return is_string($value) && str_contains($value, $name);
        }, 'Input customer tidak terisi dengan nama baru.');

        return [$name, $phone];
    }

    private function addItemViaModal(Browser $browser, ?string $itemName = null): array
    {
        $this->assertNotEmpty($browser->value('#customer_id'), 'Customer belum dipilih sebelum menambah item.');
        $itemName = $itemName ?: 'Item Dusk ' . $this->faker->numerify('###');
        $itemNameJson = json_encode($itemName);

        $browser->click('#btnBukaModalItem')
            ->waitFor('#modalTambahItem.show', 8);

        $categoryId = $browser->script("
            const select = document.getElementById('item_kategori_id');
            if (!select) return null;
            const opt = Array.from(select.options).find(o => o.value);
            return opt ? opt.value : null;
        ")[0] ?? null;

        $this->assertNotEmpty($categoryId, 'Tidak ada kategori tersedia untuk item pembelian.');

        $browser->type('#item_nama_item', $itemName)
            ->select('#item_kategori_id', (string) $categoryId)
            ->type('#item_serial_number', 'SN' . $this->faker->numerify('######'))
            ->click('#btnSimpanItem');

        $browser->waitUntilMissing('#modalTambahItem.show', 12);

        $browser->waitUsing(12, 400, function () use ($browser, $itemNameJson) {
            $result = $browser->script("
                const rows = document.querySelectorAll('#item-list-wrapper tr');
                return Array.from(rows).some(r => r.textContent.includes({$itemNameJson}));
            ");
            return (bool) ($result[0] ?? false);
        }, 'Item tidak muncul di tabel pembelian.');

        $pembelianId = $browser->value('#pembelian_id_hidden');
        $this->assertNotEmpty($pembelianId, 'Pembelian ID tidak terisi setelah menyimpan item.');

        return [$itemName, (string) $pembelianId];
    }

    private function startPurchaseWithItem(Browser $browser): array
    {
        $this->openPurchaseForm($browser);
        $this->ensureBranchSelected($browser);
        [$customerName, $phone] = $this->createCustomerViaModal($browser);
        [$itemName, $pembelianId] = $this->addItemViaModal($browser);

        return [$pembelianId, $customerName, $itemName];
    }

    private function jsonEncode(string $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    private function assertButtonDisabled(Browser $browser, string $selector, string $message = ''): void
    {
        $selectorJson = json_encode($selector);
        $disabled = $browser->script("
            const el = document.querySelector({$selectorJson});
            return el ? el.disabled : null;
        ")[0] ?? null;

        $this->assertTrue((bool) $disabled, $message ?: "{$selector} seharusnya disable.");
    }

    private function assertButtonEnabled(Browser $browser, string $selector, string $message = ''): void
    {
        $selectorJson = json_encode($selector);
        $disabled = $browser->script("
            const el = document.querySelector({$selectorJson});
            return el ? el.disabled : null;
        ")[0] ?? null;

        $this->assertFalse((bool) $disabled, $message ?: "{$selector} seharusnya aktif.");
    }

    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
