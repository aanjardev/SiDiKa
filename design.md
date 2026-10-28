# SiDiKa — Konteks Proyek dan Panduan Desain

Dokumen ini mencatat implementasi yang ada di repository sebagai acuan pembuatan halaman baru. Ini bukan rancangan ulang atau spesifikasi fitur baru.

Tanggal analisis: **28 Oktober 2026**. Bahasa antarmuka utama: **Indonesia**.

Workspace proyek yang digunakan: `D:\collages\semester3\Sistem Informasi\SiDiKa`. Dokumentasi dan halaman Dinoyo Kamera Way dipindahkan ke repo ini setelah koreksi lokasi workspace.

## 1. Ringkasan proyek

**SiDiKa (Sistem Informasi Dinoyo Kamera)** menggabungkan katalog publik Dinoyo Kamera dan aplikasi operasional internal toko kamera. Sistem mendukung produk baru/bekas, kategori, foto, pelanggan, cabang, karyawan, pembelian barang dari pelanggan, pemeriksaan QC, penjualan, laporan, dan prediksi stok.

Ada tiga konteks pengalaman pengguna:

1. **Publik:** menemukan produk, menilai kondisi dan harga, mengenal toko, mencari cabang, lalu menghubungi WhatsApp atau kanal toko.
2. **Staff:** mengelola data dan proses operasional melalui admin.
3. **Manager:** menggunakan admin dengan tambahan pengelolaan karyawan dan hak akses akun.

Katalog publik belum memiliki alur keranjang, pembayaran, atau akun pelanggan. Keranjang dan checkout yang ada merupakan alat transaksi **di admin**.

### Batas analisis

- Analisis dilakukan pada route, controller, model, service, template Blade, CSS, JavaScript terkait, dan konfigurasi aset. Setelah halaman Way, editor, dan partial aperture ditambahkan, terdapat 85 template Blade pada repo tujuan.
- Analisis awal dilakukan dari kode. Pada workspace tujuan, `vendor/autoload.php`, `node_modules`, dan `.env` sudah tersedia. Setelah pemindahan, test halaman Dinoyo Kamera Way lulus (3 test, 20 assertion), route terdaftar, serta sintaks PHP/JavaScript lolos pemeriksaan. Verifikasi visual desktop/mobile sebelumnya menggunakan data sementara; pemindahan ini tidak mengubah database aplikasi.
- Ukuran, warna, komposisi, dan perilaku di bawah berasal dari kode. Hasil visual akhir masih perlu dicek pada runtime, terutama karena CSS berlapis.
- `README.md` masih berisi template Laravel; konteks bisnis lebih jelas ditemukan pada implementasi.

## 2. Arsitektur dan sumber acuan

| Bagian | Implementasi / lokasi |
| --- | --- |
| Backend | Laravel `^10.0`, PHP `^8.1`; deklarasi pada `composer.json` |
| Rendering | Blade di `resources/views`; bukan SPA |
| Route | `routes/web.php`; route admin memakai prefix `/admin` dan nama `admin.*` |
| UI | Bootstrap 5, CSS khusus, Bootstrap Icons dan Font Awesome |
| Build | Vite `^7.2.1`, `laravel-vite-plugin`; entry point pada `vite.config.js` |
| JavaScript | Vanilla JS modular, Bootstrap, Axios; beberapa halaman memiliki script inline |
| Grafik | ApexCharts pada admin |
| Animasi publik | AOS; beberapa halaman memuat script tambahan seperti TypeIt |
| Laporan | DomPDF dan Maatwebsite Excel |
| Media | Model/accessor URL, helper upload, job pemrosesan, local/public dan konfigurasi S3/R2 |
| Pemeriksaan yang tersedia | PHPUnit dan Laravel Dusk; keberadaan test bukan bukti seluruh fitur tercakup |

### Struktur yang perlu dibaca sebelum membuat halaman

```text
routes/web.php                       URL, nama route, middleware
app/Http/Controllers/                Data, validasi, respons halaman/AJAX
app/Http/Requests/                   Validasi produk dan foto
app/Models/                         Relasi dan URL media
app/Services/                       Produk, dashboard, forecasting, cache
resources/views/layouts/            Tiga shell utama
resources/views/partials/           Header, footer, alert, WhatsApp
resources/views/admin/partials/     Fragmen tabel dan kartu pelanggan
resources/css/admin/                Shell dan beberapa stylesheet halaman admin
resources/css/legacy/               Stylesheet publik, auth, dan fallback admin
resources/js/                       Modul transaksi, QC, admin, utilitas
public/css/legacy/                  CSS yang ditautkan langsung oleh Blade
public/js/                         Script publik dan beberapa script admin
vite.config.js                      Daftar entry point build
copy-css.js                         Penyalinan CSS resources ke public
```

### Dua jalur pemuatan aset

**Admin** memuat `resources/css/app.css`, `resources/js/app.js`, stylesheet dan script layout melalui `@vite`. Layout juga memuat Bootstrap CDN serta CSS legacy melalui `CssAssetHelper`. Halaman tertentu menambah CSS/JS melalui stack.

**Publik dan auth** terutama memuat stylesheet langsung dari `public/css/legacy/` melalui `CssAssetHelper::css()`, serta script dari `public/js/` atau CDN.

`CssAssetHelper` menambahkan prefix `public/` pada environment `production`. Jangan mengubah aturan path tanpa memeriksa konfigurasi hosting. `npm run copy-css` menyalin seluruh pohon `resources/css` ke `public/css`; perintah ini berpotensi memperbarui lebih dari stylesheet halaman yang dikerjakan.

`resources/admin_theme/` dan `public/admin_theme/` menyimpan Hope UI beserta vendor. Keberadaan aset dan entry build tidak berarti semuanya aktif di layout. Shell admin saat ini dibuat pada `layouts/admin.blade.php`.

## 3. Domain dan alur bisnis

### Entitas utama

| Entitas | Peran dalam halaman |
| --- | --- |
| `Produk` | SKU, kategori, nama, harga jual/modal/servis, stok, kondisi, grade, deskripsi, Instagram, visibilitas, arsip |
| `GambarProduk` | Banyak foto produk; foto utama melalui `gambarUtama`; gunakan accessor `url` |
| `Kategori` | Pengelompokan dan gambar kategori; gunakan `image_url` |
| `Customer` | Pembeli sekaligus pihak yang menjual barang kepada toko; identitas dan riwayat transaksi |
| `Branch` / `JamOperasionalCabang` | Identitas cabang, kontak, lokasi, status aktif, jadwal per hari |
| `Karyawan` / `Employee`, `User` | Data pegawai dan akun login merupakan konsep terpisah yang dihubungkan dalam pengelolaan akses |
| `Pembelian` / `ItemPembelian` | Negosiasi pembelian dan item draft beserta kondisi fisik; item tersimpan di `item_pembelian_draft` |
| `Penjualan` / `DetailPenjualan` | Transaksi keluar dan detail produk/kuantitas |
| `CatalogSettings`, banner, partner, galeri | Identitas dan konten publik yang dikelola melalui admin |
| `ProductStockForecast` | Hasil prediksi stok untuk Smart Stock |

### Alur pembelian dan QC

1. Staff memilih pelanggan dan cabang, memasukkan item serta kondisi barang.
2. Transaksi menyimpan tawaran pelanggan, tawaran toko, harga deal, dan status `draft`, `deal`, atau `tidak_deal`.
3. Item pembelian yang memenuhi seleksi controller masuk daftar pemeriksaan QC. QC manual juga dapat dibuat tanpa transaksi pembelian.
4. QC melengkapi SKU, deskripsi final, harga modal/servis/jual, jumlah, grade, kondisi, dan catatan.
5. Status QC meliputi `menunggu_qc`, `lolos_qc`, `gagal_qc`, dan `diarsipkan`.
6. Item lolos QC membuat atau memperbarui produk berdasarkan SKU. Jalur implementasi dapat menambah stok produk yang sudah ada.
7. Foto dan visibilitas produk dikelola melalui halaman produk/foto; produk yang tersedia dapat ditemukan publik.

