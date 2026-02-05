# 🚨 URGENT FIX - Memory & Timeout Issue

Masalah sebenarnya adalah **memory limit terlalu kecil** dan **timeout terlalu pendek**.

---

## ⚡ Quick Fix (3 Steps):

### **Step 1: Edit PHP.ini**

Buka file: `C:\xampp\php\php.ini`

Cari baris ini (gunakan Ctrl+F) dan ubah:

```ini
memory_limit = 128M          → ubah ke: memory_limit = 512M
max_execution_time = 30      → ubah ke: max_execution_time = 300
post_max_size = 8M           → ubah ke: post_max_size = 50M
upload_max_filesize = 2M     → ubah ke: upload_max_filesize = 50M
```

**SAVE FILE!**

### **Step 2: Restart Apache**

1. Buka XAMPP Control Panel
2. Klik **Stop** (Apache)
3. Tunggu beberapa detik
4. Klik **Start** (Apache)

### **Step 3: Clear Cache & Test**

```bash
# Terminal/PowerShell
php artisan config:cache

# Test upload 5 gambar
```

---

## ✅ Expected Result:
- Upload 5 gambar → **Semua 5 muncul** ✅
- Tidak ada error timeout
- Tidak ada "Out of memory"

---

## 🔍 If Still Error

Check log:
```bash
# Terminal
tail -f storage/logs/laravel.log | grep -i error
```

Verify settings:
```bash
# Terminal
php -r "echo 'Memory: ' . ini_get('memory_limit') . PHP_EOL; echo 'Timeout: ' . ini_get('max_execution_time') . 's' . PHP_EOL;"
```

---

**MOST IMPORTANT: Jangan lupa restart Apache setelah edit php.ini!**
