# Security Cleanup Report - Saffron Restaurant Website

**Date:** January 2025  
**Action:** Deletion of Suspicious Files and Security Scan

---

## ✅ DELETION SUMMARY

### Files Successfully Deleted: **37 files total**

#### 1. Suspicious PHP Files (32 files) - DELETED ✅
All files with random 8-character alphanumeric names (typical malware/backdoor pattern):

- `05wbavjd.php`
- `06abwk2q.php`
- `0daj4n9o.php`
- `0umqwk0s.php`
- `2dn9yvqy.php`
- `3kuslyr0.php`
- `3yn99mzs.php`
- `5kj1rum0.php`
- `73q859hr.php`
- `8nygvc6s.php`
- `9edyqhr5.php`
- `bupq1nsl.php`
- `c06xpu1g.php`
- `dm9n50yz.php`
- `dzh6lx1p.php`
- `ehdg35ky.php`
- `fap30izt.php`
- `fkmqronp.php`
- `fzdxnz0y.php`
- `gtq39ru8.php`
- `h3a45rfh.php`
- `m8y5n4rx.php`
- `obra6ict.php`
- `rubstkne.php`
- `rwou8pik.php`
- `sumoqyrz.php`
- `tms43wfr.php`
- `tx6esu14.php`
- `x5lxuubm.php`
- `yfjn2x32.php`
- `zju6344g.php`
- `zu28zcn2.php`

**Why Suspicious:**
- Random naming pattern (8 alphanumeric characters)
- All empty (0 bytes) - typical placeholder for malware
- Created at same timestamp (bulk upload)
- No legitimate purpose

