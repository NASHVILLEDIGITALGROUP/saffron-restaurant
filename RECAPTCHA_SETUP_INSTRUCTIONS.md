# reCAPTCHA Security Setup Instructions

**Date:** January 2025  
**Purpose:** Secure reCAPTCHA implementation with environment-based configuration

---

## ✅ IMPLEMENTATION COMPLETE

The reCAPTCHA secret key has been moved from hardcoded value to secure environment-based configuration.

---

## 📋 FILES CREATED/MODIFIED

### New Files Created:
1. **`.env.example`** - Template file with placeholder values
2. **`config.php`** - Secure configuration loader
3. **`RECAPTCHA_SETUP_INSTRUCTIONS.md`** - This file

### Files Modified:
1. **`mail_send.php`** - Updated to use secure configuration
2. **`.gitignore`** - Already includes `.env` (no changes needed)

---

## 🚀 SETUP INSTRUCTIONS

### Step 1: Create .env File

1. Copy the example file:
   ```bash
   cp .env.example .env
   ```

2. Open `.env` file and add your actual reCAPTCHA keys:
   ```env
   RECAPTCHA_SECRET_KEY=6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln
   RECAPTCHA_SITE_KEY=6Le0uBwbAAAAAALEpkDRY_zc5eYl8MSw5b8m0Q58
   ```

   **Important:** Replace with your actual keys from Google reCAPTCHA admin panel.

### Step 2: Verify .gitignore

Ensure `.gitignore` includes `.env` (it already does):
```gitignore
.env
.env.local
.env.*.local
```

### Step 3: Set File Permissions

**On Linux/Mac:**
```bash
chmod 600 .env          # Read/write for owner only
chmod 644 config.php    # Read for all, write for owner
```

**On Windows:**
- Right-click `.env` → Properties → Security
- Remove all users except Administrator/Your User
- Set permissions to Read/Write for owner only

### Step 4: Test the Configuration

1. **Test form submission:**
   - Go to contact page
   - Fill out the form
   - Complete reCAPTCHA
   - Submit form
   - Verify email is sent/received

2. **Check for errors:**
   - Check PHP error logs
   - Verify no configuration errors appear
   - Test with missing .env file (should show user-friendly error)

---

## 🔒 SECURITY IMPROVEMENTS IMPLEMENTED

### ✅ Before (Insecure):
```php
// Hardcoded secret key - EXPOSED IN SOURCE CODE
$secret = "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln";
```

### ✅ After (Secure):
```php
// Loaded from environment variable - NOT IN SOURCE CODE
$secret = getRecaptchaSecret();
```

### Security Features Added:

1. **Environment-Based Configuration**
   - Secret key stored in `.env` file (not in code)
   - `.env` file excluded from version control
   - Template file (`.env.example`) provided for reference

2. **Secure Configuration Loader**
   - `config.php` handles environment variable loading
   - Validates configuration on load
   - Provides error handling without exposing secrets

3. **Improved Error Handling**
   - Configuration errors logged to error log (not displayed to users)
   - User-friendly error messages
   - No sensitive information in error messages

4. **Input Sanitization Enhanced**
   - Changed from `strip_tags()` to `htmlspecialchars()`
   - Added `filter_var()` for email validation
   - Proper UTF-8 encoding

5. **Log File Security**
   - Attempts to write to `logs/` directory (outside web root)
   - Falls back gracefully if directory not writable
   - Proper file permissions recommended

---

## 📝 CODE CHANGES SUMMARY

### mail_send.php Changes:

#### 1. Added Configuration Loading:
```php
// Load configuration
require_once __DIR__ . '/config.php';

// Ensure config is loaded
if (!defined('SAFFRON_CONFIG_LOADED')) {
    die('Configuration error. Please ensure config.php is properly configured.');
}
```

#### 2. Replaced Hardcoded Secret:
```php
// OLD (INSECURE):
$secret = "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln";

// NEW (SECURE):
$secret = getRecaptchaSecret();
```

