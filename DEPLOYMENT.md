# SiDiKa Deployment Guide ke Hostinger

## 🚀 Langkah-langkah Deployment:

### 1. Upload Files
- Upload semua file kecuali:
  - `node_modules/`
  - `.git/`
  - `storage/logs/` (kosongkan)
  - `bootstrap/cache/` (kosongkan)

### 2. Set Permissions
```bash
# Di Hostinger File Manager atau FTP
chmod 755 public/
chmod 755 storage/
chmod 755 bootstrap/cache/
chmod 644 .env
```

### 3. Setup Environment
Buat file `.env` di root directory:
```bash
APP_NAME=SiDiKa
APP_ENV=production
APP_KEY=base64:your_app_key_here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

QUEUE_CONNECTION=database
MAX_EXECUTION_TIME=300

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Run Commands
Di Hostinger Terminal atau SSH:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan storage:link
php artisan migrate --force
php artisan key:generate --force
```

### 5. Setup Queue Worker (Optional)
```bash
# Untuk background processing
php artisan queue:work --daemon
```

## 🛡️ Error Handling Configuration:

### ✅ Yang Sudah Otomatis:
- 404 → `resources/views/errors/404.blade.php`
- 403 → `resources/views/errors/403.blade.php`
- 500 → `resources/views/errors/500.blade.php`
- 503 → `resources/views/errors/503.blade.php`
- 429 → `resources/views/errors/429.blade.php`
- Timeout → `resources/views/errors/timeout.blade.php`

### 🔧 PHP Configuration (di Hostinger):
```ini
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
```

## 📋 Testing di Production:

### 1. Test Error Pages:
```bash
https://yourdomain.com/test-404
https://yourdomain.com/test-403
https://yourdomain.com/test-500
https://yourdomain.com/test-timeout
```

### 2. Remove Test Routes (Production):
Hapus atau comment test routes di `routes/web.php`:
```php
// Comment atau hapus ini di production
// Route::get('/test-404', ...);
// Route::get('/test-500', ...);
// dll.
```

## 🚨 Troubleshooting:

### Error 500 saat loading:
1. Check `.env` configuration
2. Check file permissions
3. Clear cache: `php artisan cache:clear`
4. Check logs: `storage/logs/laravel.log`

### Error pages tidak muncul:
1. Pastikan `APP_DEBUG=false`
2. Check `app/Exceptions/Handler.php`
3. Clear view cache: `php artisan view:clear`

## ✅ Verifikasi:
- [ ] Error pages tampil dengan benar
- [ ] Auto refresh berfungsi
- [ ] Redirect login berfungsi (403)
- [ ] Queue worker berjalan (jika digunakan)
