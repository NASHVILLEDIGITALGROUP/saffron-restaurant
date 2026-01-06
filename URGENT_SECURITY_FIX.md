# 🚨 URGENT: Website Security Breach & Frame Error Fix

## CRITICAL ISSUE IDENTIFIED

Your website has been **compromised with malware** that is causing the frame error. Malicious PHP files have been injected into your server.

---

## 🔴 Root Cause: Malware Infection

### Malicious Files Found:
1. **`js/RBw3WC9wE7/index.php`** - Redirects to malicious site (action01.biz)
2. **`css/RBw3WC9wE7/index.php`** - Same malicious redirect
3. **`img/RBw3WC9wE7/index.php`** - Same malicious redirect
4. **`img/RBw3WC9wE7/fc44974e.php`** - Additional malware file
5. **`js/2B1sdvjTnX/index.php`** - Suspicious file
6. **`js/2B1sdvjTnX/6b0ade7e.php`** - Suspicious file
7. **`img/2B1sdvjTnX/qoscinmd.php`** - Suspicious file
8. **`img/2B1sdvjTnX/index.php`** - Suspicious file
9. **`fonts/2B1sdvjTnX/index.php`** - Suspicious file
10. **`css/2B1sdvjTnX/index.php`** - Suspicious file

### What These Files Do:
- **Redirect visitors** to malicious websites (action01.biz)
- **Create iframe injections** that cause frame errors
- **Potentially steal data** or install additional malware
- **Damage your SEO** and website reputation

---

## 📋 Step-by-Step Fix Instructions

### STEP 1: Immediate File Cleanup (DO THIS FIRST)

Delete all malicious files immediately:

```bash
# Delete RBw3WC9wE7 directory and all contents
rm -rf js/RBw3WC9wE7/
rm -rf css/RBw3WC9wE7/
rm -rf img/RBw3WC9wE7/

# Delete 2B1sdvjTnX directory and all contents
rm -rf js/2B1sdvjTnX/
rm -rf css/2B1sdvjTnX/
rm -rf img/2B1sdvjTnX/
rm -rf fonts/2B1sdvjTnX/
```

**OR manually delete these directories via FTP/cPanel:**
- `js/RBw3WC9wE7/`
- `css/RBw3WC9wE7/`
- `img/RBw3WC9wE7/`
- `js/2B1sdvjTnX/`
- `css/2B1sdvjTnX/`
- `img/2B1sdvjTnX/`
- `fonts/2B1sdvjTnX/`

### STEP 2: Secure .htaccess File

Add security rules to prevent future infections. Update your `htaccess` file:

```apache
# ============================================
# SECURITY HEADERS - PREVENT MALWARE
# ============================================

# Prevent PHP execution in suspicious directories
<DirectoryMatch "^/.*/(RBw3WC9wE7|2B1sdvjTnX|Qp4XeVAvjj)/">
    Require all denied
</DirectoryMatch>

# Block access to suspicious PHP files
<FilesMatch "\.(php|phtml|php3|php4|php5|phps|phar)$">
    <If "%{REQUEST_URI} =~ m#/[0-9a-z]{8}\.php$#">
        Require all denied
    </If>
    <If "%{REQUEST_URI} =~ m#/[A-Za-z0-9]{16}/#">
        Require all denied
    </If>
</FilesMatch>

# Disable PHP execution in specific directories
<DirectoryMatch "^/.*/(js|css|fonts|img)/">
    <FilesMatch "\.php$">
        Require all denied
    </FilesMatch>
</DirectoryMatch>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://cdn.gravitec.net; style-src 'self' 'unsafe-inline'; frame-ancestors 'self';"
</IfModule>

# Prevent directory browsing
Options -Indexes

# Block suspicious user agents
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP_USER_AGENT} (libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (<|>|'|%0A|%0D|%27|%3C|%3E|%00) [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} (;|<|>|'|"|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader) [NC]
    RewriteRule .* - [F,L]
</IfModule>

# ============================================
# EXISTING RULES (Keep these)
# ============================================

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.html [L]
</IfModule>

RewriteEngine On
RewriteCond %{HTTP_HOST} saffrontheindiankitchen\.com [NC]
RewriteCond %{SERVER_PORT} 80
RewriteRule ^(.*)$ https://saffrontheindiankitchen.com/$1 [R,L]
```

### STEP 3: Fix Gravitec CDN Issue

The Gravitec script may be failing. Update `index.html` line 326:

