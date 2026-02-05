# ✅ Testing Checklist - Image Upload Fix

Sebelum declare fixed, lakukan testing berikut:

---

## 🧪 **Test Case 1: Single Image Upload**

**Prosedur:**
1. Buka halaman edit/tambah produk
2. Upload 1 gambar (< 500KB)
3. Refresh halaman
4. Cek database → gambar harus ada di `gambar_produk` table

**Expected Result:** ✅ Gambar muncul di halaman

**Actual Result:** _______________

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 2: Multiple Images Upload (5 gambar)**

**Prosedur:**
1. Pilih 5 gambar sekaligus (upload ke form)
2. Klik Upload/Simpan
3. Tunggu sampai redirect (atau refresh manual)
4. Lihat halaman - cek berapa gambar yang muncul
5. Buka `storage/logs/laravel.log` - cek ada error atau tidak

**Expected Result:** ✅ Semua 5 gambar muncul

**Actual Result:** _______________

**Log Errors:** (jika ada)
```
[paste log errors here]
```

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 3: Same Image Upload 3x (Duplicate Testing)**

**Prosedur:**
1. Buka photo file yang sama di file explorer
2. Upload file tersebut 3x ke product image uploader
3. Refresh halaman
4. Cek berapa banyak record di database

**Expected Result:** ✅ 3 gambar muncul (atau 1 jika dedup)

**Actual Result:** _______________

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 4: Network Simulation (R2 Offline)**

**Prosedur:**
1. Disconnect internet (atau buka DevTools Network tab → Offline)
2. Upload gambar
3. Tunggu error / timeout
4. Check log file

**Expected Result:** ✅ Error jelas di log, fallback ke local storage

**Actual Result:** _______________

**Log Output:**
```
[paste relevant log lines]
```

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 5: Large Images (> 5000px)**

**Prosedur:**
1. Buka AI image generator atau cari large image (10000x10000 px)
2. Try upload
3. Check error message

**Expected Result:** ✅ Error message: "Resolusi terlalu besar"

**Actual Result:** _______________

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 6: Database Integrity**

**Prosedur:**
1. Upload 5 gambar
2. Run SQL query di database:
   ```sql
   SELECT COUNT(*) FROM gambar_produk WHERE id_produk = [PRODUCT_ID];
   SELECT path_gambar FROM gambar_produk WHERE id_produk = [PRODUCT_ID];
   ```
3. Cek:
   - Path tidak ada yang NULL/empty
   - Path format valid (product/X/HASH.webp)
   - Jumlah sesuai upload count

**Expected Result:** ✅ Semua path valid, tidak ada NULL

**Actual Result:**
```sql
[paste query result]
```

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 7: Main Image Selection**

**Prosedur:**
1. Upload 3 gambar
2. Pilih gambar #2 sebagai main
3. Simpan
4. Refresh halaman
5. Check database

**Expected Result:** ✅ Gambar #2 punya `is_main = 1`, lainnya `is_main = 0`

**Actual Result:**
```sql
[paste query result]
```

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 8: Image Deletion**

**Prosedur:**
1. Upload 5 gambar
2. Delete gambar #1 dan #3
3. Refresh halaman
4. Check database

**Expected Result:** ✅ Hanya 3 gambar tersisa, #1 dan #3 terhapus

**Actual Result:**
```sql
SELECT COUNT(*) FROM gambar_produk WHERE id_produk = [PRODUCT_ID];
```

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 9: Edit Product dengan Existing Images**

**Prosedur:**
1. Edit produk yang sudah ada dengan existing gambar
2. Upload 2 gambar tambahan
3. Refresh
4. Check total gambar di halaman

**Expected Result:** ✅ Existing + New = Total gambar semuanya muncul

**Actual Result:** _______________

**Status:** [ ] PASS  [ ] FAIL

---

## 🧪 **Test Case 10: Error Message Clarity**

**Prosedur:**
1. Try upload file bukan gambar (txt, pdf, dll)
2. Try upload gambar > 10MB
3. Try upload dengan corrupt file
4. Check error messages di UI

**Expected Result:** ✅ Error message jelas dan helpful

**Messages:**
- Invalid file: _______________
- Too large: _______________
- Corrupt: _______________

**Status:** [ ] PASS  [ ] FAIL

---

## 📊 **Summary**

| # | Test Case | Status | Notes |
|---|-----------|--------|-------|
| 1 | Single Image | [ ] | |
| 2 | Multiple Images (5) | [ ] | |
| 3 | Duplicate Upload | [ ] | |
| 4 | Network Offline | [ ] | |
| 5 | Large Images | [ ] | |
| 6 | Database Integrity | [ ] | |
| 7 | Main Image Selection | [ ] | |
| 8 | Image Deletion | [ ] | |
| 9 | Edit with Existing | [ ] | |
| 10 | Error Messages | [ ] | |

**Overall Status:** 
- [ ] ALL PASS ✅ → Ready for production
- [ ] Some failures → Need investigation

---

## 🐛 **If Any Test Fails**

1. **Check logs:**
   ```bash
   tail -100 storage/logs/laravel.log | grep -i error
   ```

2. **Check storage:**
   ```bash
   ls -la storage/app/public/product/
   ```

3. **Check R2 availability:**
   ```php
   // Run di tinker
   php artisan tinker
   >>> \App\Helpers\StorageHelper::getStorageHealth()
   ```

4. **Clear cache:**
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

---

**Tested By:** __________________
**Date:** __________________
**Overall Status:** [ ] PASS  [ ] FAIL

---

Setelah semua test pass, feature **FULLY PRODUCTION READY** ✅