Arsip QC dan arsip produk merupakan dua mekanisme berbeda. Jangan menyamakan status item QC dengan flag `is_archived` produk.

### Alur penjualan admin

1. `/admin/sales/create` membuka katalog internal untuk memilih produk dan jumlah.
2. Keranjang disinkronkan ke session `cart_penjualan` melalui `/admin/sales/cart-sync`.
3. Checkout mengirim `POST /admin/sales` dengan `from_checkout=1`; `store()` meneruskan ke `checkout()` dan merender form transaksi.
4. Form transaksi memilih pelanggan, cabang, metode bayar, diskon, biaya tambahan, depresiasi, dan catatan.
5. Penyimpanan akhir memproses detail transaksi dan stok. Detail transaksi dapat dilihat, diedit, dan dicetak.

Jangan menambahkan route checkout berdasarkan nama method saja: method tersebut sudah dipanggil dari `store()`.

### Aturan katalog publik

- Daftar, detail, dan suggestion publik memilih produk `is_visible=true` serta `is_archived=false`.
- Landing page memilih maksimal lima produk terbaru dan lima produk grade `Unggulan`, dengan stok lebih dari nol.
- Katalog dan detail tetap dapat menampilkan produk stok habis; kartu memiliki badge `SOLD OUT`, tombol pemesanan dinonaktifkan.
- Katalog dipaginasi 16 produk. Suggestion memerlukan minimal dua karakter dan mengembalikan maksimal lima hasil.
- Pengarsipan produk mengatur `is_archived=true` dan `is_visible=false`; restore mengatur keduanya kembali menjadi aktif/terlihat.
- WhatsApp dinormalisasi ke nomor Indonesia `62`; pesan produk menyertakan nama dan SKU.

## 4. Layout dan navigasi

### Publik — `layouts/customer.blade.php`

Urutan shell: header, isi halaman, footer, tombol WhatsApp mengambang. Extension memakai `@section('title')`, `@section('content')`, `@push('styles')`, dan `@push('scripts')`.

- Font Montserrat.
- Header tetap di atas, warna `#020405`, logo dinamis, nama toko, dan menu **Beranda / Katalog / Kontak / Tentang Kami**.
- Navbar collapse pada layar kecil; status aktif ditentukan dari path.
- Isi memakai Bootstrap `.container`, grid responsif, section berjarak `py-4` atau `py-5`.
- Footer berisi deskripsi toko, tautan sosial/marketplace, serta cabang aktif dengan jadwal, WhatsApp, alamat, dan Maps.
- Floating WhatsApp hanya muncul jika nomor toko tersedia.
- Header fixed memerlukan ruang atas pada konten. Jangan menyalin margin secara buta antarhalaman.

### Admin — `layouts/admin.blade.php`

Urutan shell: alert dan loader, sidebar + overlay, topbar sticky, konten, footer, modal logout. Title halaman juga dipakai pada topbar.

- Sidebar putih, lebar dasar **260px**, menjadi **240px** pada breakpoint maksimum 1200px.
- Pada lebar maksimum **991px**, sidebar menjadi drawer dengan overlay dan tombol buka/tutup.
- Topbar mengikuti offset sidebar pada desktop. Konten dasar memakai padding horizontal **2.5rem**.
- `@stack('page-actions')` menyediakan aksi kanan atas tanpa menduplikasi heading.
- Footer dan profil/logout sudah tersedia di shell.
- Script layout menandai menu aktif dari path dan membuka grup induknya.

Struktur menu aktual:

```text
Dashboard
Master Data
  Data Produk
  Data Pelanggan
  Data Karyawan [manager]
  Daftar Kategori
  Data Cabang
Operasional
  Penjualan
  Pembelian
  Quality Control
  Foto Produk
  Smart Stock Analysis
Manajemen
  Setting Web Katalog
  Manajemen Akses [manager]
Profil / Logout
```

Group route admin memakai `auth`. Karyawan dan permissions memiliki tambahan `role:manager`. Menu yang disembunyikan bukan pengganti middleware. Modul lain dalam group utama tidak otomatis terbatas pada manager.

### Auth — `layouts/auth.blade.php`

- Font Inter; Bootstrap dan CSS auth terpisah.
- Desktop membagi layar menjadi form kiri dan panel brand kanan, masing-masing `col-lg-6`.
- Panel brand memakai gradien `#3b8aff` sampai `#0048b3` dan dekorasi lingkaran.
- Form dibatasi **420px**, kartu putih radius **16px**, padding dasar **2.5rem**.
- Panel kanan disembunyikan di bawah breakpoint `lg`.
- Halaman verifikasi/reset yang memakai layout auth tetap dapat berada pada route berautentikasi.

## 5. Bahasa visual yang ada

### Warna dan tipografi

| Konteks | Nilai acuan dari kode | Penggunaan |
| --- | --- | --- |
| Publik | `#2F353F` | Heading dan warna utama konten |
| Publik | `#EA4E2D` | Aksen pada landing/katalog/detail dan sejumlah CTA |
| Header publik | `#020405` | Latar navigasi |
| Teks publik | `#333333` | Body copy |
| Admin | `#4e6bff` | Primary/brand shell dan navigasi aktif |
| Netral | `#ffffff`, `#f8f9fa`, `#e9ecef`, `#dee2e6`, `#6c757d` | Surface, border, latar dan teks sekunder |
| Status admin | `#198754`, `#dc3545`, `#ffc107`, `#0dcaf0` | Sukses, gagal/destruktif, peringatan, informasi |
| Auth | `#3b8aff`, `#0048b3` | Primary dan gradien brand |

Nilai tersebut bukan token terpusat yang konsisten di seluruh halaman. Bootstrap, stylesheet legacy, stylesheet halaman, dan style inline dapat menimpa warna. Contoh: halaman kontak mendefinisikan `--secondary-color: #333333`; jangan menganggap semua CTA publik oranye.

Montserrat adalah font utama publik/admin; Inter digunakan auth dan beberapa elemen tambahan. Heading umumnya tebal 600–800. Nama produk ringkas, harga kuat, metadata muted. Format uang memakai `Rp` dan pemisah ribuan Indonesia.

### Komponen dan komposisi

- **Kartu:** putih, border tipis atau shadow lembut, radius umum 8–16px. Panel admin lebih padat daripada section publik.
- **Produk publik:** foto dominan dengan `object-fit: contain`, nama dan harga; badge unggulan dan stok habis. Kartu desktop dapat terangkat dan foto membesar ketika hover.
- **Mobile produk publik:** aturan maksimum 576px mengubah kartu menjadi susunan horizontal; gambar sekitar 40% lebar, teks di samping, efek hover dinonaktifkan.
- **Tabel admin:** `.table-modern`, header uppercase sekitar 0.85rem, isi sekitar 0.9rem, zebra putih/abu, hover biru muda, wrapper horizontal scroll.
- **Aksi tabel:** `.btn-action` sekitar 32×32px, ikon edit biru, hapus merah, warning kuning. Aksi baris perlu dipisahkan dari navigasi klik baris.
- **Filter:** search, select kategori/status/cabang, sort; beberapa otomatis submit atau memperbarui tabel melalui AJAX.
- **Form:** kelompok informasi dalam kartu, label jelas, tanda wajib `*`, pesan field dan ringkasan error; form edit memakai nilai lama/data existing.
- **Detail:** metadata di kartu, ringkasan angka, tabel/galeri, aksi edit/kembali/cetak sesuai modul.
- **Modal:** dipakai untuk konfirmasi, pelanggan, item pembelian, foto/detail, dan ringkasan keranjang.
- **Feedback:** flash alert, SweetAlert2, validasi Bootstrap, indikator loading, empty state dan error AJAX.
- **Gerak:** transisi sekitar 0.2–0.3 detik, AOS pada publik, carousel banner, galeri pelanggan dan foto produk.

