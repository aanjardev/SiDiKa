<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\AdminBrowserTestCase;

class QCTest extends AdminBrowserTestCase
{
    /**
     * Test end-to-end flow: Login -> Create Purchase Transaction (Deal) -> Process QC
     * 
     * Alur:
     * 1. Login sebagai admin
     * 2. Buat transaksi pembelian dengan item
     * 3. Set status pembelian menjadi 'deal'
     * 4. Navigasi ke halaman QC
     * 5. Proses item QC
     * 6. Simpan sebagai draft
     * 7. Lanjutkan proses QC hingga lolos
     */
    public function testQCFlowDariPembelianHinggaLolosQC(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Login sebagai admin
            $this->loginAsAdmin($browser);

            // Step 2: Buat transaksi pembelian
            $purchaseData = $this->createPurchaseTransaction($browser);

            // Step 3: Set status pembelian menjadi 'deal'
            $this->finalizePurchaseAsDeal($browser, $purchaseData['pembelian_id']);

            // Step 4: Navigasi ke halaman QC dan tunggu item muncul
            $browser->visit('/admin/quality-control')
                ->waitForText('Quality Control (QC)', 10)
                ->assertSee('Quality Control (QC)')
                ->pause(1000);

            // Step 5: Tunggu item muncul di tabel QC (item dari pembelian deal otomatis masuk QC)
            $itemName = $purchaseData['item_name'];
            
            // Tunggu item muncul di tabel dengan retry
            $itemFoundInQC = false;
            for ($i = 0; $i < 30; $i++) {
                $itemFoundInQC = $browser->script("
                    const table = document.querySelector('table tbody');
                    if (!table) return false;
                    const text = table.textContent || table.innerText || '';
                    return text.includes('{$itemName}');
                ")[0];
                
                if ($itemFoundInQC) {
                    break;
                }
                
                // Jika sudah beberapa kali coba dan belum ketemu, refresh halaman
                if ($i === 10) {
                    $browser->refresh()
                        ->waitForText('Quality Control (QC)', 10)
                        ->pause(1000);
                }
                
                $browser->pause(500);
            }
            
            $this->assertTrue($itemFoundInQC, 'Item tidak ditemukan di halaman QC setelah finalisasi pembelian. Nama item: ' . $itemName);
            
            $browser->waitFor('table tbody tr', 10)
                ->assertSee($itemName)
                ->pause(500);

            // Klik tombol "Proses" untuk item yang sesuai
            $this->clickProcessButtonForItem($browser, $itemName);

            // Step 6: Isi form QC dan simpan sebagai draft
            $this->fillQCFormAsDraft($browser, $purchaseData);

            // Step 7: Kembali ke halaman QC dan proses lagi untuk lolos QC
            $browser->visit('/admin/quality-control')
                ->waitForText('Quality Control (QC)', 10);

            $this->clickProcessButtonForItem($browser, $itemName);

            // Step 8: Lengkapi form QC dan set status menjadi 'lolos_qc'
            $this->completeQCFormAsLolos($browser, $purchaseData);

            // Step 9: Verifikasi item sudah tidak ada di daftar QC (karena sudah lolos)
            $browser->visit('/admin/quality-control')
                ->waitForText('Quality Control (QC)', 10)
                ->pause(1000);

            // Item yang sudah lolos_qc tidak akan muncul di daftar QC
            // (karena query hanya menampilkan status_qc = 'menunggu_qc')
            $browser->assertDontSee($itemName);
        });
    }


    /**
     * Helper: Buat transaksi pembelian dengan item
     */
    private function createPurchaseTransaction(Browser $browser): array
    {
        // Navigasi ke halaman create pembelian
        $browser->visit('/admin/purchases/create')
            ->waitFor('#formPembelian', 10)
            ->assertSee('Informasi Transaksi')
            ->pause(1000);

        // Buat customer baru untuk test
        $customerName = 'Test Customer QC ' . time();
        $customerPhone = '081' . $this->faker->numerify('#########');

        // Buka modal customer baru
        $browser->click('a[data-bs-target="#modalTambahCustomer"]')
            ->waitFor('#modalTambahCustomer.show', 5)
            ->pause(500);

        // Isi form customer baru
        $browser->within('#modalTambahCustomer', function (Browser $modal) use ($customerName, $customerPhone) {
            $modal->type('#customer_nama_modal', $customerName)
                ->type('#customer_no_telp_modal', $customerPhone)
                ->select('#customer_jenis_kelamin_modal', 'L')
                ->pause(300)
                ->click('#btnSimpanCustomer');
        });

        // Tunggu customer tersimpan dan modal tertutup
        $browser->waitUsing(15, 500, function () use ($browser) {
            $customerId = $browser->value('#customer_id');
            return !empty($customerId);
        }, 'Customer baru tidak tersimpan.');

        // Pastikan modal tertutup
        $browser->waitUntilMissing('#modalTambahCustomer.show', 5)
            ->pause(500);

        // Pilih cabang (gunakan cabang pertama yang aktif)
        $branchId = $browser->script("
            const select = document.getElementById('perusahaan_cabang_id');
            if (!select || !select.options.length) return null;
            return select.options[0].value;
        ")[0];

        $this->assertNotEmpty($branchId, 'Tidak ada cabang aktif yang bisa dipilih.');

        $browser->select('#perusahaan_cabang_id', (string) $branchId)
            ->pause(500);

        // Buka modal tambah item - gunakan ID yang benar
        $browser->click('#btnBukaModalItem')
            ->waitFor('#modalTambahItem.show', 10)
            ->pause(500);

        // Generate data item
        $itemName = 'Canon ED34 Test ' . time();
        
        // Ambil kategori ID dari dropdown
        $kategoriId = $browser->script("
            const select = document.querySelector('#modalTambahItem select[name=\"kategori_id\"], #modalTambahItem #item_kategori_id');
            if (!select || !select.options.length) return null;
            // Skip option pertama (Pilih Kategori)
            for (let i = 1; i < select.options.length; i++) {
                if (select.options[i].value) {
                    return select.options[i].value;
                }
            }
            return null;
        ")[0];

        $this->assertNotEmpty($kategoriId, 'Tidak ada kategori yang tersedia.');

        // Isi form item - gunakan ID yang benar dari form
        $browser->within('#modalTambahItem', function (Browser $modal) use ($itemName, $kategoriId) {
            // Field nama item (required) - gunakan ID yang benar
            $modal->type('#item_nama_item', $itemName)
                ->pause(300);
            
            // Field kategori (required) - gunakan ID yang benar
            $modal->select('#item_kategori_id', (string) $kategoriId)
                ->pause(300);
            
            // Field serial number (opsional)
            $modal->type('#item_serial_number', 'SN-' . time())
                ->pause(200);
            
            // Field serial lens (opsional)
            $modal->type('#item_serial_lens', 'LENS-' . time())
                ->pause(300);
        });

        // Klik tombol simpan item - tunggu tombol tersedia
        $browser->waitFor('#btnSimpanItem', 5)
            ->pause(300);

        // Klik tombol simpan item
        $browser->click('#btnSimpanItem')
            ->pause(1000);

        // Tunggu tombol loading selesai (menandakan AJAX request selesai)
        // Atau tunggu modal tertutup (menandakan sukses)
        $browser->waitUsing(20, 500, function () use ($browser) {
            // Cek apakah modal sudah tertutup (berarti sukses)
            $modalClosed = $browser->script("
                const modal = document.getElementById('modalTambahItem');
                return !modal || !modal.classList.contains('show');
            ")[0];
            
            // Cek apakah tombol tidak lagi loading
            $btnReady = $browser->script("
                const btn = document.getElementById('btnSimpanItem');
                if (!btn) return true;
                return !btn.disabled && !btn.innerHTML.includes('spinner');
            ")[0];
            
            return $modalClosed === true && $btnReady === true;
        }, 'Modal tidak tertutup atau tombol masih loading setelah menyimpan item.');

        // Pastikan tidak ada alert error
        $browser->pause(500);

        // Tunggu item muncul di tabel - cek dengan script langsung
        $itemFound = false;
        $maxAttempts = 40; // 20 detik total (40 * 500ms)
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $result = $browser->script("
                const wrapper = document.getElementById('item-list-wrapper');
                if (!wrapper) return { found: false, rows: 0, text: '' };
                const rows = wrapper.querySelectorAll('tr');
                const text = wrapper.textContent || wrapper.innerText || '';
                return {
                    found: text.includes('{$itemName}'),
                    rows: rows.length,
                    text: text.substring(0, 200) // Ambil 200 karakter pertama untuk debug
                };
            ")[0];
            
            if ($result['found']) {
                $itemFound = true;
                break;
            }
            
            // Jika sudah ada rows tapi item tidak ditemukan, mungkin nama item berbeda
            if ($result['rows'] > 0 && $i > 10) {
                // Debug: tampilkan text yang ada
                $this->fail('Item tidak ditemukan di tabel. Rows: ' . $result['rows'] . ', Text: ' . $result['text']);
            }
            
            $browser->pause(500);
        }

        $this->assertTrue($itemFound, 'Item tidak ditemukan di tabel keranjang setelah ' . ($maxAttempts * 0.5) . ' detik. Nama item: ' . $itemName);
        
        // Verifikasi dengan assertSee juga
        $browser->assertSee($itemName)
            ->pause(500);

        // Ambil pembelian_id dari hidden input
        $pembelianId = $browser->value('#pembelian_id_hidden');
        
        // Pastikan pembelian_id sudah terisi (item pertama akan membuat pembelian baru)
        $this->assertNotEmpty($pembelianId, 'Pembelian ID tidak terisi setelah menambah item. Pastikan item benar-benar tersimpan.');

        return [
            'pembelian_id' => $pembelianId,
            'item_name' => $itemName,
            'kategori_id' => $kategoriId,
            'customer_id' => $browser->value('#customer_id'),
            'branch_id' => $branchId,
        ];
    }

    /**
     * Helper: Finalisasi pembelian dengan status 'deal'
     */
    private function finalizePurchaseAsDeal(Browser $browser, string $pembelianId): void
    {
        // Pastikan masih di halaman form pembelian dan ada item di keranjang
        $browser->assertPresent('#formPembelian')
            ->assertPresent('#item-list-wrapper')
            ->pause(500);

        // Pastikan ada item di tabel (bukan keranjang kosong)
        $hasItems = $browser->script("
            const itemList = document.getElementById('item-list-wrapper');
            if (!itemList) return false;
            const rows = itemList.querySelectorAll('tr');
            return rows.length > 0;
        ")[0];

        $this->assertTrue($hasItems, 'Tidak ada item di keranjang. Pastikan item sudah ditambahkan.');

        // Isi harga tawaran dan harga deal
        $hargaTawaranCustomer = 5000000;
        $hargaTawaranToko = 4500000;
        $hargaDeal = 4750000;

        // Helper function untuk mengisi rupiah input dengan benar
        $this->fillRupiahInput($browser, 'display_harga_tawaran_customer', 'harga_tawaran_customer', $hargaTawaranCustomer);
        $this->fillRupiahInput($browser, 'display_harga_tawaran_toko', 'harga_tawaran_toko', $hargaTawaranToko);
        $this->fillRupiahInput($browser, 'display_harga_deal', 'harga_deal', $hargaDeal);

        // Verifikasi hidden input harga_deal terisi dengan benar (ini penting untuk enable tombol DEAL)
        $browser->pause(500);
        
        $hargaDealValue = $browser->script("
            const hidden = document.getElementById('harga_deal');
            return hidden ? hidden.value : null;
        ")[0];
        
        $this->assertEquals('4750000', $hargaDealValue, 'Hidden input harga_deal tidak terisi dengan benar.');

        // Pastikan tombol DEAL tersedia dan tidak disabled
        // Tombol DEAL akan enabled jika: ada items DAN harga_deal > 0
        $browser->waitFor('#btnDeal', 5)
            ->pause(500);

        // Verifikasi tombol DEAL tidak disabled
        $btnDealDisabled = $browser->script("
            const btnDeal = document.getElementById('btnDeal');
            return btnDeal ? btnDeal.disabled : true;
        ")[0];

        $this->assertFalse($btnDealDisabled, 'Tombol DEAL masih disabled. Pastikan ada item dan harga_deal terisi.');

        // Klik tombol DEAL
        $browser->click('#btnDeal')
            ->pause(1000);

        // Tunggu redirect ke halaman index pembelian
        $browser->waitForLocation('/admin/purchases', 15)
            ->assertPathIs('/admin/purchases')
            ->pause(2000); // Beri waktu untuk database commit dan item masuk ke QC
    }

    /**
     * Helper: Mengisi input rupiah mask di form QC dengan benar
     */
    private function fillQCRupiahInput(Browser $browser, string $inputId, int $value): void
    {
        // Input di form QC langsung menggunakan name sebagai id (tidak ada display/hidden terpisah)
        $browser->clear('#' . $inputId)
            ->pause(200);
        
        // Isi nilai (hanya angka)
        $browser->type('#' . $inputId, (string) $value)
            ->pause(300);
        
        // Trigger rupiah mask dan pastikan nilai dikonversi dengan benar
        $browser->script("
            (function() {
                const input = document.getElementById('{$inputId}');
                if (!input) return;
                
                // Simpan nilai murni ke dataset.raw (untuk submit)
                const cleanValue = String({$value}).replace(/\\D/g, '');
                input.dataset.raw = cleanValue;
                
                // Format display value (dengan titik sebagai pemisah ribuan)
                const formatted = cleanValue.replace(/\\B(?=(\\d{3})+(?!\\d))/g, '.');
                input.value = formatted;
                
                // Trigger events untuk memastikan handler terpanggil
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('blur', { bubbles: true }));
            })();
        ");
        
        $browser->pause(300);
    }

    /**
     * Helper: Mengisi input rupiah mask dengan benar (untuk form pembelian)
     */
    private function fillRupiahInput(Browser $browser, string $displayId, string $hiddenId, int $value): void
    {
        // Clear dulu
        $browser->clear('#' . $displayId)
            ->pause(200);
        
        // Isi nilai (hanya angka)
        $browser->type('#' . $displayId, (string) $value)
            ->pause(300);
        
        // Trigger maskRupiah function dan update hidden input
        $browser->script("
            (function() {
                const display = document.getElementById('{$displayId}');
                const hidden = document.getElementById('{$hiddenId}');
                
                if (!display || !hidden) return;
                
                // Simpan nilai murni ke dataset.raw
                const cleanValue = String({$value}).replace(/\\D/g, '');
                display.dataset.raw = cleanValue;
                
                // Format display value menggunakan Intl.NumberFormat
                const formatted = new Intl.NumberFormat('id-ID').format(cleanValue);
                display.value = formatted;
                
                // Set hidden value dengan nilai murni
                hidden.value = cleanValue;
                
                // Trigger events untuk memastikan handler terpanggil (termasuk dealButtons.sync)
                display.dispatchEvent(new Event('input', { bubbles: true }));
                display.dispatchEvent(new Event('keyup', { bubbles: true }));
                display.dispatchEvent(new Event('change', { bubbles: true }));
            })();
        ");
        
        $browser->pause(300);
    }

    /**
     * Helper: Klik tombol "Proses" untuk item tertentu
     */
    private function clickProcessButtonForItem(Browser $browser, string $itemName): void
    {
        // Tunggu tabel muncul
        $browser->waitFor('table tbody tr', 10)
            ->pause(500);

        // Cari baris tabel yang berisi item name dan klik tombol "Proses"
        $itemUrl = $browser->script("
            const rows = Array.from(document.querySelectorAll('table tbody tr'));
            const targetRow = rows.find(row => {
                const text = row.textContent || '';
                return text.includes('{$itemName}');
            });
            
            if (targetRow) {
                // Cari tombol Proses - link dengan href ke quality-control/edit
                const processBtn = targetRow.querySelector('a[href*=\"/quality-control/\"]:not([href*=\"archived\"])');
                
                if (processBtn) {
                    return processBtn.getAttribute('href');
                }
            }
            return null;
        ")[0];

        $this->assertNotEmpty($itemUrl, "Tombol Proses tidak ditemukan untuk item: {$itemName}");

        // Navigasi langsung ke URL edit QC
        $browser->visit($itemUrl)
            ->waitFor('#qcForm', 15)
            ->assertSee('Proses QC')
            ->pause(500);
    }

    /**
     * Helper: Isi form QC dan simpan sebagai draft
     */
    private function fillQCFormAsDraft(Browser $browser, array $purchaseData): void
    {
        $browser->waitFor('#qcForm', 10)
            ->assertSee('Detail Item & Fisik')
            ->pause(500);

        // Pastikan nama item sudah terisi
        $browser->assertValue('input[name="nama_item"]', $purchaseData['item_name']);

        // Isi beberapa field QC (opsional untuk draft)
        // Pastikan status_qc tetap 'menunggu_qc' untuk draft
        $browser->type('input[name="kode_sku"]', 'SKU-TEST-' . time())
            ->pause(200);

        // Isi harga dengan benar - menggunakan helper untuk rupiah mask
        $this->fillQCRupiahInput($browser, 'harga_jual', 5000000);
        $this->fillQCRupiahInput($browser, 'harga_beli', 4750000);
        $this->fillQCRupiahInput($browser, 'harga_servis', 200000);

        $browser->select('select[name="grade"]', 'Standar')
            ->select('select[name="status"]', 'Second')
            ->type('textarea[name="deskripsi_produk"]', 'Deskripsi produk test untuk QC draft')
            ->pause(500);

        // Pastikan status_qc tetap 'menunggu_qc' (default untuk draft)
        $browser->script("
            const statusSelect = document.querySelector('select[name=\"status_qc\"]');
            if (statusSelect && statusSelect.value !== 'menunggu_qc') {
                statusSelect.value = 'menunggu_qc';
                statusSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        ");

        $browser->pause(500);

        // Pastikan nilai rupiah dikonversi ke integer sebelum submit
        $browser->script("
            // Konversi semua input rupiah-mask ke nilai murni sebelum submit
            const rupiahInputs = document.querySelectorAll('#qcForm input.rupiah-mask');
            rupiahInputs.forEach(function(input) {
                const raw = input.dataset.raw ? input.dataset.raw.replace(/\\D/g, '') : input.value.replace(/\\D/g, '');
                input.value = raw;
            });
        ");
        
        $browser->pause(200);
        
        // Submit form (smart button akan handle action berdasarkan status)
        $browser->script("
            const form = document.getElementById('qcForm');
            if (form) {
                // Set action sebagai draft
                let actionInput = form.querySelector('input[name=\"action\"]');
                if (!actionInput) {
                    actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    form.appendChild(actionInput);
                }
                actionInput.value = 'draft';
                form.submit();
            }
        ");

        $browser->pause(2000);

        // Tunggu redirect kembali ke halaman QC index
        $browser->waitForLocation('/admin/quality-control', 15)
            ->assertPathIs('/admin/quality-control')
            ->pause(1000);
    }

    /**
     * Helper: Lengkapi form QC dan set status menjadi 'lolos_qc'
     */
    private function completeQCFormAsLolos(Browser $browser, array $purchaseData): void
    {
        $browser->waitFor('#qcForm', 10)
            ->assertSee('Detail Item & Fisik')
            ->pause(500);

        // Pastikan semua field required terisi
        $kodeSku = 'SKU-FINAL-' . time();
        $browser->type('input[name="kode_sku"]', $kodeSku)
            ->pause(200);

        // Isi harga dengan benar - menggunakan helper untuk rupiah mask
        $this->fillQCRupiahInput($browser, 'harga_jual', 5500000);
        $this->fillQCRupiahInput($browser, 'harga_beli', 4750000);
        $this->fillQCRupiahInput($browser, 'harga_servis', 250000);

        $browser->type('input[name="qty"]', '1')
            ->select('select[name="grade"]', 'Standar')
            ->select('select[name="status"]', 'Second')
            ->type('textarea[name="deskripsi_produk"]', 'Deskripsi produk final untuk QC lolos. Produk ini telah melalui proses quality control dan dinyatakan layak jual.')
            ->pause(500);

        // Set status QC menjadi 'lolos_qc'
        $browser->script("
            const statusSelect = document.querySelector('select[name=\"status_qc\"]');
            if (statusSelect) {
                statusSelect.value = 'lolos_qc';
                statusSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        ");

        $browser->pause(1000);

        // Pastikan nilai rupiah dikonversi ke integer sebelum submit
        $browser->script("
            // Konversi semua input rupiah-mask ke nilai murni sebelum submit
            const rupiahInputs = document.querySelectorAll('#qcForm input.rupiah-mask');
            rupiahInputs.forEach(function(input) {
                const raw = input.dataset.raw ? input.dataset.raw.replace(/\\D/g, '') : input.value.replace(/\\D/g, '');
                input.value = raw;
            });
        ");
        
        $browser->pause(200);
        
        // Submit form dengan status lolos_qc
        $browser->script("
            const form = document.getElementById('qcForm');
            if (form) {
                // Set action sebagai save untuk lolos_qc
                let actionInput = form.querySelector('input[name=\"action\"]');
                if (!actionInput) {
                    actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    form.appendChild(actionInput);
                }
                actionInput.value = 'save';
                form.submit();
            }
        ");

        $browser->pause(3000);

        // Tunggu redirect ke halaman QC index dengan pesan sukses
        $browser->waitForLocation('/admin/quality-control', 15)
            ->assertPathIs('/admin/quality-control')
            ->pause(1000);

        // Verifikasi pesan sukses muncul
        $browser->assertSee('lolos QC');
    }

    /**
     * Negatif: Form QC harus menolak submit jika field required kosong saat status 'lolos_qc'
     * Langkah: login -> buat pembelian deal -> proses QC -> set status lolos_qc tanpa isi field required -> submit -> cek error
     */
    public function testQCGagalKarenaFieldRequiredKosongSaatLolosQC(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Login sebagai admin
            $this->loginAsAdmin($browser);

            // Step 2: Buat transaksi pembelian
            $purchaseData = $this->createPurchaseTransaction($browser);

            // Step 3: Set status pembelian menjadi 'deal'
            $this->finalizePurchaseAsDeal($browser, $purchaseData['pembelian_id']);

            // Step 4: Navigasi ke halaman QC
            $browser->visit('/admin/quality-control')
                ->waitForText('Quality Control (QC)', 10)
                ->pause(1000);

            $itemName = $purchaseData['item_name'];
            $browser->waitFor('table tbody tr', 10)
                ->assertSee($itemName)
                ->pause(500);

            // Klik tombol "Proses"
            $this->clickProcessButtonForItem($browser, $itemName);

            // Step 5: Set status menjadi 'lolos_qc' tanpa mengisi field required
            $browser->waitFor('#qcForm', 10)
                ->pause(500);

            // Kosongkan field required
            $browser->script("
                const form = document.getElementById('qcForm');
                if (form) {
                    const kodeSku = form.querySelector('[name=\"kode_sku\"]');
                    const hargaJual = form.querySelector('[name=\"harga_jual\"]');
                    const deskripsi = form.querySelector('[name=\"deskripsi_produk\"]');
                    const qty = form.querySelector('[name=\"qty\"]');
                    
                    if (kodeSku) kodeSku.value = '';
                    if (hargaJual) hargaJual.value = '';
                    if (deskripsi) deskripsi.value = '';
                    if (qty) qty.value = '';
                }
            ");

            // Set status menjadi 'lolos_qc'
            $browser->script("
                const statusSelect = document.querySelector('select[name=\"status_qc\"]');
                if (statusSelect) {
                    statusSelect.value = 'lolos_qc';
                    statusSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");

            $browser->pause(1000);

            // Submit form
            $browser->script("
                const form = document.getElementById('qcForm');
                if (form) {
                    let actionInput = form.querySelector('input[name=\"action\"]');
                    if (!actionInput) {
                        actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        form.appendChild(actionInput);
                    }
                    actionInput.value = 'save';
                    form.submit();
                }
            ");

            $browser->pause(2000);

            // Verifikasi tetap di halaman form dengan error
            $browser->assertPresent('#qcForm')
                ->assertSee('Kode SKU wajib diisi')
                ->pause(500);
        });
    }

    /**
     * Negatif: Form QC harus menolak submit jika harga_jual tidak valid (bukan integer)
     * Langkah: login -> buat pembelian deal -> proses QC -> isi harga_jual dengan string -> submit -> cek error
     */
    public function testQCGagalKarenaHargaJualTidakValid(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);
            $purchaseData = $this->createPurchaseTransaction($browser);
            $this->finalizePurchaseAsDeal($browser, $purchaseData['pembelian_id']);

            $browser->visit('/admin/quality-control')
                ->waitForText('Quality Control (QC)', 10)
                ->pause(1000);

            $itemName = $purchaseData['item_name'];
            $browser->waitFor('table tbody tr', 10)
                ->assertSee($itemName)
                ->pause(500);

            $this->clickProcessButtonForItem($browser, $itemName);

            $browser->waitFor('#qcForm', 10)
                ->pause(500);

            // Isi field dengan data valid kecuali harga_jual
            $browser->type('input[name="kode_sku"]', 'SKU-INVALID-' . time())
                ->type('input[name="harga_jual"]', 'invalid-price')
                ->type('textarea[name="deskripsi_produk"]', 'Deskripsi test')
                ->type('input[name="qty"]', '1')
                ->pause(500);

            // Set status menjadi 'lolos_qc'
            $browser->script("
                const statusSelect = document.querySelector('select[name=\"status_qc\"]');
                if (statusSelect) {
                    statusSelect.value = 'lolos_qc';
                    statusSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");

            $browser->pause(1000);

            // Submit form
            $browser->script("
                const form = document.getElementById('qcForm');
                if (form) {
                    let actionInput = form.querySelector('input[name=\"action\"]');
                    if (!actionInput) {
                        actionInput = document.createElement('input');
                        actionInput.type = 'hidden';
                        actionInput.name = 'action';
                        form.appendChild(actionInput);
                    }
                    actionInput.value = 'save';
                    form.submit();
                }
            ");

            $browser->pause(2000);

            // Verifikasi error muncul
            $browser->assertPresent('#qcForm')
                ->assertSee('harga jual')
                ->pause(500);
        });
    }
}

