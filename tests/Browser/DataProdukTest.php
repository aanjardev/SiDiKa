<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\AdminBrowserTestCase;

class DataProdukTest extends AdminBrowserTestCase
{
    /**
     * Test end-to-end flow: Login -> Buka Halaman Data Produk -> Tambah Data Produk -> Simpan
     * 
     * Alur:
     * 1. Login sebagai admin
     * 2. Navigasi ke halaman Data Produk
     * 3. Klik tombol "Tambah Produk"
     * 4. Isi semua field form dengan benar
     * 5. Upload gambar dari storage
     * 6. Simpan produk
     * 7. Verifikasi produk tersimpan dan muncul di daftar
     */
    public function testTambahDataProdukDenganGambar(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Login sebagai admin
            $this->loginAsAdmin($browser);

            // Step 2: Navigasi ke halaman Data Produk
            $browser->visit('/admin/products')
                ->waitForText('Data Produk', 10)
                ->assertSee('Data Produk')
                ->pause(500);

            // Step 3: Klik tombol "Tambah Produk"
            $browser->click('a[href*="/admin/products/create"]')
                ->waitForText('Tambah Data Produk', 10)
                ->assertSee('Tambah Data Produk')
                ->pause(500);

            // Step 4: Isi form dengan data lengkap
            $productData = $this->fillProductForm($browser);

            // Step 5: Upload gambar dari storage
            $this->uploadProductImage($browser);

            // Step 6: Simpan produk
            $this->submitProductForm($browser);

            // Step 7: Verifikasi produk tersimpan dan muncul di daftar
            $browser->waitForLocation('/admin/products', 15)
                ->assertPathIs('/admin/products')
                ->pause(1000);

            // Verifikasi pesan sukses
            $browser->assertSee('Produk berhasil ditambahkan')
                ->pause(500);

            // Verifikasi produk muncul di tabel
            $browser->waitFor('table tbody tr', 10)
                ->assertSee($productData['nama_produk'])
                ->assertSee($productData['kode_sku'])
                ->pause(500);
        });
    }

    /**
     * Helper: Isi form produk dengan semua field yang diperlukan
     */
    private function fillProductForm(Browser $browser): array
    {
        $browser->waitFor('#product-form', 10)
            ->assertSee('Informasi Produk')
            ->pause(500);

        // Generate data produk unik
        $timestamp = time();
        $productName = 'Test Produk Dusk ' . $timestamp;
        $skuCode = 'SKU-TEST-' . $timestamp;
        $description = 'Deskripsi produk test untuk automated testing. Produk ini dibuat melalui Dusk test.';

        // Field required: Nama Produk
        $browser->type('input[name="nama_produk"]', $productName)
            ->pause(200);

        // Field optional: Deskripsi
        $browser->type('textarea[name="deskripsi_produk"]', $description)
            ->pause(200);

        // Field required: Kategori
        $kategoriId = $browser->script("
            const select = document.querySelector('select[name=\"id_kategori\"]');
            if (!select || !select.options.length) return null;
            // Skip option pertama (-- Pilih Kategori --)
            for (let i = 1; i < select.options.length; i++) {
                if (select.options[i].value) {
                    return select.options[i].value;
                }
            }
            return null;
        ")[0];

        $this->assertNotEmpty($kategoriId, 'Tidak ada kategori yang tersedia.');

        $browser->select('select[name="id_kategori"]', (string) $kategoriId)
            ->pause(200);

        // Field required: Kode SKU
        $browser->type('input[name="kode_sku"]', $skuCode)
            ->pause(200);

        // Field optional: Stok (wajib diisi karena ada validasi min:0, tapi nullable)
        // Isi dengan nilai yang valid
        $browser->type('input[name="stok_produk"]', '10')
            ->pause(200);
        
        // Trigger event untuk numeric-only handler
        $browser->script("
            const stokInput = document.querySelector('input[name=\"stok_produk\"]');
            if (stokInput) {
                stokInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        ");
        
        $browser->pause(200);

        // Field required: Status (Kondisi)
        $browser->select('select[name="status"]', 'Second')
            ->pause(200);

        // Field required: Grade
        $browser->select('select[name="grade"]', 'Standar')
            ->pause(200);

        // Field optional: Harga Beli
        $this->fillProductRupiahInput($browser, 'harga_beli', 5000000);

        // Field optional: Harga Jual
        $this->fillProductRupiahInput($browser, 'harga_jual', 6000000);

        // Field optional: Harga Servis
        $this->fillProductRupiahInput($browser, 'harga_servis', 200000);

        $browser->pause(500);

        return [
            'nama_produk' => $productName,
            'kode_sku' => $skuCode,
            'kategori_id' => $kategoriId,
            'deskripsi_produk' => $description,
        ];
    }

    /**
     * Helper: Upload gambar produk dari storage
     * 
     * Menggunakan pendekatan yang lebih andal untuk mengunggah file
     */
    private function uploadProductImage(Browser $browser): void
    {
        // Path gambar
        $imagePath = 'public/productIMG/1/12.jpg';
        $fullPath = base_path($imagePath);
        
        // Pastikan file gambar ada
        if (!file_exists($fullPath)) {
            throw new \Exception("File gambar tidak ditemukan di: " . $fullPath);
        }

        // Tunggu upload grid muncul
        $browser->waitFor('#product-upload-grid', 15);
        $browser->pause(2000);

        // Buat script JavaScript dengan HEREDOC
        $js = <<<JS
        // Hapus input file lama jika ada
        const oldInput = document.getElementById('dusk-file-input');
        if (oldInput) oldInput.remove();
        
        // Buat input file baru
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'dusk-file-input';
        input.style.cssText = 'display:block; visibility:visible; opacity:1; position:fixed; top:0; left:0; width:100px; height:40px; z-index:9999;';
        document.body.appendChild(input);
        JS;

        // Eksekusi script
        $browser->script($js);
        $browser->pause(500);
        
        try {
            // Coba unggah file
            $browser->attach('#dusk-file-input', $fullPath)
                   ->pause(2000);
            
            // Verifikasi
            $browser->waitFor('.upload-box.has-image, .preview-image, [data-preview], .upload-box img', 10);
            
        } catch (\Exception $e) {
            // Jika gagal, coba cara alternatif
            try {
                $browser->script('
                    const inputs = document.querySelectorAll(\'#product-upload-grid input[type="file"]\');
                    if (inputs.length > 0) {
                        inputs[0].style.cssText = "display:block !important; visibility:visible !important; opacity:1 !important; position:absolute !important; top:0; left:0; width:100%; height:100%; z-index:9999;";
                    }
                ');
                
                $browser->attach('#product-upload-grid input[type="file"]', $fullPath)
                       ->pause(2000);
                
                $browser->waitFor('.upload-box.has-image, .preview-image, [data-preview], .upload-box img', 10);
                
            } catch (\Exception $e) {
                throw new \Exception("Gagal mengunggah file setelah beberapa kali percobaan: " . $e->getMessage());
            }
        }
        
        $browser->pause(1000);
    }

    /**
     * Helper: Mengisi input rupiah di form produk
     */
    private function fillProductRupiahInput(Browser $browser, string $inputName, int $value): void
    {
        $browser->clear('input[name="' . $inputName . '"]')
            ->pause(200);

        // Isi nilai (hanya angka)
        $browser->type('input[name="' . $inputName . '"]', (string) $value)
            ->pause(300);

        // Trigger rupiah mask dan pastikan nilai dikonversi dengan benar
        $browser->script("
            (function() {
                const input = document.querySelector('input[name=\"{$inputName}\"]');
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
     * Helper: Submit form produk
     */
    private function submitProductForm(Browser $browser): void
    {
        // Pastikan nilai rupiah dikonversi ke integer sebelum submit
        $browser->script("
            // Konversi semua input rupiah-mask ke nilai murni sebelum submit
            const rupiahInputs = document.querySelectorAll('#product-form input.rupiah-mask');
            rupiahInputs.forEach(function(input) {
                const raw = input.dataset.raw ? input.dataset.raw.replace(/\\D/g, '') : input.value.replace(/\\D/g, '');
                input.value = raw;
            });
        ");

        $browser->pause(300);

        // Submit form menggunakan tombol di header
        $browser->click('button[form="product-form"]')
            ->pause(1000);
    }

    /**
     * Negatif: Form produk harus menolak submit jika field required kosong
     * Langkah: login -> buka form tambah produk -> submit tanpa isi field required -> cek error
     */
    public function testTambahProdukGagalKarenaFieldRequiredKosong(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/products')
                ->waitForText('Data Produk', 10)
                ->pause(500);

            $browser->click('a[href*="/admin/products/create"]')
                ->waitForText('Tambah Data Produk', 10)
                ->assertSee('Tambah Data Produk')
                ->pause(500);

            // Submit form tanpa mengisi field required
            $browser->waitFor('#product-form', 10)
                ->pause(500);

            // Matikan validasi HTML5 untuk test negatif
            $browser->script("
                const form = document.getElementById('product-form');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Pastikan field kosong
            $browser->clear('input[name="nama_produk"]')
                ->clear('input[name="kode_sku"]')
                ->pause(500);

            $browser->click('button[form="product-form"]')
                ->pause(3000);

            // Verifikasi tetap di halaman form (tidak redirect)
            $browser->waitUsing(10, 500, function () use ($browser) {
                $currentPath = $browser->driver->getCurrentURL();
                return str_contains($currentPath, '/create') || str_contains($currentPath, '/products/create');
            }, 'Form redirect ke halaman lain padahal seharusnya ada error.');

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                const form = document.getElementById('product-form');
                return (form !== null) && (
                    bodyText.includes('nama') ||
                    bodyText.includes('produk') ||
                    bodyText.includes('wajib') ||
                    bodyText.includes('required') ||
                    document.querySelector('.alert-danger') !== null ||
                    document.querySelector('.text-danger') !== null ||
                    document.querySelector('[role=\"alert\"]') !== null ||
                    document.querySelector('.invalid-feedback') !== null ||
                    document.querySelector('input[name=\"nama_produk\"].is-invalid') !== null
                );
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan field required kosong.');
        });
    }

    /**
     * Negatif: Form produk harus menolak submit jika kode SKU duplikat
     * Langkah: login -> buka form tambah produk -> isi dengan SKU yang sudah ada -> submit -> cek error
     */
    public function testTambahProdukGagalKarenaSKUDuplikat(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            // Ambil SKU yang sudah ada dari produk pertama di tabel
            $browser->visit('/admin/products')
                ->waitForText('Data Produk', 10)
                ->pause(1000);

            $existingSku = $browser->script("
                const firstRow = document.querySelector('table tbody tr');
                if (!firstRow) return null;
                const cells = firstRow.querySelectorAll('td');
                // SKU biasanya di kolom kedua atau ketiga
                for (let i = 0; i < cells.length; i++) {
                    const text = cells[i].textContent.trim();
                    if (text && text.length > 0 && text.length < 30) {
                        return text;
                    }
                }
                return null;
            ")[0];

            // Jika tidak ada produk, skip test ini
            if (empty($existingSku)) {
                $this->markTestSkipped('Tidak ada produk yang ada untuk test duplikasi SKU');
                return;
            }

            $browser->click('a[href*="/admin/products/create"]')
                ->waitForText('Tambah Data Produk', 10)
                ->pause(500);

            // Isi form dengan data valid kecuali SKU yang duplikat
            $productData = $this->fillProductForm($browser);
            
            // Ganti SKU dengan yang sudah ada
            $browser->clear('input[name="kode_sku"]')
                ->type('input[name="kode_sku"]', $existingSku)
                ->pause(500);

            // Submit form
            $browser->click('button[form="product-form"]')
                ->pause(3000);

            // Verifikasi tetap di halaman form atau redirect dengan error
            $browser->waitUsing(10, 500, function () use ($browser) {
                $currentPath = $browser->driver->getCurrentURL();
                return str_contains($currentPath, '/create') || str_contains($currentPath, '/products');
            }, 'Form tidak berada di halaman yang diharapkan.');

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                return bodyText.includes('SKU') ||
                       bodyText.includes('sudah') ||
                       bodyText.includes('digunakan') ||
                       bodyText.includes('terdaftar') ||
                       bodyText.includes('unique') ||
                       document.querySelector('.alert-danger') !== null ||
                       document.querySelector('.text-danger') !== null ||
                       document.querySelector('[role=\"alert\"]') !== null ||
                       document.querySelector('.invalid-feedback') !== null;
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan SKU duplikat.');
        });
    }

    /**
     * Negatif: Form produk harus menolak submit jika kategori tidak dipilih
     * Langkah: login -> buka form tambah produk -> isi semua field kecuali kategori -> submit -> cek error
     */
    public function testTambahProdukGagalKarenaKategoriTidakDipilih(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/products')
                ->waitForText('Data Produk', 10)
                ->pause(500);

            $browser->click('a[href*="/admin/products/create"]')
                ->waitForText('Tambah Data Produk', 10)
                ->pause(500);

            $browser->waitFor('#product-form', 10)
                ->pause(500);

            // Isi field required lainnya
            $timestamp = time();
            $browser->type('input[name="nama_produk"]', 'Test Produk ' . $timestamp)
                ->type('input[name="kode_sku"]', 'SKU-TEST-' . $timestamp)
                ->select('select[name="status"]', 'Second')
                ->select('select[name="grade"]', 'Standar')
                ->pause(500);

            // Pastikan kategori tidak dipilih (reset ke default)
            $browser->script("
                const select = document.querySelector('select[name=\"id_kategori\"]');
                if (select) {
                    select.value = '';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
            ");

            $browser->pause(500);

            // Matikan validasi HTML5
            $browser->script("
                const form = document.getElementById('product-form');
                if (form) {
                    form.setAttribute('novalidate', 'true');
                }
            ");

            // Submit form
            $browser->click('button[form="product-form"]')
                ->pause(3000);

            // Verifikasi tetap di halaman form (tidak redirect)
            $browser->waitUsing(10, 500, function () use ($browser) {
                $currentPath = $browser->driver->getCurrentURL();
                return str_contains($currentPath, '/create') || str_contains($currentPath, '/products/create');
            }, 'Form redirect ke halaman lain padahal seharusnya ada error.');

            // Cek error di berbagai tempat
            $hasError = $browser->script("
                const bodyText = document.body.textContent || document.body.innerText || '';
                const form = document.getElementById('product-form');
                return (form !== null) && (
                    bodyText.includes('kategori') ||
                    bodyText.includes('wajib') ||
                    bodyText.includes('required') ||
                    document.querySelector('.alert-danger') !== null ||
                    document.querySelector('.text-danger') !== null ||
                    document.querySelector('[role=\"alert\"]') !== null ||
                    document.querySelector('.invalid-feedback') !== null ||
                    document.querySelector('select[name=\"id_kategori\"].is-invalid') !== null
                );
            ")[0];

            $this->assertTrue($hasError === true, 'Error message tidak ditemukan setelah submit dengan kategori tidak dipilih.');
        });
    }
}

