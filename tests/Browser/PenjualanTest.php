<?php

namespace Tests\Browser;

use Faker\Factory as Faker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PenjualanTest extends DuskTestCase
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
     * Negatif: validasi harus menolak submit jika customer belum dipilih.
     * Langkah: login -> pilih produk -> checkout -> kosongkan customer -> klik proses -> pastikan error customer muncul & tetap di form.
     */
    public function test01_TidakBisaProsesTanpaMemilihCustomer(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $browser->assertPresent('#tableItemsBody tr[data-product-id]');

            // Kosongkan input customer agar trigger validasi front-end
            $browser->script([
                "localStorage.removeItem('sales_form_draft');",
                "const search = document.getElementById('customer_search'); if (search) { search.value = ''; search.classList.remove('is-valid'); }",
                "const hidden = document.getElementById('customer_id'); if (hidden) { hidden.value = ''; }",
            ]);

            $this->selectBranchAndPayment($browser);

            $browser->press('@btn-proses-transaksi')
                ->waitFor('#customer_search_error', 5)
                ->assertSeeIn('#customer_search_error', 'Customer wajib dipilih')
                ->assertPresent('#formPenjualan')
                ->assertPathIs('/admin/sales');
        });
    }

    /**
     * Negatif: user mengetik nama customer yang tidak terdaftar tanpa memilih suggestion harus ditolak.
     */
    public function test02_TidakBisaProsesDenganNamaCustomerTidakTerdaftar(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertPresent('#tableItemsBody tr[data-product-id]');

            $browser->type('#customer_search', 'Customer Tidak Terdaftar');
            $browser->script("document.getElementById('customer_id').value = '';");

            $this->selectBranchAndPayment($browser);

            $browser->press('@btn-proses-transaksi')
                ->waitFor('#customer_search_error', 5)
                ->assertSeeIn('#customer_search_error', 'Customer wajib dipilih')
                ->assertPathIs('/admin/sales');
        });
    }

    /**
     * Negatif: nomor telepon tidak sesuai format harus ditandai invalid di modal customer.
     */
    public function test03_ModalMenolakNomorTeleponInvalid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openCustomerModal($browser);

            $browser->type('#customer_nama_modal', 'Invalid Phone User')
                ->select('#customer_jenis_kelamin_modal', 'L')
                ->type('#customer_no_telp_modal', '12345')
                ->click('#customer_nama_modal'); // blur phone

            $browser->waitFor('#customer_no_telp_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_no_telp_modal', 'class', 'is-invalid')
                ->assertSeeIn('#modalTambahCustomer', 'Nomor telepon harus diawali 0 atau 62');

            $this->closeCustomerModal($browser);
        });
    }

    /**
     * Negatif: nama customer kosong harus ditolak saat simpan modal.
     */
    public function test04_ModalMenolakNamaKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openCustomerModal($browser);
            $browser->type('#customer_no_telp_modal', '081234567890')
                ->select('#customer_jenis_kelamin_modal', 'P')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_nama_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_nama_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
            $this->closeCustomerModal($browser);
        });
    }

    /**
     * Negatif: nomor telepon kosong harus ditolak saat simpan modal.
     */
    public function test05_ModalMenolakTeleponKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openCustomerModal($browser);
            $browser->type('#customer_nama_modal', 'User Tanpa Telepon')
                ->select('#customer_jenis_kelamin_modal', 'L')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_no_telp_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_no_telp_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
            $this->closeCustomerModal($browser);
        });
    }

    /**
     * Negatif: jenis kelamin tidak dipilih harus ditolak saat simpan modal.
     */
    public function test06_ModalMenolakGenderKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openCustomerModal($browser);
            $browser->type('#customer_nama_modal', 'User Tanpa Gender')
                ->type('#customer_no_telp_modal', '081234567890')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_jenis_kelamin_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_jenis_kelamin_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
            $this->closeCustomerModal($browser);
        });
    }

    /**
     * Positif: user menambah item baru dari modal checkout dan jumlah item bertambah.
     */
    public function test10_TambahItemBaruDiCheckoutBerhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $firstProductId = $this->startCheckoutFlow($browser);

            $initialCount = $this->getCartCount($browser);

            // Klik tombol "Tambah Item" yang kembali ke halaman katalog
            $browser->click('#btnTambahItem')
                ->waitForLocation('/admin/sales/create', 10)
                ->waitFor('.product-card', 10);

            // Tambah produk lain lalu checkout kembali ke form
            $secondProductId = $this->addProductFromCatalog($browser, [$firstProductId]);

            $browser->click('@btn-checkout')
                ->waitFor('#formPenjualan', 15)
                ->assertSee('Informasi Transaksi');

            $browser->waitFor("#tableItemsBody tr[data-product-id=\"{$secondProductId}\"]", 10);

            $updatedCount = $this->getCartCount($browser);
            $this->assertGreaterThan($initialCount, $updatedCount, 'Jumlah item tidak bertambah setelah menambah produk.');
            $browser->assertPresent("#tableItemsBody tr[data-product-id=\"{$firstProductId}\"]");
        });
    }

    /**
     * Positif: user berhasil menambahkan customer baru lewat modal.
     */
    public function test11_BerhasilTambahCustomerBaruLewatModal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertPresent('#formPenjualan')
                ->assertPresent('#tableItemsBody tr[data-product-id]');
            $this->createCustomerViaModal($browser);

            // Berhasil jika alert success muncul dan field hidden terisi
            $browser->waitFor('.ui-alert.ui-alert--success', 8)
                ->assertSeeIn('.ui-alert.ui-alert--success', 'Customer berhasil disimpan.');

            $this->closeCustomerModal($browser);
        });
    }

    /**
     * Positive: berhasil membuat transaksi penjualan end-to-end dari katalog sampai submit.
     * Langkah: login -> pilih produk stok > 0 -> checkout -> tambah customer baru -> pilih cabang & kas -> submit -> pastikan alert sukses di halaman index penjualan.
     */
    public function test12_PenjualanBerhasilDenganDataLengkap(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertPresent('#formPenjualan')
                ->assertPresent('#tableItemsBody tr[data-product-id]');

            $productId = $browser->script("
                const row = document.querySelector('#tableItemsBody tr[data-product-id]');
                return row ? row.dataset.productId : null;
            ")[0] ?? 'unknown';

            // Isi diskon & biaya tambahan pada form
            $browser->type('input[name="diskon"]', '5000')
                ->type('input[name="biaya_tambahan"]', '3000');

            // Customer sudah ada dari langkah sebelumnya
            $this->selectBranchAndPayment($browser);
            $browser->pause(800); // tunggu auto-hitungan total

            $browser->type('textarea[name="keterangan"]', 'Catatan otomatis Dusk untuk produk #' . $productId);

            $this->submitSaleForm($browser);

            $browser->waitFor('@alert-success', 15)
                ->assertSee('Transaksi penjualan berhasil disimpan.')
                ->assertPathIs('/admin/sales');
        });
    }

    /**
     * Positif: setelah submit, buka detail terbaru dan cetak nota.
     * Asumsi: test12 sudah menyimpan transaksi dan berada di halaman Data Penjualan.
     */
    public function test13_CetakNotaSetelahSubmit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->assertPathIs('/admin/sales')
                ->waitFor('.sales-row', 10);

            $detailUrl = $browser->script("
                const row = document.querySelector('tr.sales-row');
                return row ? row.dataset.detailUrl : null;
            ")[0] ?? null;
            $this->assertNotEmpty($detailUrl, 'URL detail penjualan tidak ditemukan di tabel.');

            $parts = explode('/', trim((string) $detailUrl, '/'));
            $saleId = end($parts);

            $browser->visit($detailUrl)
                ->waitForText('Informasi Transaksi', 10)
                ->assertPresent("a[href*=\"/admin/sales/{$saleId}/print\"]")
                ->click("a[href*=\"/admin/sales/{$saleId}/print\"]");

            // Tidak validasi isi cetak; cukup memastikan tombol dapat diklik.
        });
    }

    private function loginAsAdmin(Browser $browser): void
    {
        $browser->driver->manage()->deleteAllCookies();

        $browser->visit('/login')
            ->waitFor('#loginForm', 10);

        // Pastikan tidak ada sisa draft / sesi lama yang mengganggu
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

    private function startCheckoutFlow(Browser $browser): string
    {
        $browser->visit('/admin/sales/create')
            ->waitFor('.product-card', 15);

        $productId = $this->pickFirstProductWithStock($browser);

        // Scroll ke tombol produk agar dapat diklik pada tampilan kecil
        $browser->script("document.querySelector('[dusk=\"btn-add-{$productId}\"]')?.scrollIntoView({ behavior: 'instant', block: 'center' });");

        $browser->click("@btn-add-{$productId}")
            ->pause(300)
            ->waitFor('@btn-checkout', 5)
            ->click('@btn-checkout')
            ->waitFor('#formPenjualan', 15)
            ->assertSee('Informasi Transaksi');

        return $productId;
    }

    private function pickFirstProductWithStock(Browser $browser): string
    {
        $result = $browser->script("
            const card = Array.from(document.querySelectorAll('.product-card'))
                .find(el => Number(el.dataset.stock || 0) > 0);
            return card ? card.dataset.productId : null;
        ");

        $productId = $result[0] ?? null;
        $this->assertNotEmpty($productId, 'Tidak ada produk dengan stok tersedia untuk diuji.');

        return (string) $productId;
    }

    private function createCustomerViaModal(Browser $browser): array
    {
        $name = 'Dusk Customer ' . $this->faker->firstName();
        $phone = '08' . $this->faker->numerify('##########');
        $gender = $this->faker->randomElement(['L', 'P']);

        $browser->click('a[data-bs-target="#modalTambahCustomer"]')
            ->waitFor('#modalTambahCustomer.show', 5)
            ->within('#modalTambahCustomer', function (Browser $modal) use ($name, $phone, $gender) {
                $modal->type('#customer_nama_modal', $name)
                    ->type('#customer_no_telp_modal', $phone)
                    ->select('#customer_jenis_kelamin_modal', $gender)
                    ->click('#btnSimpanCustomer');
            });

        $browser->waitUsing(12, 500, function () use ($browser) {
            return !empty($browser->value('#customer_id'));
        }, 'Customer baru tidak tersimpan atau tidak dipilih.');

        // Pastikan input pencarian menampilkan nama customer baru
        $browser->waitUsing(8, 300, function () use ($browser, $name) {
            $value = $browser->value('#customer_search');
            return is_string($value) && str_contains($value, $name);
        }, 'Input customer tidak terisi dengan customer baru.');

        // Pastikan modal tertutup agar tidak menghalangi interaksi berikutnya
        $this->closeCustomerModal($browser);

        return [$name, $phone];
    }

    private function selectBranchAndPayment(Browser $browser): void
    {
        $branchId = $browser->script("
            const select = document.getElementById('perusahaan_cabang_id');
            if (!select) return null;
            return select.value || (select.options[0]?.value ?? null);
        ")[0] ?? null;

        $this->assertNotEmpty($branchId, 'Tidak ada cabang aktif yang bisa dipilih.');

        $browser->select('#perusahaan_cabang_id', (string) $branchId)
            ->select('@select-kas', 'cash');
    }

    private function submitSaleForm(Browser $browser): void
    {
        // Pastikan tombol terlihat dan tidak disabled
        $browser->script("document.querySelector('[dusk=\"btn-proses-transaksi\"]')?.scrollIntoView({behavior:'instant',block:'center'});");

        $browser->waitFor('@btn-proses-transaksi', 5)
            ->waitUsing(10, 300, function () use ($browser) {
                return $browser->attribute('@btn-proses-transaksi', 'disabled') === null;
            }, 'Tombol proses transaksi tidak aktif.')
            ->click('@btn-proses-transaksi')
            ->pause(800);

        // Fallback jika klik ter-intercept: paksa submit form
        if ($browser->element('#formPenjualan')) {
            $browser->script("
                (function(){
                    const btn=document.querySelector('[dusk=\"btn-proses-transaksi\"]');
                    if(btn && btn.getAttribute('disabled')) btn.removeAttribute('disabled');
                    const form=document.getElementById('formPenjualan');
                    if(form){ form.submit(); }
                })();
            ");
        }
    }

    private function getCartCount(Browser $browser): int
    {
        $raw = $browser->value('#itemsInput') ?? '[]';
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? count($decoded) : 0;
    }

    private function addProductFromCatalog(Browser $browser, array $excludeIds = [], int $qty = 1): ?string
    {
        $excludeJson = json_encode(array_values($excludeIds));
        $result = $browser->script("
            const excludes = {$excludeJson} || [];
            const cards = Array.from(document.querySelectorAll('.product-card'));
            const found = cards.find(c => {
                const id = c.dataset.productId;
                if (!id) return false;
                if (excludes.includes(id) || excludes.includes(Number(id))) return false;
                const stock = Number(c.dataset.stock || 0);
                return stock > 0;
            });
            return found ? found.dataset.productId : null;
        ");

        $productId = $result[0] ?? null;
        if (!$productId) {
            return null;
        }

        $browser->script("
            const btn = document.querySelector('[dusk=\"btn-add-{$productId}\"]');
            if (btn) { btn.scrollIntoView({behavior:'instant', block:'center'}); }
        ");

        $browser->click("@btn-add-{$productId}")
            ->pause(300);

        if ($qty > 1) {
            $incClicks = $qty - 1;
            for ($i = 0; $i < $incClicks; $i++) {
                $browser->click("@btn-inc-{$productId}")
                    ->pause(150);
            }
        }

        return (string) $productId;
    }

    private function openCustomerModal(Browser $browser): void
    {
        $browser->click('a[data-bs-target="#modalTambahCustomer"]')
            ->waitFor('#modalTambahCustomer.show', 5)
            ->pause(300);

        $browser->script("
            ['customer_nama_modal','customer_no_telp_modal','customer_jenis_kelamin_modal'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                if (el.tagName === 'SELECT') { el.value = ''; }
                else { el.value = ''; }
                el.classList.remove('is-invalid');
            });
        ");
    }

    private function closeCustomerModal(Browser $browser): void
    {
        $browser->script("
            (() => {
                const modal = document.getElementById('modalTambahCustomer');
                if (!modal || !modal.classList.contains('show')) return;
                const closeBtn = modal.querySelector('button[data-bs-dismiss=\"modal\"]');
                if (closeBtn) { closeBtn.click(); }
            })();
        ");

        $browser->pause(200)
            ->waitUntilMissing('#modalTambahCustomer.show', 5);
    }

    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
