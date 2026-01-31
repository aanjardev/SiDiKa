# Deploy Guide for Hostinger Business Shared Hosting

## CSS Loading Issue - SOLVED ✅

Masalah sebelumnya: CSS tidak ter-load di Hostinger karena file belum ter-copy ke `public/css`.

### Solution

Sekarang menggunakan sistem **auto-copy CSS** yang lebih sederhana:

#### 1. **Local Development**

```bash
npm run copy-css  # Copy CSS files dari resources/css ke public/css
npm run dev       # Start development server (otomatis copy CSS dulu)
```

#### 2. **Production Build**

```bash
npm run build     # Build assets (otomatis copy CSS dulu)
```

#### 3. **Deploy ke Hostinger**

Pastikan mengikuti langkah ini:

1. **Upload semua files ke Hostinger:**

    ```
    - app/
    - bootstrap/
    - config/
    - database/
    - public/ (penting! termasuk public/css/ yang sudah di-copy)
    - resources/
    - routes/
    - etc...
    ```

2. **SSH ke Hostinger dan jalankan:**

    ```bash
    cd /home/your_domain/public_html  # atau path yang sesuai

    # Install dependencies
    npm ci

    # Copy CSS files ke public/css
    npm run copy-css

    # Run PHP setup
    php artisan migrate
    php artisan storage:link
    ```

3. **Atau gunakan Auto-Deployment (jika pakai Git):**
    - Konfigurasi di Hostinger untuk menjalankan command saat deploy:
        ```bash
        npm ci && npm run copy-css && php artisan migrate
        ```

### File Structure

**Sebelum Build:**

```
resources/css/
├── app.css
├── legacy/
│   ├── katalog.css
│   ├── header.css
│   ├── mainPage.css
│   └── ... (CSS lainnya)
└── admin/
    └── ... (CSS admin)
```

**Sesudah Build / Deploy:**

```
public/css/          ← CSS ter-copy ke sini
├── app.css
├── legacy/
│   ├── katalog.css
│   ├── header.css
│   ├── mainPage.css
│   └── ... (CSS lainnya)
└── admin/
    └── ... (CSS admin)

public/build/        ← JS assets dari Vite (jika ada)
```

### Asset Helper

Semua blade files menggunakan Laravel's `asset()` helper:

```blade
{{ asset('css/katalog.css') }}  <!-- Akan point ke /public/css/katalog.css -->
{{ asset('css/header.css') }}   <!-- Akan point ke /public/css/header.css -->
```

### Troubleshooting

**Jika CSS masih tidak muncul:**

1. Cek apakah folder `public/css/` ada dan terisi
2. Verifikasi permissions: `chmod -R 755 public/css/`
3. Clear cache: `php artisan cache:clear`
4. Check browser inspector untuk verifikasi CSS path

**Quick Debug:**

```bash
# Check apakah CSS files sudah ada
ls -la public/css/

# Manual copy jika perlu
node copy-css.js
```

### Notes

- ✅ Tidak perlu menggunakan Vite untuk CSS (lebih simple untuk shared hosting)
- ✅ Hanya gunakan asset() links untuk semua CSS
- ✅ Script `copy-css.js` otomatis berjalan saat `npm run dev` dan `npm run build`
- ✅ Vite masih digunakan untuk JS, tapi CSS dimuat secara manual