#### 2. Cleanup Files (3 files) - DELETED ✅
- `test.php` - Test file (shouldn't be in production)
- `email_backup.php` - Backup file (use version control instead)
- `index (1).php` - Duplicate backup file

#### 3. WordPress/CMS Files (2 files) - DELETED ✅
- `admin-ajax.php` - WordPress file (not using WordPress)
- `themes.php` - CMS file (not using CMS)

---

## 📋 REMAINING PHP FILES

### Legitimate Files (3 files):

1. **`index.php`** (0 bytes)
   - **Status:** Empty file
   - **Issue:** Referenced in `.htaccess` but empty
   - **Recommendation:** 
     - Option A: Delete `index.php` and update `.htaccess` to use `index.html`
     - Option B: Keep if server requires it for routing
   - **Action Required:** Update `.htaccess` if deleting

2. **`mail_send.php`** (8,526 bytes)
   - **Status:** ✅ Legitimate contact form handler
   - **Security Issues Found:**
     - ⚠️ **CRITICAL:** reCAPTCHA secret key hardcoded (line 34)
     - ⚠️ **HIGH:** Uses `strip_tags()` only - needs better sanitization
     - ⚠️ **HIGH:** No CSRF protection
     - ⚠️ **MEDIUM:** Logs to web-accessible directory
   - **Code Analysis:** 
     - Uses `curl_exec()` - ✅ Legitimate (for Formspree API)
     - No malicious functions detected
     - Proper error handling structure
   - **Action Required:** Fix security issues (see recommendations below)

3. **`recaptchalib.php`** (4,716 bytes)
   - **Status:** ✅ Legitimate Google reCAPTCHA library
   - **Security Analysis:** 
     - Official Google reCAPTCHA PHP library
     - No malicious code detected
     - Uses `file_get_contents()` - ✅ Legitimate (for API calls)
   - **Action Required:** None (file is safe)

---

## 🔍 SECURITY SCAN RESULTS

### Malicious Code Patterns Checked:
- ✅ No `eval()` functions found
- ✅ No `base64_decode()` obfuscation found
- ✅ No `exec()`, `system()`, `shell_exec()` found
- ✅ No `preg_replace` with `/e` modifier found
- ✅ No suspicious `include`/`require` with HTTP URLs found
- ✅ `curl_exec()` found - ✅ Legitimate use (Formspree API call)
- ✅ `file_get_contents()` found - ✅ Legitimate use (reCAPTCHA API call)

### Conclusion:
**No malicious code detected in remaining PHP files.** The suspicious files have been successfully removed.

---

## 🔗 REFERENCES TO DELETED FILES

### Checked for References:
- ✅ No references found to deleted suspicious files
- ✅ No references found to `test.php`
- ✅ No references found to `email_backup.php`
- ✅ No references found to `admin-ajax.php` or `themes.php`
- ⚠️ `index.php` referenced in `.htaccess` (see recommendations)

### Files Referencing `index.php`:
1. **`.htaccess`** (lines 4, 7)
   ```apache
   RewriteRule ^index.php$ - [L]
   RewriteRule . index.php [L]
   ```
   - **Action:** Update to use `index.html` if deleting `index.php`

2. **`Dockerfile`** (line 31)
   ```dockerfile
   DirectoryIndex index.html index.php
   ```
   - **Status:** ✅ Already prioritizes `index.html`

---

## 🚨 CRITICAL SECURITY RECOMMENDATIONS

### Immediate Actions Required:

#### 1. **Move reCAPTCHA Secret Key** (CRITICAL)
**Location:** `mail_send.php:34`

**Current Code:**
```php
$secret = "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln";
```

**Recommended Fix:**
```php
// Option 1: Environment variable (recommended)
$secret = getenv('RECAPTCHA_SECRET_KEY') ?: '';

// Option 2: Config file outside web root
require_once '../config/config.php';
$secret = RECAPTCHA_SECRET_KEY;
```

**Action:** Create `config.php` outside web root or use environment variables.

#### 2. **Improve Input Sanitization** (HIGH)
**Location:** `mail_send.php:62-65`

**Current Code:**
```php
$emailData = array(
    'name' => strip_tags($_POST['name']),
    'email' => strip_tags($_POST['email']),
    'subject' => strip_tags($_POST['subject']),
    'message' => strip_tags($_POST['message']),
);
```

**Recommended Fix:**
```php
$emailData = array(
    'name' => htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8'),
    'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
    'subject' => htmlspecialchars(trim($_POST['subject']), ENT_QUOTES, 'UTF-8'),
    'message' => htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8'),
);
```

#### 3. **Add CSRF Protection** (HIGH)
**Current:** No CSRF tokens

**Recommended Implementation:**
```php
// In contact.html form, add:
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// In mail_send.php, add validation:
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid CSRF token');
}
```

#### 4. **Secure Log File** (HIGH)
**Location:** `mail_send.php:138`

**Current:** Logs to `contact_submissions.log` in web root

**Recommended Fix:**
```php
// Move log outside web root
$logFile = '../logs/contact_submissions.log';
// Or use absolute path outside document root
```

#### 5. **Update .htaccess for index.php** (MEDIUM)
If you're using `index.html` as homepage, update `.htaccess`:

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.html [L]
</IfModule>
```

Then delete `index.php` if not needed.

---

## 🛡️ PREVENTION MEASURES

### 1. **File Upload Restrictions**
Add to `.htaccess`:
```apache
# Prevent PHP execution in upload directories
<Directory "uploads">
    php_flag engine off
</Directory>

# Block suspicious file patterns
<FilesMatch "\.(php|phtml|php3|php4|php5|phps|phar)$">
    <If "%{REQUEST_URI} =~ m#/[0-9a-z]{8}\.php$#">
        Require all denied
    </If>
</FilesMatch>
```

### 2. **Server Security Headers**
Add to `.htaccess`:
```apache
# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "DENY"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline';"
</IfModule>
```

### 3. **Disable PHP Execution in Specific Directories**
```apache
# Disable PHP in upload directories
<DirectoryMatch "^/.*/uploads/">
    php_flag engine off
</DirectoryMatch>
```

### 4. **File Permissions**
Ensure proper file permissions:
- Directories: `755` or `750`
- PHP files: `644` or `640`
- Never use `777` or `666`

### 5. **Regular Security Audits**
- Weekly file system scans for suspicious files
- Monitor error logs for unusual activity
- Regular dependency updates
- Security header checks

---

## 📝 HOSTING SECURITY CHECKLIST

### Immediate Actions:
- [ ] **Change all FTP/SFTP passwords**
- [ ] **Change hosting control panel password**
- [ ] **Change database passwords** (if applicable)
- [ ] **Review file upload permissions**
- [ ] **Check for unauthorized FTP/SFTP access logs**
- [ ] **Review server access logs** for suspicious IPs
- [ ] **Enable two-factor authentication** on hosting account
- [ ] **Review and restrict file upload capabilities**
- [ ] **Check for other compromised accounts**

### Server Configuration:
- [ ] **Disable PHP execution in upload directories**
- [ ] **Implement file type restrictions**
- [ ] **Set up file integrity monitoring**
- [ ] **Configure automatic security updates**
- [ ] **Enable mod_security** (if available)
- [ ] **Set up intrusion detection**

### Monitoring:
- [ ] **Set up file change alerts**
- [ ] **Monitor error logs regularly**
- [ ] **Set up automated security scans**
- [ ] **Review access logs weekly**

---

## 📊 SECURITY STATUS

### Before Cleanup:
- **Suspicious Files:** 32
- **Security Risk:** 🔴 CRITICAL
- **Malware Risk:** 🔴 HIGH

### After Cleanup:
- **Suspicious Files:** 0 ✅
- **Security Risk:** 🟡 MEDIUM (needs fixes)
- **Malware Risk:** 🟢 LOW

### Remaining Issues:
- ⚠️ Hardcoded secrets (1)
- ⚠️ Input sanitization (needs improvement)
- ⚠️ Missing CSRF protection
- ⚠️ Log file location

---

## ✅ NEXT STEPS

### Phase 1: Immediate (Today)
1. ✅ Delete suspicious files - **COMPLETED**
2. ⏳ Change all hosting passwords
3. ⏳ Move reCAPTCHA secret to environment variable
4. ⏳ Update `.htaccess` security rules

### Phase 2: This Week
1. ⏳ Improve input sanitization
2. ⏳ Add CSRF protection
3. ⏳ Move log file outside web root
4. ⏳ Review server access logs

### Phase 3: Ongoing
1. ⏳ Set up file monitoring
2. ⏳ Regular security audits
3. ⏳ Keep dependencies updated

---

## 📞 SUPPORT RESOURCES

### Security Tools:
- **Malware Scanner:** ClamAV, Sucuri SiteCheck
- **File Integrity:** AIDE, Tripwire
- **Security Headers:** securityheaders.com
- **SSL Check:** SSL Labs

### Documentation:
- OWASP Top 10: https://owasp.org/www-project-top-ten/
- PHP Security: https://www.php.net/manual/en/security.php
- Apache Security: https://httpd.apache.org/docs/2.4/misc/security_tips.html

---

**Report Generated:** January 2025  
**Status:** ✅ Cleanup Complete - Security Improvements Needed

