# Review visual Dinoyo Kamera Way

Tanggal: 28 Oktober 2026.

## Tujuan

Mengurangi dominasi paragraf dengan komposisi bergambar, diagram, serta interaksi yang mendukung pemahaman. Konten budaya, label usulan, dan integrasi editor admin tetap dipertahankan.

## Putaran pertama

Implementasi menambahkan foto editorial, disclosure filosofi/kinerja, diagram orbit misi, pemilih enam pilar, foto produk, jalur harian, tangga karier, donut evaluasi, dan poster janji bersama.

Pemeriksaan browser menunjukkan gambar termuat, satu panel pilar aktif, navigasi Home/End berfungsi, dan tidak ada overflow horizontal pada desktop/mobile. Reviewer visual independen kemudian menilai tangkapan layar setiap bagian.

Temuan yang ditindaklanjuti:

1. Navigasi bagian pada mobile memerlukan petunjuk bahwa baris dapat digeser.
2. Grid karier mobile kehilangan makna progresi dari tangga desktop.
3. Jalur harian desktop memerlukan titik fokus dan teks yang lebih mudah dibaca.
4. Ikon aperture dari font tidak tampil konsisten.
5. Chrome header perlu lebih ringkas dan kontras sejak tampilan awal.

Beberapa tangkapan layar per bagian menampilkan header fixed di atas judul. Uji klik anchor langsung membuktikan judul tidak tertutup: posisi judul sekitar 244px, sedangkan batas bawah header/menu sekitar 122px. Tangkapan bagian berikutnya menyembunyikan header/menu hanya selama pengambilan screenshot agar komposisi tidak tertutup overlay. Tangkapan cover tetap menampilkan header produk sebenarnya.

## Putaran kedua

- Header khusus halaman dipadatkan menjadi 88px desktop / 72px mobile dengan logo dan teks navigasi yang kontras.
- Baris navigasi mendapat fade/chevron kiri-kanan serta scroll otomatis untuk tautan aktif.
- Karier mobile diubah menjadi enam tahap vertikal terhubung.
- Tahap operasional pada jalur harian mendapat penekanan visual; ukuran teks ditingkatkan.
- Aperture memakai SVG lokal, bukan glyph font.
- Ukuran teks pemilih dan panel pilar pada mobile ditingkatkan.

Reviewer independen menilai putaran kedua siap: tidak ada masalah visual berat pada clipping, kepadatan, keterbacaan, atau konsistensi. Galeri pelanggan saat pemeriksaan belum berisi foto; foto lokal yang tersedia dipakai tanpa membuat representasi tim/toko yang tidak berasal dari sumber sebenarnya.

## Bukti verifikasi akhir

| Pemeriksaan | Hasil |
| --- | --- |
| Lebar 320 / 375 / 390 / 768 / 1024 / 1440px | Lebar dokumen sama dengan viewport; tidak ada overflow horizontal |
| Tab pilar dengan JavaScript | Enam tab, satu panel terlihat; Home/End dan pemilihan langsung diperiksa |
| Reduced motion | Tidak ada konten reveal tersembunyi; animasi dinonaktifkan |
| Tanpa JavaScript | Enam panel pilar tersedia; dua disclosure HTML native tetap dapat digunakan |
| Gambar desktop | Tidak ada gambar yang gagal dimuat |
| Grafik evaluasi | Test empat bobot 25% membuktikan panjang dan offset arc mengikuti data |
| Suite proyek | 43 test, 498 assertion lulus; peringatan XML PHPUnit yang sudah ada tetap tercatat |

Browser menggunakan database sementara. Pengujian visual tidak mengubah konten database operasional.


## Revisi section 3?6 berdasarkan masukan pengguna

- Pilar: ikon/label terpusat, nomor pojok dihapus, spotlight/tilt/ripple dan transisi panel, scroll ke bawah navbar setelah pemilihan; keyboard dan reduced motion diperiksa.
- Standar kerja: batas bawah foto dan kolom kanan sama pada desktop (1441.8125px pada pengukuran browser).
- Daily Winning System: kelima marker identik ukurannya dan warnanya; highlight tahap ketiga dari putaran sebelumnya dihapus.
- Budaya: banner sebelumnya diganti ilustrasi keseimbangan dan tiga pernyataan bertumpuk. Screenshot desktop dan mobile diperiksa.
- Section 7?8: markup dan CSS khusus dibandingkan dengan salinan sebelum perubahan; tidak berubah.

Browser memverifikasi satu panel aktif, posisi kartu setelah klik berada dalam selisih 1px dari offset header/nav, ripple aktif pada gerak normal dan tidak dibuat pada reduced motion, serta tidak ada overflow desktop/mobile. Suite PHP lulus 44 test / 507 assertion.


## Navigasi terpusat dan motion menyeluruh

Pemeriksaan browser pada localhost:8000 mengonfirmasi pusat grup tautan tepat pada tengah viewport desktop (selisih 0px), indikator mengikuti Enam pilar, dan CTA filosofi tidak dirender. Sebanyak 44 elemen diberi entrance berurutan. Tidak ada overflow pada 320, 390, 768, 1024 dan 1440px; tidak ada error JavaScript. Screenshot desktop/mobile diperiksa. Perubahan preferensi reduced motion membuka semua elemen tertunda, menghapus transform hero, dan memakai scroll auto. Suite terakhir: 44 test, 506 assertion; satu assertion CTA lama dihapus sesuai permintaan.


## Disclosure stabil, hover dan penutup

Browser menguji dua disclosure di 1440/390/320px. Tinggi dokumen sebelum, saat terbuka dan sesudah tertutup identik: 9552/14161/15291px sesuai viewport. Ikon caption dan nomor panel tidak dirender. Penutup diperiksa melalui screenshot desktop/mobile; overflow horizontal tidak ditemukan setelah ring orbit dibatasi pada section. Suite: 44 test / 506 assertion.


## Perbaikan konflik navigasi dan hover Daily Winning

Reproduksi menunjukkan klik Filosofi ke Janji menggerakkan indikator ke target, kembali ke Filosofi, lalu melewati semua anchor perantara. Penyebabnya handler klik dan scroll-spy mengatur indikator secara bersamaan. Selama native anchor scroll, pilihan tujuan kini dipertahankan sampai scroll selesai; wheel/touch/tombol scroll membatalkannya. Gulir horizontal hanya diterbitkan saat pilihan berubah, bukan setiap frame. Scrollend horizontal tidak melepas target navigasi vertikal.

Audit browser pada 1440/390px: trace pilihan saat perpindahan hanya Janji kami, tujuan terlihat pada navigasi mobile, klik cepat berakhir di Bertumbuh, tanpa error JavaScript. Hover judul tahap memberi transform none; hover ikon memberi translate/rotate yang diharapkan. Selector hover dipindah dari li ke marker ikon.


## Filosofi sebagai teks langsung

Feedback loop: disclosure diganti paragraf terbuka dan subjudul; screenshot desktop 1440px dan mobile 390px diperiksa. Komposisi foto/teks seimbang, hierarki subjudul terbaca, tidak ada ruang cadangan atau scrollbar internal. Audit 320/390px: dua paragraf tampil langsung, body dan lead sama-sama 16px, tanpa overflow. Uji regresi memastikan section filosofi tanpa details/summary dan subjudul serta teks tampil langsung.
