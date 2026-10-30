# Deployment SiDiKa di Hostinger Business

Repo server: `/home/u640357930/domains/dinoyokamera.com/public_html`.
Skrip lokal: `scripts/deploy-hostinger.sh`. Jalankan menggunakan akun hosting melalui SSH Linux, tanpa sudo.

## Melanjutkan setup yang sudah berhasil build dan migrasi

Kirim skrip dari PowerShell laptop:

```powershell
scp -P 65002 "D:\collages\semester3\Sistem Informasi\SiDiKa\scripts\deploy-hostinger.sh" u640357930@145.79.28.168:~/deploy-sidika.sh
```

Jalankan dalam terminal SSH:

```bash
bash ~/deploy-sidika.sh /home/u640357930/domains/dinoyokamera.com/public_html --finish-storage
```

Mode ini tidak memasang dependensi, menjalankan build/migrasi, atau fetch/reset Git. Public storage link diperiksa tanpa mengganti link yang salah atau menghapus folder existing. URL gambar diperiksa menggunakan token acak dan gambar PNG valid yang dibuat dengan GD. Token harus cocok dan gambar harus dapat didekode; gambar yang dioptimalkan CDN tetap diterima. File pengujian milik skrip dibersihkan setelah selesai.

Jika `/storage` tidak bekerja sedangkan `/public/storage` telah terbukti melayani token dan gambar yang benar, skrip menyesuaikan URL disk public pada Laravel. Skrip tidak mengubah `.htaccess`.

1. Config cache harus nonaktif; jika aktif, skrip berhenti sebelum mengubah pengaturan.
2. `.env` dibackup ke direktori akun hosting dengan permission 600.
3. Bila diperlukan, tepat satu baris konfigurasi URL lama pada `config/filesystems.php` diganti agar mendukung `PUBLIC_STORAGE_URL`. File source dibackup terlebih dahulu. Format source yang berbeda ditolak. Perubahan ini sama dengan versi config pada repo lokal.
4. Hanya `PUBLIC_STORAGE_URL` ditambahkan/diperbarui menjadi URL `/public/storage`. APP_KEY, database, dan pengaturan lain tetap dipertahankan. Entri duplikat ditolak.
5. Proses PHP baru memeriksa URL yang dihasilkan Laravel, HTTP gambar dan token, serta dua halaman publik. Jika gagal, `.env` dan source config dikembalikan dari backup. Pesan error menyebut pemeriksaan yang gagal.
6. Pengulangan skrip tidak menambah entri konfigurasi baru ketika URL sudah bekerja.

Backup disimpan di `~/.local/share/sidika-deploy/backups`. Backup `.env` mengandung rahasia: jangan dibagikan atau dipindahkan ke direktori publik. Tidak perlu membuat APP_KEY baru, menjalankan seeder, atau mengulang build untuk perubahan URL ini.

Pembaruan satu baris source config merupakan perubahan lokal server sampai perubahan repo yang sama masuk main. Skrip deployment lengkap akan tetap menolak source yang berbeda dari HEAD; tidak melakukan reset/restore otomatis untuk mengatasinya.

## Deployment lengkap untuk perubahan berikutnya

Pastikan source server telah sinkron dengan main dan tidak memiliki perubahan lokal yang belum diselesaikan (termasuk source untracked). File root `.htaccess` dan `index.php` merupakan pengecualian khusus layout hosting; direktori hasil build/cache/storage juga dikecualikan. Lalu jalankan:

```bash
bash ~/deploy-sidika.sh /home/u640357930/domains/dinoyokamera.com/public_html --check
bash ~/deploy-sidika.sh /home/u640357930/domains/dinoyokamera.com/public_html
```

Skrip memastikan origin sesuai dan HEAD sama dengan hasil fetch main terbaru, memasang Composer/npm sesuai lockfile, membackup aset, membangun Vite dan copy-css, membersihkan cache lama, menjalankan migrasi yang belum diterapkan, dan membuat view cache. Config cache tidak diaktifkan karena env() masih digunakan di model/provider; route cache tidak diaktifkan karena ada route closure. Untuk migrasi produksi, gunakan mekanisme backup database hosting yang berlaku.

Node 22 dipasang dari arsip tar.gz resmi dengan verifikasi SHA-256 jika versi tersedia belum kompatibel. Process substitution Bash tidak dipakai karena /dev/fd tidak tersedia pada akun ini. Link storage menggunakan ln -s dari SSH karena fungsi PHP symlink() dinonaktifkan pada akun ini.