#### 3. Improved Input Sanitization:
```php
// OLD:
'name' => strip_tags($_POST['name']),

// NEW:
'name' => htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8'),
```

#### 4. Enhanced Error Handling:
```php
// Logs detailed errors to error log (not visible to users)
error_log("reCAPTCHA Error: " . implode(', ', $response->errorCodes));
```

#### 5. Improved Log File Location:
```php
// Attempts to use logs/ directory (more secure)
$logFile = $logDir . '/contact_submissions.log';
```

---

## 🔍 VERIFICATION CHECKLIST

After setup, verify:

- [ ] `.env` file exists and contains actual keys
- [ ] `.env` file is NOT committed to Git (check `.gitignore`)
- [ ] `.env` file has proper permissions (600 on Linux/Mac)
- [ ] Contact form submission works
- [ ] reCAPTCHA verification works
- [ ] No secret keys visible in source code
- [ ] Error messages don't expose configuration details
- [ ] Log file is created in `logs/` directory (if writable)

---

## 🚨 TROUBLESHOOTING

### Issue: "reCAPTCHA configuration error"

**Cause:** `.env` file missing or `RECAPTCHA_SECRET_KEY` not set

**Solution:**
1. Ensure `.env` file exists in root directory
2. Verify `RECAPTCHA_SECRET_KEY` is set in `.env`
3. Check file permissions on `.env`

### Issue: Form submission fails silently

**Cause:** Configuration error or missing .env file

**Solution:**
1. Check PHP error logs
2. Verify `config.php` is loading correctly
3. Test with `var_dump(getRecaptchaSecret())` (remove after testing)

### Issue: Log file not created

**Cause:** `logs/` directory doesn't exist or not writable

**Solution:**
1. Create `logs/` directory manually:
   ```bash
   mkdir logs
   chmod 750 logs
   ```
2. Or ensure web root is writable (less secure)

---

## 📦 DEPLOYMENT CHECKLIST

### Before Deploying to Production:

1. **Create .env file on server:**
   ```bash
   # On production server
   cp .env.example .env
   nano .env  # Edit with production keys
   ```

2. **Set proper permissions:**
   ```bash
   chmod 600 .env
   chmod 644 config.php
   chmod 750 logs/  # If using logs directory
   ```

3. **Verify .env is excluded:**
   ```bash
   git check-ignore .env  # Should return .env
   ```

4. **Test form submission:**
   - Submit test form
   - Verify email received
   - Check error logs

5. **Update .htaccess (if needed):**
   ```apache
   # Prevent .env file access
   <Files ".env">
       Require all denied
   </Files>
   ```

---

## 🔐 SECURITY BEST PRACTICES

### ✅ DO:
- Keep `.env` file outside version control
- Use strong file permissions (600) on `.env`
- Rotate reCAPTCHA keys periodically
- Monitor error logs for configuration issues
- Use different keys for development and production

### ❌ DON'T:
- Commit `.env` file to Git
- Share `.env` file via email or chat
- Use same keys for multiple projects
- Expose secret keys in error messages
- Store keys in client-side code

---

## 📞 SUPPORT

### Getting Your reCAPTCHA Keys:

1. Go to: https://www.google.com/recaptcha/admin/create
2. Register your site
3. Get Site Key (public) and Secret Key (private)
4. Add to `.env` file

### Testing reCAPTCHA:

- **Test Site Key:** `6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI`
- **Test Secret Key:** `6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe`
- Use these for testing only, not production!

---

## ✅ IMPLEMENTATION STATUS

- [x] Created `.env.example` template
- [x] Created `config.php` loader
- [x] Updated `mail_send.php` to use secure config
- [x] Enhanced input sanitization
- [x] Improved error handling
- [x] Updated log file location
- [x] Added security documentation
- [x] Verified `.gitignore` includes `.env`

**Status:** ✅ **IMPLEMENTATION COMPLETE**

**Next Step:** Create `.env` file with your actual keys and test!

---

*For questions or issues, refer to the troubleshooting section above.*

