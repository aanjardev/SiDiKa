# Audit storage lokal dan editor Way

## Perubahan

- Controller Way secara eksplisit memilih disk public pada ImageUpload.
- File upload langsung ke storage/app/public/catalog/dinoyo-way/; tidak mencoba R2.
- Optimasi WebP dan penanganan gagal upload tetap berlaku.
- Perilaku default R2 untuk fitur lain tidak berubah dan diuji tersendiri.
- Referensi foto R2 versi lama masih dapat dibaca; database lokal saat audit memiliki nol foto R2.

## Automated backend tests

Suite penuh: 53 test, 820 assertion lulus. Mencakup teks admin ke publik, sembilan foto, src per panel, file WebP, preserve/reset, URL pengganti yang berbeda, akses/auth, validasi, dan penyimpanan gagal. Mock R2 melarang exists/putFileAs/url pada pengujian upload Way. Peringatan skema XML PHPUnit deprecated sudah ada sebelumnya.

## Automated browser test

Fixture SQLite dan public storage terisolasi digunakan pada port 8174. Browser mengubah 147 field teks dan mengunggah sembilan foto uji yang unik melalui form. Setelah simpan, seluruh marker teks ditemukan di HTML publik dan sembilan gambar berhasil didekode dari URL lokal. Browser kemudian mengganti satu foto dan judul, memverifikasi src baru serta foto lain tetap, lalu mereset satu foto tanpa mengubah foto lain. Tidak ada error JavaScript. Database operasional tidak diubah.