## Bukti dan batas verifikasi

Server telah membuktikan link storage benar dan gambar/token dapat diakses melalui `/public/storage`. Percobaan alias rewrite sebelumnya gagal dan `.htaccess` dipulihkan; penyebab spesifik alias tidak diklaim telah ditemukan. Strategi saat ini memakai konfigurasi URL filesystem Laravel dan tidak bergantung pada alias tersebut.

Pengujian lokal mencakup upload admin yang menghasilkan URL hosting di editor dan halaman publik, semua tes Way, serta eksekusi finish-storage melalui PHP/GD dan Apache. Pengujian skrip memeriksa pembaruan config lama, pelestarian pengaturan lain, eksekusi ulang, pemulihan source dan .env pada HTTP 503, penolakan key duplikat, dan penolakan format source yang berbeda. Semua ini bukan bukti eksekusi akhir pada akun Hostinger; hasil server tetap dikonfirmasi dari keluaran pengguna.

## Rujukan resmi

- [Laravel 10 filesystem URLs](https://laravel.com/docs/10.x/filesystem#url-host-customization)
- [Hostinger document root](https://www.hostinger.com/support/1583494-what-is-the-path-to-your-website-s-root-home-directory-and-how-to-change-it-in-hostinger/)
- [Hostinger symlinks melalui SSH](https://www.hostinger.com/support/1583694-how-to-create-a-symlink-at-hostinger/)
- [Hostinger disabled PHP functions](https://www.hostinger.com/support/3212034-how-to-enable-disabled-php-functions-in-hostinger/)
- [Hostinger CDN image optimization](https://www.hostinger.com/support/7935917-hostinger-cdn-website-optimization/)

## Aset Way setelah deployment

Pemeriksaan HTTP 200 pada halaman dan storage tidak membuktikan semua CSS/JavaScript dimuat. Pada 30 Oktober 2026, HTML production memuat `/js/dinoyo-way.js` yang mengembalikan 404, sementara `/public/js/dinoyo-way.js` tersedia. Tanpa skrip ini, offset sticky memakai 110px walaupun header desktop hanya 88px. Reproduksi lokal dengan layout hosting membuktikan celah 22px.

PublicAssetHelper menyamakan path JavaScript, gambar bawaan Way, dan CSS dengan layout hosting yang sebelumnya dipakai CssAssetHelper. Versi SHA-256 konten pada URL aset mencegah penggunaan file lama dari cache browser/CDN. Perubahan tidak memerlukan npm build karena file JavaScript dan CSS fisik tidak berubah; source PHP/Blade perlu diterapkan dan view cache dibersihkan.

Pemeriksaan browser pada viewport 1440px dan 390px membuktikan offset navbar tepat di bawah header, WhatsApp terlihat dan menerima hit test pada tiga posisi scroll, tidak ada horizontal overflow, serta menu mobile yang diperluas memperbarui tinggi header. Pemeriksaan hosting lewat browser otomatis dibatasi challenge HTTP 403; hasil UI ini berasal dari reproduksi lokal dengan struktur hosting, bukan klaim perbaikan production yang sudah diterapkan.

## Update source PHP/Blade setelah perbaikan admin dan aset masuk main

Setup sebelumnya dapat meninggalkan perubahan source pada server (config URL storage dan hotfix aset). Simpan perubahan pada source terkait ke Git stash terlebih dahulu, termasuk helper baru yang mungkin belum tracked. Jangan stash seluruh repo: `.env`, upload, dan konfigurasi root hosting harus tetap berada di tempatnya.

```bash
cd /home/u640357930/domains/dinoyokamera.com/public_html
git stash push -u -m "backup source Way sebelum update main" -- app/Helpers app/Services/DinoyoWayContent.php config/filesystems.php resources/views/dinoyo-way.blade.php resources/views/admin/dinoyo-way.blade.php
git pull --ff-only origin main
php artisan config:clear
php artisan view:clear
```

Lanjutkan perintah cache hanya jika pull berhasil. Jangan jalankan stash pop otomatis: main sudah memuat perbaikan yang sebelumnya diterapkan secara manual. Stash tetap tersedia sebagai backup. Release ini hanya mengubah source PHP/Blade, pengujian dan alat deployment; tidak mengubah dependensi atau source CSS/JavaScript, sehingga tidak memerlukan npm build maupun migrasi baru. Nilai PUBLIC_STORAGE_URL di .env tetap dipakai.