### Grid dan responsivitas

| Halaman / bagian | Pola dari markup dan CSS |
| --- | --- |
| Kategori landing | 2 kolom kecil, 3 pada `md`, 5 pada `lg` |
| Produk landing | 1 / 2 / 3 / 5 kolom pada base / `sm` / `md` / `lg`; kartu horizontal pada mobile kecil |
| Katalog publik | 1 / 2 / 3 / 4 kolom; filter membagi search/kategori/sort |
| Detail produk | Galeri dan informasi masing-masing 6 kolom pada `lg`; ditumpuk di bawahnya |
| Cabang kontak | `col-md-6`, `col-lg-4`, `col-xl-3` |
| Auth | Dua panel desktop, satu panel form pada mobile |
| Admin | Sidebar desktop, drawer sampai 991px, kartu/form menumpuk, tabel dapat digeser |

`resources/css/legacy/katalog.css` dan `contactStore.css` menambahkan container 1320px pada ≥1200px dan 1420px pada ≥1400px. Pada workspace tujuan, salinan publik kedua stylesheet tersebut sudah identik dengan sumbernya. Perubahan CSS yang sudah ada di repo tujuan dipertahankan saat pemindahan.

## 6. Inventaris dan analisis setiap halaman

Nama file pada tabel berada di `resources/views`, kecuali dinyatakan lain. Resource route Laravel dapat mendaftarkan lebih banyak endpoint daripada halaman yang benar-benar diimplementasikan; daftar berikut mengikuti view yang dirender controller.

### 6.1 Publik — 6 halaman

| URL | Template | Isi dan interaksi utama |
| --- | --- | --- |
| `/` | `mainPage.blade.php` | Search dengan suggestion, carousel promosi, kategori, produk terbaru, rekomendasi, brand/partner, video YouTube, enam benefit. Semua menjadi section vertikal; header dan footer berasal dari layout. Banner, kategori, produk dan partner dinamis; video dan sebagian benefit masih hardcoded/eksternal. |
| `/katalog` | `product.blade.php` | Category pills, pencarian, kategori, urutan terbaru/termurah/termahal, kartu produk, pagination dan empty state. Controller juga menerima `sort=rekomendasi` dari landing. |
| `/katalog/{id}` | `product-detail.blade.php` | Kembali/search, galeri foto utama dan thumbnail, nama/harga, CTA WhatsApp, SKU, kategori, kondisi, stok, video Instagram opsional, deskripsi yang dapat diperluas. Stok habis atau nomor WhatsApp kosong menghasilkan tombol disabled. |
| `/about` | `AboutStore.blade.php` | Hero identitas/deskripsi, empat nilai toko, tiga layanan, dan galeri pelanggan bila tersedia. Campuran gambar lokal/eksternal; galeri memakai kontrol geser. |
| `/contact` | `contact.blade.php` | Kanal sosial/marketplace, kartu cabang aktif dengan peta/jadwal, kontak WhatsApp, link Maps, form nama/email/pesan. Form membuka WhatsApp melalui JavaScript, bukan endpoint pengiriman email. |
| `/dinoyo-kamera-way` | `dinoyo-way.blade.php` | Filosofi dan budaya SIAPPPP, standar kerja, kegiatan harian, karier, evaluasi dan janji bersama. Isi berasal dari database melalui `DinoyoWayContent`, dengan fallback konfigurasi awal; navbar dan footer memakai label tersimpan yang sama. |

`/katalog/suggest` merupakan endpoint JSON, bukan halaman. `/admin` mengarahkan ke dashboard, bukan landing admin tersendiri.

### 6.2 Dashboard, produk dan master data — 21 halaman

| URL / mode | Template admin | Analisis fungsi dan desain |
| --- | --- | --- |
| `/admin/dashboard` | `index.blade.php` | Filter tahun/bulan/cabang, KPI pendapatan/laba/transaksi/cabang terbaik, grafik annual, ringkasan transaksi, top lima produk dan transaksi pembelian terbaru. Data dihitung melalui `DashboardMetricsService`; gunakan sebagai referensi halaman analitik. |
| `/admin/products` | `dataProduk.blade.php` | Search, kategori dan sort; tabel thumbnail/nama, SKU, harga, stok, pembaruan dan aksi. Akses tambah, detail, edit, hapus dan arsip. |
| `/admin/products/archived` | `dataProdukArchived.blade.php` | Pola tabel produk yang sama untuk data diarsipkan, dengan restore/hapus dan navigasi ke produk aktif. |
| `/admin/products/create`, `/{product}/edit` | `inputDataProduk.blade.php` | Form bersama create/edit: informasi/deskripsi/Instagram, media, klasifikasi/stok/visibilitas, harga modal/servis/jual. Preview foto dan pilihan utama; modal pengarsipan pada edit. |
| `/admin/products/{product}` | `showProduk.blade.php` | Galeri, identitas produk, status, stok, harga dan deskripsi; aksi edit, arsip/restore, serta unggah foto. |
| `/admin/products/photos` | `dataFotoProduk.blade.php` | Tabel khusus operasi foto, filter nama/kategori, pagination AJAX, tombol menuju upload per produk. Berbagi fragmen `photo_product_rows`. |
| `/admin/products/{id}/photos-upload` | `uploadProductPhotos.blade.php` | Kotak pilih banyak foto, preview, foto existing, penentuan foto utama dan hapus. Menggunakan `public/js/productImages.js`; jangan mengganti kontrak DOM tanpa meninjau script ini. |
| `/admin/customers` | `dataPelanggan.blade.php` | Tabel kode/nama/gender/telepon/alamat/NIK/jumlah transaksi, search dan sort; akses detail/edit. Penambahan pelanggan utama berlangsung melalui modal transaksi. |
| `/admin/customers/{customer}`, `/{customer}/edit` | `inputDataPelanggan.blade.php` | Kartu identitas bersama, mode detail/edit. Mode detail memuat riwayat penjualan/pembelian dan ringkasan jumlah/nilai transaksi. |
| `/admin/categories` | `dataKategori.blade.php` | Tabel gambar/nama kategori, pencarian, sort, tambah/edit/hapus, serta empty state. |
| `/admin/categories/create`, `/{category}/edit` | `inputDataKategori.blade.php` | Form singkat nama kategori dan gambar; preview upload. |
| `/admin/branches` | `dataCabang.blade.php` | Tabel nama, alamat, kontak, jam, status dan aksi; search/sort, tambah/detail/edit serta penggantian status aktif. |
| `/admin/branches/create`, `/{branch}/edit` | `inputDataCabang.blade.php` | Identitas, alamat, email/deskripsi/status, telepon, Maps, jadwal tujuh hari. Ada alat buka/tutup semua hari dan penyamaan jam. Template juga mengandung mode `isShow`, tetapi controller show saat ini memakai template terpisah. |
| `/admin/branches/{branch}` | `showDataCabang.blade.php` | Informasi cabang, kontak, lokasi/deskripsi dan jadwal; aksi kembali/edit. |
| `/admin/employees` [manager] | `dataKaryawan.blade.php` | Filter nama/jabatan/status dan sort; tabel telepon, jabatan, lama bekerja, status, detail/edit. |
| `/admin/employees/create`, `/{employee}`, `/{employee}/edit` [manager] | `inputDataKaryawan.blade.php` | Satu form create/edit/read-only: nama, NIK, jabatan, telepon, gaji, tanggal masuk/keluar, status dan alamat. Tanggal keluar mengikuti status. |
| `/admin/catalog-settings` | `catalog-settings.blade.php` | Panel logo, identitas/kontak, deskripsi, enam link kanal, galeri pelanggan, banner dan logo partner. Preview dan penghapusan aset; sumber konten beberapa halaman publik. |
| `/admin/dinoyo-kamera-way` | `dinoyo-way.blade.php` | Editor per bagian untuk semua teks Way, label menu dan navigasi, enam pilar, persentase evaluasi, tujuan CTA, serta sembilan foto. Simpan melalui POST pada URL yang sama; memakai hak akses auth seperti pengaturan katalog. |
| `/admin/permissions` [manager] | `permissions.blade.php` | Search dan filter role/status, tabel akun dan status pegawai; edit, perubahan status, regenerasi aktivasi. Status akun: aktif/pending/nonaktif sesuai nilai controller/view. |
| `/admin/permissions/create`, `/{id}/edit` [manager] | `inputPermissions.blade.php` | Form memilih karyawan, role `manager`/`staff`, dan email. Pembuatan akun terhubung ke aktivasi, bukan registrasi pelanggan publik. |
| `/admin/smart-stock` | `smart-stock/index.blade.php` | Kartu total/kritis/peringatan/aman, filter status/sort/threshold, tabel stok/rata-rata harian/hari tersisa/harga, dan notifikasi. Link aksi menuju produk. |