**Current (may be failing):**
```html
<script src="https://cdn.gravitec.net/storage/fd6e76d98f0cdcee5c6836c4ff9891b2/client.js" async></script>
```

**Option 1: Remove if not critical:**
```html
<!-- Gravitec push notifications - temporarily disabled -->
<!-- <script src="https://cdn.gravitec.net/storage/fd6e76d98f0cdcee5c6836c4ff9891b2/client.js" async></script> -->
```

**Option 2: Add error handling:**
```html
<script>
(function() {
    var script = document.createElement('script');
    script.src = 'https://cdn.gravitec.net/storage/fd6e76d98f0cdcee5c6836c4ff9891b2/client.js';
    script.async = true;
    script.onerror = function() {
        console.warn('Gravitec script failed to load');
    };
    document.head.appendChild(script);
})();
</script>
```

### STEP 4: Check for Additional Malware

Search for other suspicious files:

```bash
# Find all PHP files in asset directories
find js/ css/ img/ fonts/ -name "*.php" -type f

# Find files with suspicious names (random strings)
find . -type d -name "[A-Za-z0-9]\{16\}"
```

### STEP 5: Verify File Permissions

Set proper permissions:

```bash
# Directories should be 755
find . -type d -exec chmod 755 {} \;

# Files should be 644
find . -type f -exec chmod 644 {} \;

# PHP files should be 600 (more restrictive)
find . -name "*.php" -type f -exec chmod 600 {} \;

# Except mail_send.php and config.php which need execution
chmod 644 mail_send.php config.php recaptchalib.php
```

### STEP 6: Check Server Logs

Review your server error logs for:
- Unusual file access patterns
- Failed login attempts
- Suspicious PHP execution
- 404 errors for malicious files

### STEP 7: Change All Passwords

**CRITICAL:** Change all passwords immediately:
- FTP/SFTP credentials
- cPanel/hosting account password
- Database passwords (if applicable)
- Admin panel passwords (if any)

### STEP 8: Scan for Backdoors

Check for backdoor files:

```bash
# Common backdoor patterns
grep -r "eval(" *.php
grep -r "base64_decode" *.php
grep -r "exec(" *.php
grep -r "system(" *.php
grep -r "shell_exec" *.php
grep -r "passthru" *.php
```

---

## 🔒 Additional Security Measures

### 1. Install Security Plugin (if using WordPress/CMS)
- Wordfence
- Sucuri Security
- iThemes Security

### 2. Enable Server-Level Security
- **ModSecurity** (if available)
- **Fail2Ban** for brute force protection
- **Cloudflare** for DDoS protection

### 3. Regular Backups
- Set up automated daily backups
- Store backups off-server
- Test backup restoration regularly

### 4. File Integrity Monitoring
- Use tools like Tripwire or AIDE
- Monitor for unauthorized file changes

### 5. Update All Software
- PHP version (if possible, upgrade to 8.1+)
- All plugins/libraries
- Server software

---

## 🧪 Testing After Fix

1. **Clear browser cache** completely
2. **Test homepage**: https://www.saffrontheindiankitchen.com
3. **Test contact page**: https://www.saffrontheindiankitchen.com/contact.html
4. **Check browser console** for errors (F12 → Console)
5. **Check network tab** for failed requests
6. **Test on mobile device**
7. **Use online tools**:
   - Google PageSpeed Insights
   - GTmetrix
   - Sucuri SiteCheck

---

## 📞 If Issues Persist

If the frame error continues after cleanup:

1. **Check server error logs** (usually in cPanel → Error Logs)
2. **Contact your hosting provider** - they may need to:
   - Scan the server for malware
   - Check for compromised user accounts
   - Review server-level security
3. **Check DNS settings** - ensure they point correctly
4. **Verify SSL certificate** is valid and not expired

---

## ⚠️ Prevention Checklist

- [ ] All malicious files deleted
- [ ] .htaccess security rules added
- [ ] File permissions set correctly
- [ ] All passwords changed
- [ ] Server logs reviewed
- [ ] Backups configured
- [ ] Security monitoring enabled
- [ ] Website tested and working

---

## 🆘 Emergency Contact

If you need immediate assistance:
1. Contact your hosting provider's support
2. Consider hiring a security professional
3. Use malware removal services (Sucuri, Wordfence)

---

**Last Updated**: 2025-01-27
**Priority**: CRITICAL - Fix Immediately
