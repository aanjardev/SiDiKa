# PHP Configuration Recommendations untuk Image Upload

## 🔧 Recommended PHP.ini Settings

Edit file: `C:\xampp\php\php.ini`

### Critical Settings:

```ini
# Memory Management
memory_limit = 512M          ; Dari 128M atau kurang → 512M minimum

# Execution Time
max_execution_time = 300     ; Dari 30s → 300s (5 menit)

# Upload Limits
post_max_size = 50M          ; Untuk upload multiple files
upload_max_filesize = 50M    ; Max per file

# GD Library (untuk image processing)
extension=gd                 ; Pastikan enabled

# Optional: Imagick (lebih efficient)
extension=imagick            ; Jika tersedia (lebih recommended)
```

---

## 📋 Steps:

### 1. Locate php.ini
```
C:\xampp\php\php.ini
```

### 2. Find dan Update:
```bash
# Cari baris-baris ini (gunakan Ctrl+F):
memory_limit = 128M          → Ubah ke: memory_limit = 512M
max_execution_time = 30      → Ubah ke: max_execution_time = 300
post_max_size = 8M           → Ubah ke: post_max_size = 50M
upload_max_filesize = 2M     → Ubah ke: upload_max_filesize = 50M
```

### 3. Save dan Restart Apache
```bash
# Stop Apache
# Buka XAMPP Control Panel
# Click "Stop" untuk Apache

# Tunggu beberapa detik

# Click "Start" untuk Apache
```

### 4. Verify Configuration
```bash
# Terminal/PowerShell
php -r "echo 'Memory: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Max Exec Time: ' . ini_get('max_execution_time') . 's' . PHP_EOL;"
php -r "echo 'Upload Max: ' . ini_get('upload_max_filesize') . PHP_EOL;"
```

---

## ✅ Verifikasi di Laravel

Jalankan command untuk check:
```bash
php artisan debug:image-upload
```

Expected output harus show:
```
📦 Storage Health Check
Local (Public)   ✅ Available
```

---

## 🎯 Why These Settings?

| Setting | Value | Reason |
|---------|-------|--------|
| memory_limit | 512M | Image processing + WebP encoding bisa memakan 200-300MB |
| max_execution_time | 300 | Processing multiple large images bisa 30-60 detik |
| post_max_size | 50M | Upload 10 gambar × 5MB = 50MB |
| upload_max_filesize | 50M | Per file maksimal |

---

## 🚨 Common Issues

### Issue: "Out of memory" error
```ini
memory_limit = 512M   ; Increase this
```

### Issue: "Maximum execution time exceeded"
```ini
max_execution_time = 300   ; Increase this
```

### Issue: "No space left" error
```bash
# Check disk space
# Windows: Properties of C: drive
# Make sure you have > 1GB free space
```

---

## 📝 After Configuration

1. **Restart Apache** (CRITICAL!)
2. **Clear Laravel cache:**
   ```bash
   php artisan config:cache
   ```
3. **Test upload:**
   - Upload 5 gambar
   - Check if all appear
4. **Monitor logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

**PENTING:** Jangan lupa restart Apache setelah edit php.ini!