Smart Stock memakai histori penjualan 30 hari, pembagian stok dengan rata-rata harian, pembulatan ke bawah, dan sentinel `999` bila tidak ada penggunaan. Ini kalkulasi stok berbasis histori, bukan bukti model machine learning. Threshold halaman dibatasi 1–30 dengan default 3; hasil dapat disegarkan melalui cache umur 10 menit. Status endpoint prediksi per produk masih memakai batas tetap 3, sehingga berbeda dari threshold kustom halaman.

### 6.3 Transaksi dan QC — 12 halaman

| URL / mode | Template admin | Analisis fungsi dan desain |
| --- | --- | --- |
| `/admin/sales` | `Datapenjualan.blade.php` | Tabel kode/customer/tanggal/item/cabang/total/aksi, search/cabang/sort, pembaruan AJAX, modal ekspor bulanan PDF/Excel. Perhatikan kapitalisasi nama file. |
| `/admin/sales/create` | `listProdukJual.blade.php` | Katalog produk internal, search/kategori/sort, kontrol kuantitas, detail produk, panel ringkasan keranjang dan checkout. Produk dipaginasi 20; keranjang disimpan di session server. |
| Checkout melalui `POST /admin/sales`; `/{sale}/edit` | `inputPenjualan.blade.php` | Informasi transaksi, customer search dan modal tambah/edit customer, item, diskon/biaya/depresiasi, pembayaran dan catatan. Form create/edit menggunakan modul penjualan yang sama. |
| `/admin/sales/{sale}` | `showPenjualan.blade.php` | Informasi transaksi, ringkasan total dan tabel item; kembali/edit/cetak. |
| `/admin/purchases` | `dataPembelian.blade.php` | Tabel kode/customer/tanggal/cabang/item/status/harga deal, filter search/status/cabang/sort, AJAX dan ekspor bulanan. |
| `/admin/purchases/create`, `/{purchase}/edit` | `inputPembelian.blade.php` | Informasi transaksi, customer dan cabang, tabel draft item, modal detail pemeriksaan fisik/lensa/sensor/fungsi, negosiasi tawaran/deal, serta modal tambah/edit customer. Modul JS dipisah menurut API/form/tabel/tombol deal. |
| `/admin/purchases/{purchase}` | `reviewPembelian.blade.php` | Informasi dan total transaksi, daftar item, detail kondisi/kelengkapan dan serial, copy informasi, edit/cetak. |
| `/admin/quality-control` | `dataQC.blade.php` | Antrian item, search/kategori/sort termasuk progress, identitas/serial dan kelengkapan QC, aksi proses, QC manual dan riwayat. Memakai fragmen AJAX. |
| `/admin/quality-control/create`, `/{quality_control}/edit` | `dataQC_form.blade.php` | Form QC manual/proses: identitas, detail fisik, lensa/sensor, fungsi, kelengkapan, deskripsi final, harga, jumlah, grade, kondisi dan status akhir. Mendukung draft/arsip dan mode `readonly` dari query. |
| `/admin/quality-control/history` | `dataQC_history.blade.php` | Riwayat item lolos; filter dan akses detail melalui form QC; referensi transaksi pembelian bila ada. |
| `/admin/quality-control/archived` | `dataQC_archived.blade.php` | Tabel item gagal/arsip, search/kategori/sort, status dan konfirmasi restore. |
| `/admin/profile` | `profilPengguna.blade.php` | Profil dan informasi akun, form/modal ganti password dengan validasi, tautan reset dan modal logout. |

### 6.4 Autentikasi dan pemulihan akun — 12 halaman

| URL | Template admin | Isi dan catatan |
| --- | --- | --- |
| `/login` | `login.blade.php` | Email/password, tampil/sembunyikan password, lupa password dan aktivasi akun; memakai layout auth. |
| `/activation` | `activation.blade.php` | Memasukkan email akun untuk proses aktivasi. |
| `/activation/verify` | `verification.blade.php` | Kode verifikasi dan kirim ulang. |
| `/activation/setup-password/{token}` | `setup-password.blade.php` | Password awal dan konfirmasi; membutuhkan token aktivasi valid. |
| `/forgot-password` | `public-forgot-password.blade.php` | Meminta kode reset berdasarkan email. |
| `/verify-reset-code` | `public-verify-reset-code.blade.php` | Input kode, timer/kirim ulang dan navigasi kembali. |
| `/reset-password` | `public-reset-password.blade.php` | Password baru dan konfirmasi setelah tahapan verifikasi. |
| `/admin/profile/resetPassword` | `profilPenggunaResetPassword.blade.php` | Form password lama, password baru dan konfirmasi dalam shell admin. |
| `/admin/profile/forgot-password` | `profile-forgot-password.blade.php` | Memulai reset dari profil, email terdaftar dan informasi proses. Memakai shell admin. |
| `/admin/profile/verify-reset-code` | `verify-reset-code.blade.php` | Verifikasi kode reset dari profil; memakai layout auth meskipun route berada dalam group auth. |
| `/admin/profile/reset-forgotten-password` | `reset-forgotten-password.blade.php` | Password baru/konfirmasi serta indikator kekuatan; memakai layout auth. |
| Tidak terhubung dari route aktif | `Xindex.blade.php` | Halaman admin lama yang menyatukan metrik dan CRUD produk. Memakai `headerAdmin`/`footerAdmin` dan sejumlah nama route lama; jangan dijadikan acuan halaman admin baru. |

### 6.5 Cetak, ekspor, error dan email

**Lima template cetak/ekspor admin** menggunakan dokumen HTML mandiri, tabel rapat, dan gaya khusus keluaran; bukan shell interaktif admin.

| Template admin | Pemakaian |
| --- | --- |
| `notaPenjualan.blade.php` | `/admin/sales/{id}/print`; nota identitas toko, transaksi, item, harga dan total melalui DomPDF |
| `notaPembelian.blade.php` | `/admin/purchases/{id}/print`; nota pembelian dan kondisi item melalui DomPDF |
| `exports/sales-monthly.blade.php` | Laporan penjualan bulanan untuk keluaran berbasis view; tabel transaksi dan ringkasan |
| `exports/purchases-monthly.blade.php` | Laporan pembelian bulanan PDF; status, item dan harga deal |
| `exports/purchases-monthly-excel.blade.php` | Spreadsheet pembelian lebih terperinci, termasuk serialisasi item/unit dan penawaran |

