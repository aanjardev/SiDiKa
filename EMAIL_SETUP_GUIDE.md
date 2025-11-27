# 📧 Email Setup Guide - SiDiKa System

## 🎯 Opsi Email Configuration

### Option 1: Hostinger SMTP (Recommended)
**Best for production use**

#### Step 1: Create Email Account di Hostinger
1. Login ke Hostinger cPanel
2. Go to **Email Accounts**
3. Create new email: `noreply@yourdomain.com`
4. Set password yang kuat
5. Note: Email ini akan digunakan untuk mengirim activation emails

#### Step 2: Get SMTP Credentials
1. Di cPanel, cari **Email Configuration** atau **SMTP Settings**
2. Note down:
   - SMTP Host: `smtp.hostinger.com`
   - SMTP Port: `465` (SSL) atau `587` (TLS)
   - Username: `noreply@yourdomain.com`
   - Password: Password email yang dibuat

#### Step 3: Update .env File
```bash
# Copy .env.example ke .env
cp .env.example .env

# Update konfigurasi email
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="SiDiKa System"
```

#### Step 4: Test Email Configuration
```bash
# Test email dari artisan console
php artisan tinker
> app(App\Services\EmailService::class)->testEmailConfig()
```

---

### Option 2: Gmail SMTP (For Testing)
**Good for development, not recommended for production**

#### Step 1: Enable 2-Factor Authentication
1. Go to Google Account settings
2. Enable 2FA

#### Step 2: Create App Password
1. Go to Google Account → Security
2. App passwords → Generate new app password
3. Select: Mail → Other (Custom name)
4. Copy generated password

#### Step 3: Update .env File
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password  # Use app password, not regular password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-gmail@gmail.com"
MAIL_FROM_NAME="SiDiKa System"
```

---

### Option 3: Local Testing (Mailpit)
**For development only**

#### Step 1: Install Mailpit
```bash
# Install Mailpit
brew install mailpit  # macOS
# atau download dari https://github.com/axllent/mailpit
```

#### Step 2: Update .env File
```bash
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="SiDiKa Test"
```

#### Step 3: Start Mailpit
```bash
mailpit
# Visit http://localhost:8025 to see emails
```

---

## 🚀 Quick Setup Commands

### 1. Choose Your Option
```bash
# Option 1: Hostinger (Recommended)
# Update .env dengan Hostinger credentials

# Option 2: Gmail (Testing)
# Update .env dengan Gmail credentials

# Option 3: Local (Development)
# Update .env dengan Mailpit settings
```

### 2. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Test Email
```bash
# Test email service
php artisan tinker
> app(App\Services\EmailService::class)->testEmailConfig()
```

### 4. Test User Creation
1. Buat user baru dari admin panel
2. Check email masuk ke inbox user
3. Click activation link di email

---

## 🔧 Troubleshooting

### Common Issues:

#### 1. "Connection refused"
- Check SMTP host dan port
- Verify firewall settings
- Test with telnet: `telnet smtp.hostinger.com 465`

#### 2. "Authentication failed"
- Verify email dan password
- For Gmail: use App Password, not regular password
- Check 2FA settings

#### 3. "Email not received"
- Check spam/junk folder
- Verify FROM address matches email account
- Test with different email provider

#### 4. SSL/TLS errors
- Try different encryption (ssl/tls)
- Check port numbers
- Verify SSL certificates

### Debug Mode:
```bash
# Enable mail logging
MAIL_MAILER=log

# Check logs
tail -f storage/logs/laravel.log
```

---

## 📱 Email Templates

System menggunakan template HTML yang responsive:

- **Activation Email**: `resources/views/emails/activation.blade.php`
- **Password Reset**: `resources/views/emails/password-reset.blade.php` (coming soon)

Customize templates sesuai kebutuhan branding Anda.

---

## 🎯 Production Checklist

- [ ] Create professional email account (`noreply@yourdomain.com`)
- [ ] Configure SMTP with SSL/TLS
- [ ] Test email delivery
- [ ] Check spam score
- [ ] Set up email monitoring
- [ ] Configure bounce handling
- [ ] Test with different email providers

---

## 📞 Support

Jika mengalami masalah dengan Hostinger SMTP:
1. Contact Hostinger support
2. Check cPanel error logs
3. Verify email account status
4. Test SMTP connection manually

---

**🎉 Selamat! Email verification system SiDiKa sudah siap digunakan!**
