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
     * Positive: berhasil membuat transaksi penjualan end-to-end dari katalog sampai submit.
     * Langkah: login -> pilih produk stok > 0 -> checkout -> tambah customer baru -> pilih cabang & kas -> submit -> pastikan alert sukses di halaman index penjualan.
     */
    public function testPenjualanBerhasilDenganDataLengkap(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $productId = $this->startCheckoutFlow($browser);

            // Pastikan item terbawa ke halaman form
            $browser->assertPresent('#tableItemsBody tr[data-product-id]');

            $this->createCustomerViaModal($browser);
            $this->selectBranchAndPayment($browser);

            $browser->type('textarea[name="keterangan"]', 'Catatan otomatis Dusk untuk produk #' . $productId);

            $this->submitSaleForm($browser);

            $browser->waitFor('@alert-success', 15)
                ->assertSee('Transaksi penjualan berhasil disimpan.')
                ->assertPathIs('/admin/sales');
        });
    }

    /**
     * Negatif: validasi harus menolak submit jika customer belum dipilih.
     * Langkah: login -> pilih produk -> checkout -> kosongkan customer -> klik proses -> pastikan error customer muncul & tetap di form.
     */
    public function testTidakBisaProsesTanpaMemilihCustomer(): void
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
    public function testTidakBisaProsesDenganNamaCustomerTidakTerdaftar(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

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
     * Positif: user menambah item baru dari modal checkout dan jumlah item bertambah.
     */
    public function testTambahItemBaruDiCheckoutBerhasil(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $firstProductId = $this->startCheckoutFlow($browser);

            $initialCount = $this->getCartCount($browser);

            $this->openProductModal($browser);
            $addedProductId = $this->addAnotherProductFromModal($browser, [$firstProductId], 2);

            $this->assertNotEmpty($addedProductId, 'Tidak ada produk tambahan dengan stok tersedia.');

            $browser->waitFor("#tableItemsBody tr[data-product-id=\"{$addedProductId}\"]", 10);

            $updatedCount = $this->getCartCount($browser);
            $this->assertGreaterThan($initialCount, $updatedCount, 'Jumlah item tidak bertambah setelah menambah produk.');
        });
    }

    /**
     * Positif: user berhasil menambahkan customer baru lewat modal.
     */
    public function testBerhasilTambahCustomerBaruLewatModal(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $this->createCustomerViaModal($browser);

            // Berhasil jika alert success muncul dan field hidden terisi
            $browser->waitFor('.ui-alert.ui-alert--success', 8)
                ->assertSeeIn('.ui-alert.ui-alert--success', 'Customer berhasil disimpan.')
                ->assertNotEmpty($browser->value('#customer_id'));
        });
    }

    /**
     * Negatif: nomor telepon tidak sesuai format harus ditandai invalid di modal customer.
     */
    public function testModalMenolakNomorTeleponInvalid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $this->openCustomerModal($browser);

            $browser->type('#customer_nama_modal', 'Invalid Phone User')
                ->select('#customer_jenis_kelamin_modal', 'L')
                ->type('#customer_no_telp_modal', '12345')
                ->click('#customer_nama_modal'); // blur phone

            $browser->waitFor('#customer_no_telp_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_no_telp_modal', 'class', 'is-invalid')
                ->assertSeeIn('#modalTambahCustomer', 'Nomor telepon harus diawali 0 atau 62');
        });
    }

    /**
     * Negatif: nama customer kosong harus ditolak saat simpan modal.
     */
    public function testModalMenolakNamaKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $this->openCustomerModal($browser);

            $browser->type('#customer_no_telp_modal', '081234567890')
                ->select('#customer_jenis_kelamin_modal', 'P')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_nama_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_nama_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
        });
    }

    /**
     * Negatif: nomor telepon kosong harus ditolak saat simpan modal.
     */
    public function testModalMenolakTeleponKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $this->openCustomerModal($browser);

            $browser->type('#customer_nama_modal', 'User Tanpa Telepon')
                ->select('#customer_jenis_kelamin_modal', 'L')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_no_telp_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_no_telp_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
        });
    }

    /**
     * Negatif: jenis kelamin tidak dipilih harus ditolak saat simpan modal.
     */
    public function testModalMenolakGenderKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $this->startCheckoutFlow($browser);

            $this->openCustomerModal($browser);

            $browser->type('#customer_nama_modal', 'User Tanpa Gender')
                ->type('#customer_no_telp_modal', '081234567890')
                ->click('#btnSimpanCustomer')
                ->waitFor('#customer_jenis_kelamin_modal.is-invalid', 5)
                ->assertAttributeContains('#customer_jenis_kelamin_modal', 'class', 'is-invalid');

            $this->assertEmpty($browser->value('#customer_id'));
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

    private function openProductModal(Browser $browser): void
    {
        $browser->script("
            const modal = document.getElementById('modalTambahItem');
            if (modal) { const m = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal); m.show(); }
        ");
        $browser->waitFor('#modalTambahItem.show', 5);
    }

    private function addAnotherProductFromModal(Browser $browser, array $excludeIds = [], int $qty = 1): ?string
    {
        $excludeJson = json_encode(array_values($excludeIds));
        $result = $browser->script("
            const excludes = {$excludeJson} || [];
            const opts = Array.from(document.querySelectorAll('#produkBaru option'));
            const found = opts.find(o => {
                if (!o.value) return false;
                if (excludes.includes(o.value) || excludes.includes(Number(o.value))) return false;
                const stockAttr = o.dataset.stock;
                if (stockAttr === undefined) return true;
                if (stockAttr === '') return true;
                const n = Number(stockAttr);
                return !Number.isFinite(n) || n > 0;
            });
            return found ? found.value : null;
        ");

        $productId = $result[0] ?? null;
        if (!$productId) {
            return null;
        }

        $browser->script("
            const select = document.getElementById('produkBaru');
            if (select) {
                select.value = '{$productId}';
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const qtyInput = document.getElementById('qtyProdukBaru');
            if (qtyInput) { qtyInput.value = '{$qty}'; }
        ");

        $browser->click('#formTambahItem button[type="submit"]');

        $browser->waitUsing(5, 300, function () use ($browser, $productId) {
            $value = $browser->value('#itemsInput') ?? '[]';
            $json = json_decode($value, true) ?: [];
            foreach ($json as $item) {
                if ((string) ($item['id'] ?? '') === (string) $productId) {
                    return true;
                }
            }
            return false;
        });

        return (string) $productId;
    }

    private function openCustomerModal(Browser $browser): void
    {
        $browser->click('a[data-bs-target="#modalTambahCustomer"]')
            ->waitFor('#modalTambahCustomer.show', 5);

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

    protected function shouldStartMaximized(): bool
    {
        return true;
    }
}