**Tujuh template error** di `errors/` merupakan halaman mandiri dengan gaya utilitas Tailwind CDN dan ikon, sehingga tampil berbeda dari Bootstrap admin/publik.

| Template | Perilaku |
| --- | --- |
| `403.blade.php` | Akses ditolak, login/kembali/beranda |
| `404.blade.php` | Halaman tidak ditemukan, kembali/beranda |
| `429.blade.php` | Terlalu banyak permintaan; countdown 30 detik sebelum tombol coba lagi aktif |
| `500.blade.php` | Server error, coba lagi/kembali/beranda |
| `503.blade.php` | Maintenance, periksa kembali/beranda |
| `generic.blade.php` | Fallback kesalahan umum |
| `timeout.blade.php` | Server sibuk, reload/kembali; countdown refresh otomatis yang dapat dibatalkan dengan klik |

Route `/test-*`, `/simulate-timeout`, dan `/check-job/{jobId}` adalah sarana testing/diagnostik, bukan halaman bisnis dalam navigasi.

**Tiga template email** di `emails/` memiliki HTML dan style mandiri: `activation.blade.php` untuk informasi/aktivasi akun; `verification-code.blade.php` untuk kode aktivasi; `password-reset-code.blade.php` untuk kode pemulihan. Jangan menerapkan sidebar/header aplikasi ke template email.

### 6.6 Layout dan partial — 19 template pendukung

| Template | Tanggung jawab |
| --- | --- |
| `layouts/customer.blade.php` | Shell publik dan dependensi global publik |
| `layouts/admin.blade.php` | Shell admin, menu, profil, topbar, loader, footer dan logout |
| `layouts/auth.blade.php` | Shell form autentikasi dan panel brand |
| `partials/header.blade.php` | Navbar publik dan alert |
| `partials/footer.blade.php` | Identitas toko, kanal dan cabang aktif |
| `partials/floating-wa.blade.php` | CTA WhatsApp global berbasis nomor toko |
| `partials/alerts.blade.php` | Feedback session/validasi bersama |
| `partials/catalog-pagination.blade.php` | Pagination katalog bersama yang tersedia pada repo tujuan |
| `partials/dinoyo-way-aperture.blade.php` | SVG aperture yang dipakai diagram misi dan emblem janji bersama; tidak bergantung pada font ikon |
| `partials/headerAdmin.blade.php` | Header lama; dipakai halaman legacy |
| `partials/footerAdmin.blade.php` | Footer lama; dipakai halaman legacy |
| `admin/partials/customer_detail_card.blade.php` | Data pelanggan dan mode edit/read-only |
| `admin/partials/photo_product_rows.blade.php` | Baris foto produk dan pagination untuk refresh AJAX |
| `admin/partials/purchase_table_content.blade.php` | Isi tabel pembelian untuk refresh AJAX |
| `admin/partials/sales_table_content.blade.php` | Isi tabel penjualan untuk refresh AJAX |
| `admin/partials/qc_table_rows.blade.php` | Baris QC untuk refresh AJAX |
| `admin/partials/dinoyo_way_image.blade.php` | Preview, upload, reset per foto editorial dan pilar |
| `admin/partials/dinoyo_way_fields.blade.php` | Field editor Way bertingkat dengan label, nilai lama, batas panjang, dan pesan validasi |
| `vendor/pagination/bootstrap-5.blade.php` | Template pagination Bootstrap yang dipakai ulang |

Total cakupan: **6 publik + 57 template dalam admin + 7 error + 3 email + 3 layout + 8 partial global + 1 pagination = 85 template**. Tujuh partial admin sudah termasuk angka 57, dan dicantumkan kembali di tabel pendukung untuk menjelaskan reuse.

## 7. Pola implementasi yang perlu dipertahankan

### Halaman daftar admin

1. Gunakan `layouts.admin` dan title singkat sesuai modul.
2. Letakkan aksi utama pada `page-actions` atau ikuti penempatan halaman tetangga.
3. Gunakan kartu filter, tabel responsif, empty state, dan pagination.
4. Pertahankan query pencarian/filter/sort ketika pagination dan kembali dari detail.
5. Jika memakai AJAX, buat fragmen server yang sama untuk muatan pertama dan refresh. Pertahankan kontrak JSON controller dan binding aksi setelah tabel diganti.
6. Gunakan `.no-row-navigation` atau mekanisme pengecualian yang didukung script halaman terkait agar tombol tidak memicu klik baris.

### Form dan detail

- Pisahkan kelompok informasi berdasarkan tugas pengguna, seperti identitas, klasifikasi, harga dan media.
- Gunakan `@csrf`, method spoofing untuk update/delete, validasi server, `old()` dan error field.
- Gunakan model/accessor URL media yang sudah ada; jangan merangkai URL storage secara manual.
- Gunakan helper Rupiah, mask input, validasi telepon dan customer search yang tersedia ketika sesuai kebutuhan.
- Konfirmasi aksi hapus/arsip/restore dengan pola modul terkait; berikan feedback dan target kembali yang jelas.
- Bedakan kondisi `create`, `edit`, `readOnly`, `empty`, `loading`, `error`, `disabled`, dan tidak berhak akses.

### Halaman publik

- Gunakan `layouts.customer`, navigasi dan konten identitas dari `CatalogSettings`.
- Pertahankan Montserrat, latar netral, foto produk yang utuh dan hierarki nama/harga/CTA.
- Ambil pola section/card dari halaman publik yang fungsinya terdekat.
- CTA harus mengikuti kanal yang benar-benar ada: katalog, WhatsApp, Maps, Instagram atau marketplace.
- Bila menambah item navigasi, tinjau active state serta menu mobile dan footer bila relevan.

## 8. Temuan dan batasan yang memengaruhi halaman berikutnya

Temuan berikut berasal dari inspeksi kode, bukan reproduksi bug di browser. Tidak ada perbaikan fitur dilakukan dalam analisis ini.

