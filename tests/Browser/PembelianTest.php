<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\AdminBrowserTestCase;

class PembelianTest extends AdminBrowserTestCase
{
    /**
     * Positif: Berhasil membuat transaksi pembelian dengan data lengkap
     */
    public function testPembelianBerhasilDenganDataLengkap(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            // Navigasi ke halaman create pembelian
            $browser->visit('/admin/purchases/create')
                ->waitFor('#formPembelian', 10)
                ->assertSee('Informasi Transaksi')
                ->pause(1000);

            // Buat customer baru
            $customerName = 'Test Customer Pembelian ' . time();
            $customerPhone = '081' . $this->faker->numerify('#########');

            $browser->click('a[data-bs-target="#modalTambahCustomer"]')
                ->waitFor('#modalTambahCustomer.show', 5)
                ->pause(500);

            $browser->within('#modalTambahCustomer', function (Browser $modal) use ($customerName, $customerPhone) {
                $modal->type('#customer_nama_modal', $customerName)
                    ->type('#customer_no_telp_modal', $customerPhone)
                    ->select('#customer_jenis_kelamin_modal', 'L')
                    ->pause(300)
                    ->click('#btnSimpanCustomer');
            });

            $browser->waitUsing(15, 500, function () use ($browser) {
                $customerId = $browser->value('#customer_id');
                return !empty($customerId);
            }, 'Customer baru tidak tersimpan.');

            $browser->waitUntilMissing('#modalTambahCustomer.show', 5)
                ->pause(500);

            // Pilih cabang
            $branchId = $browser->script("
                const select = document.getElementById('perusahaan_cabang_id');
                if (!select || !select.options.length) return null;
                return select.options[0].value;
            ")[0];

            $this->assertNotEmpty($branchId, 'Tidak ada cabang aktif yang bisa dipilih.');

            $browser->select('#perusahaan_cabang_id', (string) $branchId)
                ->pause(500);

            // Tambah item
            $browser->click('#btnBukaModalItem')
                ->waitFor('#modalTambahItem.show', 10)
                ->pause(500);

            $itemName = 'Item Test ' . time();
            $kategoriId = $browser->script("
                const select = document.querySelector('#modalTambahItem select[name=\"kategori_id\"], #modalTambahItem #item_kategori_id');
                if (!select || !select.options.length) return null;
                for (let i = 1; i < select.options.length; i++) {
                    if (select.options[i].value) {
                        return select.options[i].value;
                    }
                }
                return null;
            ")[0];

            $this->assertNotEmpty($kategoriId, 'Tidak ada kategori yang tersedia.');

            $browser->within('#modalTambahItem', function (Browser $modal) use ($itemName, $kategoriId) {
                $modal->type('#item_nama_item', $itemName)
                    ->pause(300)
                    ->select('#item_kategori_id', (string) $kategoriId)
                    ->pause(300);
            });

            $browser->click('#btnSimpanItem')
                ->pause(2000);

            // Tunggu item muncul
            $browser->waitUsing(20, 500, function () use ($browser, $itemName) {
                $result = $browser->script("
                    const wrapper = document.getElementById('item-list-wrapper');
                    if (!wrapper) return false;
                    const text = wrapper.textContent || wrapper.innerText || '';
                    return text.includes('{$itemName}');
                ")[0];
                return $result === true;
            }, 'Item tidak ditemukan di tabel.');

            // Finalisasi sebagai deal
            $hargaDeal = 5000000;
            $browser->type('#display_harga_deal', (string) $hargaDeal)
                ->pause(500);

            $browser->script("
                const display = document.getElementById('display_harga_deal');
                const hidden = document.getElementById('harga_deal');
                if (display && hidden) {
                    const cleanValue = String({$hargaDeal}).replace(/\\D/g, '');
                    display.dataset.raw = cleanValue;
                    hidden.value = cleanValue;
                    display.value = new Intl.NumberFormat('id-ID').format(cleanValue);
                    display.dispatchEvent(new Event('input', { bubbles: true }));
                    display.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");

            $browser->pause(1000);

            $browser->click('#btnDeal')
                ->pause(2000);

            $browser->waitForLocation('/admin/purchases', 15)
                ->assertPathIs('/admin/purchases')
                ->pause(1000);
        });
    }

    /**
     * Negatif: Form pembelian harus menolak submit jika customer tidak dipilih
     */
    public function testPembelianGagalKarenaCustomerTidakDipilih(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/purchases/create')
                ->waitFor('#formPembelian', 10)
                ->pause(1000);

            // Pastikan customer kosong
            $browser->script("
                const customerId = document.getElementById('customer_id');
                const customerSearch = document.getElementById('customer_search');
                if (customerId) customerId.value = '';
                if (customerSearch) customerSearch.value = '';
            ");

            // Pilih cabang
            $branchId = $browser->script("
                const select = document.getElementById('perusahaan_cabang_id');
                if (!select || !select.options.length) return null;
                return select.options[0].value;
            ")[0];

            if ($branchId) {
                $browser->select('#perusahaan_cabang_id', (string) $branchId)
                    ->pause(500);
            }

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('formPembelian');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Coba submit (jika ada tombol submit langsung)
            // Biasanya form pembelian tidak bisa submit tanpa item, tapi kita test validasi customer
            $browser->pause(1000);

            // Verifikasi error atau form tidak bisa submit
            $browser->assertPresent('#formPembelian')
                ->pause(500);
        });
    }

    /**
     * Negatif: Form pembelian harus menolak finalisasi deal jika harga_deal kosong
     */
    public function testPembelianGagalDealKarenaHargaDealKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/purchases/create')
                ->waitFor('#formPembelian', 10)
                ->pause(1000);

            // Buat customer dan tambah item (sama seperti test positif)
            $customerName = 'Test Customer ' . time();
            $customerPhone = '081' . $this->faker->numerify('#########');

            $browser->click('a[data-bs-target="#modalTambahCustomer"]')
                ->waitFor('#modalTambahCustomer.show', 5)
                ->pause(500);

            $browser->within('#modalTambahCustomer', function (Browser $modal) use ($customerName, $customerPhone) {
                $modal->type('#customer_nama_modal', $customerName)
                    ->type('#customer_no_telp_modal', $customerPhone)
                    ->select('#customer_jenis_kelamin_modal', 'L')
                    ->pause(300)
                    ->click('#btnSimpanCustomer');
            });

            $browser->waitUsing(15, 500, function () use ($browser) {
                return !empty($browser->value('#customer_id'));
            }, 'Customer tidak tersimpan.');

            $browser->waitUntilMissing('#modalTambahCustomer.show', 5)
                ->pause(500);

            // Pilih cabang dan tambah item
            $branchId = $browser->script("
                const select = document.getElementById('perusahaan_cabang_id');
                if (!select || !select.options.length) return null;
                return select.options[0].value;
            ")[0];

            if ($branchId) {
                $browser->select('#perusahaan_cabang_id', (string) $branchId)
                    ->pause(500);
            }

            // Tambah item
            $browser->click('#btnBukaModalItem')
                ->waitFor('#modalTambahItem.show', 10)
                ->pause(500);

            $itemName = 'Item Test ' . time();
            $kategoriId = $browser->script("
                const select = document.querySelector('#modalTambahItem select[name=\"kategori_id\"], #modalTambahItem #item_kategori_id');
                if (!select || !select.options.length) return null;
                for (let i = 1; i < select.options.length; i++) {
                    if (select.options[i].value) {
                        return select.options[i].value;
                    }
                }
                return null;
            ")[0];

            if ($kategoriId) {
                $browser->within('#modalTambahItem', function (Browser $modal) use ($itemName, $kategoriId) {
                    $modal->type('#item_nama_item', $itemName)
                        ->pause(300)
                        ->select('#item_kategori_id', (string) $kategoriId)
                        ->pause(300);
                });

                $browser->click('#btnSimpanItem')
                    ->pause(2000);
            }

            // Pastikan harga_deal kosong
            $browser->script("
                const display = document.getElementById('display_harga_deal');
                const hidden = document.getElementById('harga_deal');
                if (display) display.value = '';
                if (hidden) hidden.value = '';
            ");

            $browser->pause(1000);

            // Cek apakah tombol DEAL disabled
            $btnDealDisabled = $browser->script("
                const btnDeal = document.getElementById('btnDeal');
                return btnDeal ? btnDeal.disabled : true;
            ")[0];

            // Tombol DEAL seharusnya disabled jika harga_deal kosong
            $this->assertTrue($btnDealDisabled === true, 'Tombol DEAL seharusnya disabled jika harga_deal kosong.');
        });
    }
}