1. **CSS sumber dan CSS aktif memiliki jalur terpisah.** Pada workspace tujuan, `katalog.css`, `contactStore.css`, `header.css`, dan `dinoyo-way.css` sudah sinkron antara resources dan public. Untuk perubahan berikutnya, perubahan resources saja belum menjamin salinan publik ikut berubah. Salin stylesheet yang terkait agar perubahan lain tetap terjaga.
2. **Cascade belum tunggal.** Admin memuat Bootstrap melalui Vite dan CDN, serta CSS layout dan legacy. Ada selector global, deklarasi berulang dan `!important`; variabel warna admin tidak otomatis mengubah semua tombol Bootstrap.
3. **Selector admin terlalu luas untuk komponen baru tertentu.** `legacy/adminpage.css` mengatur `nav` dan `nav > div` secara global untuk pagination. Halaman dengan navigasi internal perlu selector yang dibatasi.
4. **CSS katalog memuat blok akhir yang tidak valid.** Ada deklarasi `--bs-grid-columns` tanpa selector dan kurung tambahan pada media query bagian akhir. Gunakan kelas grid yang terbukti dipakai; jangan menyalin blok tersebut sebagai pola baru.
5. **Promosi belum mempunyai view.** `/admin/promotions` merujuk `admin.promotions`, tetapi file tersebut tidak ada. Route itu belum membuktikan halaman promosi telah tersedia.
6. **Resource route tidak menjamin halaman tersedia.** Contoh: `CustomerController::create()` kosong. Jangan menautkan halaman baru ke endpoint hanya karena `Route::resource` mendaftarkannya.
7. **Artefak lama masih tersimpan.** `Xindex`, partial admin lama, `xAdminProductController`, dan `PermissionsControllers` tidak menjadi sumber utama flow route saat ini. Ada juga variasi nama model CatalogSetting(s); gunakan kelas yang benar-benar dipakai controller aktif.
8. **Data identitas diasumsikan tersedia.** Header/footer dan sebagian shell mengakses `CatalogSettings::first()` tanpa fallback menyeluruh. Tampilan dengan database kosong perlu diuji.
9. **Placeholder gambar dirujuk tetapi tidak ditemukan.** Beberapa view/controller memakai `images/placeholder.jpg`; file itu tidak ditemukan pada inventaris `public`. Uji kondisi produk tanpa foto.
10. **Perilaku galeri detail berada di dua tempat.** `product-detail` memuat `productDetailGallery.js` serta script inline untuk galeri. Tinjau keduanya sebelum mengubah DOM atau handler foto.
11. **Ketergantungan eksternal cukup banyak.** Font, ikon, AOS, chart, beberapa gambar benefit/layanan, YouTube, Maps dan aset marketplace memerlukan jaringan. Tidak semua materi publik berasal dari pengaturan admin.
12. **Batas upload belum seragam.** UI upload foto menyebut 10 gambar dan 5 MB/file; FormRequest batch menerima gambar hingga 10 MB/file, sedangkan endpoint single memakai 5 MB. Selaraskan kontrak yang dipilih jika halaman baru menyertakan upload.
13. **Threshold Smart Stock belum seragam.** Halaman menerima threshold kustom, endpoint prediksi produk menggunakan angka 3. Jangan memperluas perbedaan ini pada tampilan baru.
14. **Bahasa dan aksesibilitas belum konsisten.** Layout menyatakan `lang="en"` meski mayoritas konten Indonesia; sebagian kontrol ikon hanya memiliki title. Sidebar juga mengatur `aria-hidden` berdasarkan drawer. Audit keyboard, focus dan pembaca layar saat halaman baru diverifikasi.

## 9. Acuan praktis untuk permintaan halaman baru

Sebelum implementasi, tentukan lokasi halaman (**publik / admin / auth / cetak**), peran pengguna, tujuan utama, sumber data, aksi utama, dan menu induknya. Spesifikasi halaman baru belum diberikan pada sesi analisis ini.

| Kebutuhan halaman baru | Referensi paling dekat |
| --- | --- |
| Daftar master data | `admin/dataProduk.blade.php`, `admin/dataKategori.blade.php` |
| Form data dengan beberapa bagian | `admin/inputDataProduk.blade.php`, `admin/inputDataCabang.blade.php` |
| Detail dan riwayat pelanggan | `admin/inputDataPelanggan.blade.php` dan partial kartu |
| Analitik / KPI | `admin/index.blade.php`, `admin/smart-stock/index.blade.php` |
| Pemilihan produk dan keranjang internal | `admin/listProdukJual.blade.php` |
| Transaksi banyak item | `admin/inputPenjualan.blade.php`, `admin/inputPembelian.blade.php` |
| Proses pemeriksaan / status | `admin/dataQC_form.blade.php` |
| Pengaturan media/konten publik | `admin/catalog-settings.blade.php` |
| Landing atau section pemasaran | `mainPage.blade.php`, `AboutStore.blade.php` |
| Produk dan filter publik | `product.blade.php`, `product-detail.blade.php` |
| Informasi lokasi dan kontak | `contact.blade.php` |
| Login / kode / pemulihan | `layouts/auth.blade.php` dan halaman `public-*` |
| Dokumen keluaran | `admin/nota*.blade.php`, `admin/exports/` |

Verifikasi saat implementasi halaman baru:

- Route, nama route, middleware dan menu aktif sesuai konteks.
- Data dan aksi valid di server; visibilitas tombol mengikuti hak akses.
- Keadaan normal, kosong, error validasi, loading, disabled dan data tanpa gambar dapat dipahami.
- Desktop dan mobile tidak menutupi konten dengan header/sidebar/keranjang; tabel dan modal dapat dipakai.
- Search/filter/pagination mempertahankan state yang diperlukan.
- Keyboard, label, focus, teks alternatif dan kontras diperiksa pada browser.
- Entry Vite dan/atau salinan CSS publik yang sesuai benar-benar termuat.
- Build dan pemeriksaan perilaku yang relevan dijalankan setelah dependensi/runtime tersedia.

## 10. Kesimpulan desain

SiDiKa memiliki dua bahasa visual utama: **katalog publik yang mengutamakan foto dan kontak toko**, serta **admin berbasis tabel, form, dan proses operasional**. Autentikasi dan dokumen keluaran memakai shell khusus.

Halaman baru sebaiknya mengikuti layout, hierarki informasi, komponen, kontrak data dan alur bisnis yang terdekat. Gunakan dokumen ini sebagai peta awal, lalu baca ulang controller, view, CSS aktif dan script modul terkait sebelum mengubah implementasi.

## 11. Tambahan halaman Dinoyo Kamera Way

Halaman publik `/dinoyo-kamera-way`, bernama route `dinoyo-way`, ditambahkan melalui `PageController::dinoyoWay()`. Tautan tersedia di navbar dan kolom identitas footer. Inventaris terkini di repo tujuan berjumlah 85 template setelah halaman publik, editor admin, dan partial visual ditambahkan.

Konten mengadaptasi `THE_DINOYO_KAMERA_WAY.pdf`: filosofi, misi, enam pilar SIAPPPP, standar kerja, Daily Winning System, budaya kinerja, jalur karier, evaluasi, janji bersama, dan rekomendasi penerapan. Bagian usulan tetap ditandai sebagai usulan/rekomendasi, sehingga tidak menyatakan seluruh sistem HR sudah berlaku.

Copy, label, alt gambar, dan kelompok isi awal berada pada `config/dinoyo-way.php`. Editor admin kini menyimpan perubahan ke kolom JSON `catalog_settings.dinoyo_way_content`. `DinoyoWayContent` menggabungkan nilai tersimpan dengan nilai awal untuk field yang belum tersedia; Blade merender teks dengan escaping.

Desain memakai layout customer, Montserrat, foto kamera lokal, hero gelap, aksen oranye, section editorial, daftar berpembatas, dan navigasi bagian sticky. CSS khusus berada pada `resources/css/legacy/dinoyo-way.css` beserta salinan aktif `public/css/legacy/dinoyo-way.css`. `public/js/dinoyo-way.js` menyesuaikan offset header, penanda bagian aktif, dan reveal ringan. Konten tetap terlihat tanpa JavaScript; preferensi reduced motion menonaktifkan animasi.

Navbar pada 992–1199px memakai jarak dan ukuran teks lebih rapat agar tautan tambahan tetap satu baris. Halaman tidak memerlukan entry Vite baru. Pemeriksaan fitur berada pada `tests/Feature/DinoyoWayTest.php` dan menggunakan SQLite in-memory, tanpa mengubah database aplikasi.

## 12. Alur dinamis Dinoyo Kamera Way

Implementasi mengikuti pola pengaturan website yang sudah ada: group route `auth`, form Blade admin, validasi server, penyimpanan pada baris `CatalogSettings` yang sama, lalu pembacaan pada halaman publik. Setting Web Katalog tetap menangani identitas toko dan aset katalog; editor Way menangani isi budaya perusahaan.

| Langkah | Route / komponen | Perilaku |
| --- | --- | --- |
| Buka editor | `GET /admin/dinoyo-kamera-way`, `admin.dinoyo-way.edit` | `DinoyoWayController::edit()` memuat isi database dan fallback; menu tersedia pada Manajemen |
| Simpan | `POST /admin/dinoyo-kamera-way`, `admin.dinoyo-way.update` | `DinoyoWayUpdateRequest` memvalidasi struktur, teks, bobot, dan foto; controller memperbarui JSON tanpa mengganti identitas/kontak toko |
| Tampilkan publik | `GET /dinoyo-kamera-way`, `dinoyo-way` | `PageController` membaca `DinoyoWayContent`; foto menggunakan resolver URL sesuai penyimpanan |
| Label navigasi global | View composer header/footer pada `AppServiceProvider` | Navbar dan footer langsung menggunakan nama menu dari isi Way tersimpan |

Hak akses mengikuti pengaturan katalog yang ada: pengguna yang sudah login, termasuk staff dan manager, dapat mengedit. Permintaan tamu mengikuti handler aplikasi yang mengembalikan halaman 403. Form menggunakan CSRF dan validasi server; tidak menyediakan endpoint publik untuk menyimpan.

Editor memakai kelompok yang dapat dibuka/tutup, label manusiawi, input teks/textarea, angka untuk bobot, dan tombol simpan bersama. Susunan elemen serta ID anchor tetap; admin mengubah isinya tanpa merusak navigasi bagian. Seluruh bobot evaluasi harus berjumlah 100%. Teks yang gagal validasi tetap tersedia melalui `old()`.

Foto hero menerima JPG, PNG, atau WebP hingga 4 MB dan 5000 × 5000 piksel. Upload menggunakan `ImageUpload` dengan R2 dan fallback lokal; resolver menangani path `public/catalog/dinoyo-way/` sebagai URL storage lokal. Pilihan reset mengembalikan foto awal. Penggantian teks mempertahankan foto yang sudah ada. Hasil `putFileAs()` yang bernilai false kini dianggap gagal; bila kedua penyimpanan gagal, editor mempertahankan input dan tidak memublikasikan URL foto yang tidak tersedia.

Migrasi `2026_10_28_000000_add_dinoyo_way_content_to_catalog_settings.php` hanya menambahkan kolom JSON nullable. Migrasi sudah diterapkan pada database lokal workspace ini tanpa menjalankan ulang seeder. Pada environment lain, jalankan migrasi sebelum memakai tombol simpan editor.

Pengujian mencakup akses tamu/staff, simpan dan pembacaan ulang, label navbar/footer, escaping, fallback field, validasi bobot/anchor, upload/reset, kegagalan storage, serta migrasi dan rollback. Pemeriksaan browser memakai database sementara untuk uji simpan dan tampilan desktop/mobile; isi database operasional tidak diubah oleh uji browser.

## 13. Penyempurnaan visual halaman Way

Halaman Way kini memakai komposisi editorial bergambar dengan ritme section terang, gelap, dan merah bata. Header khusus halaman ini dipadatkan menjadi 88px pada desktop dan 72px pada mobile. Foto hero tetap bisa diganti lewat admin; foto ilustrasi cerita dan pemeriksaan produk menggunakan aset lokal yang sudah ada di repository.

- Cerita perusahaan menggunakan foto meja kerja, keterangan gambar, dan disclosure HTML native untuk filosofi lengkap.
- Misi divisualisasikan sebagai diagram orbit lima lingkup manfaat. Nama lingkup dan identitas pusat diagram dapat diedit di admin.
- Enam pilar memakai pemilih tab dengan satu panel visual aktif. Navigasi keyboard mendukung panah kiri/kanan, Home, dan End. Tanpa JavaScript, keenam panel tampil sebagai konten berurutan.
- Standar kerja dipadukan dengan foto detail kamera. Alur harian menggunakan jalur ikon terhubung dengan bobot visual setara untuk kelima tahap.
- Jenjang karier memakai tangga pada desktop dan jalur vertikal terhubung pada mobile; label usulan tetap ditampilkan.
- Bobot evaluasi divisualisasikan melalui donut SVG yang dihitung dari angka tersimpan. Nilai persentase tetap tersedia dalam daftar teks.
- Janji bersama memakai komposisi poster merah bata dan emblem aperture SVG.
- Navigasi bagian menunjukkan arah geser ketika tautan melebihi lebar layar dan menjaga tautan aktif tetap terlihat. Offset anchor mengikuti tinggi header yang sebenarnya.

Label CTA tambahan, disclosure, keterangan/alt foto, lingkup misi, dan label grafik berada dalam konfigurasi default dan otomatis tersedia di editor admin. Data lama tetap dibaca dengan fallback per field; penyempurnaan visual ini tidak membutuhkan migrasi baru atau menimpa isi database.

Dua putaran review visual dan pemeriksaan browser dicatat pada `docs/superpowers/reviews/2026-10-28-dinoyo-way-visual.md`. Pemeriksaan akhir mencakup lebar 320, 375, 390, 768, 1024, dan 1440px, serta keyboard, reduced motion, dan tanpa JavaScript. Suite terakhir lulus 49 test dengan 754 assertion; peringatan skema XML PHPUnit yang deprecated sudah ada sebelumnya.

### Revisi terfokus: section 1 dan 2

Penanda dekoratif SIAPPPP dan panah pada section filosofi dihapus. Penggantinya adalah CTA `Kenali enam pilar kami` yang benar-benar menavigasi ke `#nilai`. Filosofi lengkap memakai disclosure native berlatar netral tanpa garis pemisah panjang; label berubah menjadi `Ringkas filosofi` ketika terbuka. Label CTA dan kedua keadaan disclosure dapat diedit melalui admin.

Pusat diagram misi kini memakai label `Misi besar`. Identitas pada emblem janji bersama dipisahkan ke `pledge.emblem_label`, sehingga perbaikan section misi tidak mengganti emblem pada section lain. `DinoyoWayContent` mengenali data versi lama yang masih memakai satu label bersama dan mempertahankan identitas emblem lamanya tanpa menulis ulang database. Label misi dan emblem tetap dapat diedit secara independen setelah disimpan melalui editor baru.


### Revisi terfokus: section 3 sampai 6

Kartu pemilih pilar memusatkan ikon dan label tanpa nomor pojok. Hover memakai spotlight mengikuti pointer, tilt ringan, dan gerak ikon; klik memakai ripple dan transisi panel. Pemilihan lewat klik atau keyboard menggulirkan deretan kartu ke bawah header serta navigasi bagian. Halaman tidak bergulir otomatis saat pertama dibuka. Preferensi reduced motion menonaktifkan efek gerak.

Foto standar kerja mengikuti tinggi kolom kanan pada desktop, lalu memakai tinggi tersendiri saat kedua kolom bertumpuk. Kelima tahap Daily Winning System kini setara; penekanan khusus tahap ketiga dihapus. Tiga prinsip budaya memakai komposisi ilustrasi timbangan SVG dan pernyataan bertumpuk pada bidang krem. Semua teks tetap memakai sumber konten dinamis yang sama. Markup section 7?8 dan CSS khusus keduanya dipertahankan.


### Navigasi dan motion halaman

Navigasi bagian dipusatkan saat semua tautan muat; pada layar sempit tetap dapat digeser dengan petunjuk arah. Indikator pill mengikuti bagian aktif, hover mengangkat tautan, klik memberi respons pegas, dan garis tipis menunjukkan progres membaca. CTA tambahan pada filosofi dihapus beserta field default editornya; data tersimpan lama tidak diubah.

Motion menggunakan CSS, IntersectionObserver, requestAnimationFrame dan Web Animations API tanpa dependensi tambahan. Hero memiliki entrance berurutan dan depth saat scroll; foto editorial bergerak ringan; orbit dan aperture merespons posisi baca; elemen pilar, tahap harian, prinsip, karier, evaluasi, janji dan rekomendasi muncul berurutan. Grafik mendapat entrance putar/scale. Bentuk dan urutan konten tetap. Reduced motion menghapus gerak dan membuka elemen tertunda, termasuk saat preferensi berubah ketika halaman sedang terbuka. Konten tetap tersedia tanpa JavaScript.


Animasi halaman Way tidak memakai blur: reveal dan pergantian panel hanya memakai opacity serta transform. Navigasi bagian memakai latar putih solid tanpa backdrop blur.


### Detail interaksi dan penutup

Caption foto filosofi tanpa ikon kamera; panel visual pilar tanpa nomor dekoratif. Kedua disclosure (filosofi dan budaya kinerja) memakai slot tetap 262px: cuplikan saat tertutup dan konten lengkap dalam viewport maksimal 188px saat terbuka. Area teks dapat difokuskan serta digulir dengan keyboard. Buka/tutup memakai opacity dan translate melalui Web Animations API, menghormati reduced motion, dan menangani klik berulang tanpa menyisakan state animasi. Konten admin tetap lengkap.

Pusat Misi besar mendapat hover scale/rotasi dan cincin ekspansi. Batang jenjang karier mendapat lift, peregangan ringan dan sapuan cahaya. Penutup Kenali kami lebih dekat memakai bidang krem, headline besar, aperture dekoratif, serta dua tautan kontak/katalog. Semua copy tetap berasal dari konfigurasi/database yang sama.


Audit tombol filosofi: trigger mengikuti lebar label (sekitar 202px untuk label default), tinggi minimum 48px, tanpa panel kosong abu-abu. Slot filosofi transparan setinggi 214px mempertahankan stabilitas halaman; viewport teks maksimal 150px. Pemeriksaan buka/tutup pada 1440/390/320px menghasilkan tinggi dokumen yang sama dan tidak ada overflow horizontal.


Disclosure filosofi dan kinerja kini berbagi class compact: tinggi trigger 48px, lebar mengikuti label, tanpa preview maupun latar panel kosong. Kinerja memakai chevron dan label tutup dinamis performance.read_less. Animasi buka/tutup tetap sama, slot 214px dan viewport teks 150px. Audit desktop/mobile memastikan tinggi dokumen tidak berubah saat toggle.


Section 06 menghapus slot cadangan 214px: tinggi mengikuti isi disclosure (48px saat tertutup). Panel prinsip kembali mendekati pengantar dengan jarak section normal. Saat dibuka, section berkembang mengikuti teks dan kembali ringkas setelah ditutup; aturan section filosofi tetap terpisah. Audit desktop/mobile mengonfirmasi slot tertutup 48px serta tinggi section kembali ke nilai awal setelah toggle.


Hover section 06: panel prinsip merespons pointer dengan tilt maksimal satu derajat per sumbu, sorotan radial, dan kedalaman bayangan; ilustrasi mendapat drift ringan dan cincin ekspansi. Pernyataan yang di-hover bergeser 7px dengan aksen warna. Efek hanya aktif pada pointer presisi dan tanpa reduced motion. State dibersihkan saat pointer keluar atau preferensi berubah. Audit browser memverifikasi hover, reset, reduced motion dan lebar mobile tanpa overflow.


Filosofi kini ditampilkan langsung setelah paragraf pengantar dengan subjudul dinamis philosophy.subtitle (default: Tempat bertumbuh bersama.). Tombol baca/ringkas, slot cadangan, dan scroll internal di section 01 dihapus; label disclosure filosofi juga dihapus dari konfigurasi editor. Dua paragraf yang sebelumnya berada dalam disclosure tampil sebagai body 16px / line-height 1.9, sama dengan pengantar. Audit screenshot desktop/mobile memverifikasi hierarki, jarak, dan tidak ada overflow 320/390px. Disclosure kinerja tetap terpisah.


Panel enam pilar kini memakai foto editorial fotografi, meja kerja dan detail produk. Konstruksi ikon besar serta ring dekoratif dihapus, termasuk aturan CSS dan observasi animasinya. Foto memakai crop cover pada desktop dan tinggi 220px pada mobile. Enam panel diuji dengan foto termuat dan tanpa overflow mobile; foto tambahan disimpan lokal, sumber pada docs/way-photo-sources.md. Foto tidak menyatakan bahwa orang yang tampil merupakan karyawan Dinoyo.


Section 01 menghapus subjudul Tempat bertumbuh bersama; isi tampil sebagai tiga paragraf berurutan (pengantar perusahaan dan dua paragraf filosofi), dengan ukuran teks konsisten. Field subtitle filosofi tidak lagi ditawarkan editor.


Section 06 menghapus disclosure kinerja beserta dua paragraf tambahannya. Bagian ini kini langsung menampilkan pengantar dan panel tiga prinsip; field konten yang dihapus tidak lagi ditawarkan editor.


Penutup Kenali kami lebih dekat kini memakai bidang navy tanpa aperture dekoratif. Judul/deskripsi berada di kiri dan dua CTA sejajar di kanan pada desktop: kontak berwarna aksen, katalog outline. Pada mobile kedua CTA tersusun penuh, tinggi 56px, dengan jarak konsisten. Gaya penutup lama dihapus. Audit screenshot desktop/mobile serta lebar 320/390/1024/1440px menunjukkan tombol tidak bertumpuk, tanpa overflow, dan tautan kontak/katalog tetap benar.


### Audit editor dinamis (29 Oktober 2026)

Semua field teks aktif diuji melalui POST admin lalu diperiksa kembali pada HTML publik. Tidak ada lagi field paragraf filosofi yang disimpan tetapi tidak tampil: dua paragraf aktif dinormalisasi dari data lama yang masih memiliki tiga paragraf, tanpa menulis ulang database saat membaca. Judul, deskripsi, label navigasi, isi setiap bagian, contoh perilaku, prinsip, serta bobot evaluasi tetap berbasis JSON CatalogSettings.

Sembilan foto dapat diganti/reset secara independen: hero, filosofi, standar kerja, dan enam pilar. Jalur foto dikelola server, tidak menjadi input teks. Preview tersedia pada bagian terkait; foto pilar berada bersama field pilar masing-masing. Upload memakai ImageUpload/R2 dengan fallback lokal; JPG/PNG/WebP maksimal 4 MB dan 5000 x 5000 per foto. Simpan teks mempertahankan foto. Kegagalan upload tidak memublikasikan teks atau path baru; input teks tetap tersedia untuk dicoba ulang. Aset dekoratif SVG, struktur, dan animasi tetap bagian desain.

Tujuan CTA hero/penutup dapat diedit: http/https, path lokal, atau anchor bagian yang tersedia. Skema skrip/data, path protokol relatif, backslash dan karakter kontrol ditolak. Anchor navigasi bagian tetap dikelola sistem. Nilai lama yang belum memiliki field baru mendapat fallback default; tidak perlu migrasi baru untuk perluasan editor ini.

Verifikasi: 49 test / 754 assertion lulus. Browser dengan database dan storage sementara di drive D membuktikan perubahan paragraf, pilar, foto, judul penutup dan tujuan CTA sampai ke publik; reset satu foto tidak mengubah foto atau teks lain. Terdapat sembilan input foto, tanpa error JavaScript. Database operasional tidak diubah oleh audit.


Editor admin menyembunyikan dan mengunci field Teks tombol (action) serta Tujuan tombol utama (action_url) pada hero dan penutup. Simpan konten tetap mempertahankan nilai tombol utama yang sudah tersimpan atau fallback default. Whitelist validasi menolak pengubahan field tersebut melalui request. Pengaturan tautan tambahan tetap tersedia.


Penyederhanaan editor untuk pengguna nonteknis: seluruh pengaturan tautan (teks/tujuan CTA dan navigasi) serta teks alternatif foto dikeluarkan dari form dan whitelist input. Pengaturan tersimpan atau default tetap dipakai di publik, termasuk alt untuk aksesibilitas. Section navigasi tidak muncul sebagai kelompok kosong; upload/reset foto dan isi utama tetap dapat diedit.
