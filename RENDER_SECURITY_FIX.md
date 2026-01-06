# 🔒 Render Hosting - Security Fix Guide

## ⚠️ Important: Render Doesn't Use .htaccess

Since your site is hosted on **Render**, the `.htaccess` file **won't work** because:
- Render doesn't use Apache by default
- Render uses different web servers (Nginx, Caddy, or custom)
- Security must be configured differently

---

## ✅ What I've Already Fixed (Still Valid)

1. ✅ **Deleted malicious files** - These are removed from your codebase
2. ✅ **Fixed Gravitec script** - Error handling added
3. ✅ **Updated HTML files** - Both `index.html` and `contact.html` are fixed

---

## 🔧 Render-Specific Security Configuration

### Option 1: If Using Docker (Recommended)

If you're using the `Dockerfile`, add security headers via PHP or Nginx configuration.

#### Update Dockerfile

Add Nginx configuration for security headers:

```dockerfile
# Use official PHP 8.1 with Apache
FROM php:8.1-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy all files to the container
COPY . /var/www/html/

# Create security configuration
RUN echo '<IfModule mod_headers.c>' > /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-Content-Type-Options "nosniff"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-Frame-Options "SAMEORIGIN"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set X-XSS-Protection "1; mode=block"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set Referrer-Policy "strict-origin-when-cross-origin"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '    Header set Content-Security-Policy "default-src '\''self'\''; script-src '\''self'\'' '\''unsafe-inline'\'' https://www.google.com https://www.gstatic.com https://cdn.gravitec.net https://maps.googleapis.com; style-src '\''self'\'' '\''unsafe-inline'\''; frame-ancestors '\''self'\'';"' >> /etc/apache2/conf-available/security-headers.conf \
    && echo '</IfModule>' >> /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Block PHP execution in asset directories
RUN echo '<DirectoryMatch "^/.*/(js|css|fonts|img)/">' > /etc/apache2/conf-available/block-php-assets.conf \
    && echo '    <FilesMatch "\.php$">' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '        Require all denied' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '    </FilesMatch>' >> /etc/apache2/conf-available/block-php-assets.conf \
    && echo '</DirectoryMatch>' >> /etc/apache2/conf-available/block-php-assets.conf \
    && a2enconf block-php-assets

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html -name "*.php" -type f -exec chmod 644 {} \;

# Configure Apache virtual host
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf \
    && echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    ServerName localhost' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf \
    && echo '        DirectoryIndex index.html index.php' >> /etc/apache2/sites-available/000-default.conf \
    && echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf \
    && echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
```

### Option 2: If Using Static Site on Render

If Render is serving this as a static site, create a `_headers` file (for Netlify-style) or configure via Render dashboard.

#### Create `_headers` file (if supported):

```
/*
  X-Content-Type-Options: nosniff
  X-Frame-Options: SAMEORIGIN
  X-XSS-Protection: 1; mode=block
  Referrer-Policy: strict-origin-when-cross-origin
  Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://cdn.gravitec.net https://maps.googleapis.com; style-src 'self' 'unsafe-inline'; frame-ancestors 'self';
```

### Option 3: PHP-Based Security Headers

Add security headers directly in PHP. Create `security-headers.php`:

```php
<?php
/**
 * Security Headers for Render Hosting
 * Include this at the top of index.html (if using PHP) or in a bootstrap file
 */

// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://cdn.gravitec.net https://maps.googleapis.com; style-src 'self' 'unsafe-inline'; frame-ancestors 'self';");

// Block access to malicious directories
$blockedDirs = ['RBw3WC9wE7', '2B1sdvjTnX', 'Qp4XeVAvjj'];
$requestUri = $_SERVER['REQUEST_URI'] ?? '';

foreach ($blockedDirs as $dir) {
    if (strpos($requestUri, $dir) !== false) {
        http_response_code(403);
        die('Access Denied');
    }
}

// Block PHP execution in asset directories
$assetDirs = ['/js/', '/css/', '/fonts/', '/img/'];
$isAssetDir = false;
foreach ($assetDirs as $assetDir) {
    if (strpos($requestUri, $assetDir) !== false && strpos($requestUri, '.php') !== false) {
        $isAssetDir = true;
        break;
    }
}

if ($isAssetDir) {
    http_response_code(403);
    die('Access Denied');
}
```

Then include it in `index.html` (if using PHP) or create a wrapper.

---

## 🚀 Render Deployment Steps

### 1. Update Your Render Service

1. Go to Render Dashboard → Your Service
2. Go to **Settings** → **Build Command**
3. Ensure build command is appropriate (may be empty for static sites)

### 2. Environment Variables

Add these in Render Dashboard → **Environment**:

```
DISPLAY_ERRORS=0
ERROR_REPORTING=0
RECAPTCHA_SECRET_KEY=your_secret_key_here
RECAPTCHA_SITE_KEY=your_site_key_here
```

### 3. Deploy Updated Code

1. **Commit all changes** to your repository:
   ```bash
   git add .
   git commit -m "Security fix: Remove malware and add security headers"
   git push
   ```

2. **Render will auto-deploy** if connected to Git
3. Or **manually deploy** from Render dashboard

### 4. Verify Deployment

After deployment:
1. Check Render logs for errors
2. Test website: https://www.saffrontheindiankitchen.com
3. Check browser console (F12) for errors
4. Verify security headers:
   - Open browser DevTools → Network tab
   - Reload page
   - Click on any request
   - Check "Response Headers" for security headers

---

## 🔍 How to Check Your Render Configuration

### Check Render Service Type:

1. Go to Render Dashboard
2. Click on your service
3. Check **Service Type**:
   - **Web Service** (Docker/PHP)
   - **Static Site** (HTML/CSS/JS)
   - **Background Worker**

### Check Build Settings:

1. Go to **Settings** → **Build & Deploy**
2. Note:
   - **Build Command**
   - **Start Command**
   - **Dockerfile path** (if using Docker)

---

## 🛡️ Additional Render Security Measures

### 1. Render Environment Variables

Set these in Render Dashboard:
- `DISPLAY_ERRORS=0` (hide PHP errors)
- `ERROR_REPORTING=0` (disable error reporting)

### 2. Render Access Control

1. Go to **Settings** → **Access Control**
2. Enable if you want to restrict access
3. Add IP whitelist if needed

### 3. Render Logs Monitoring

1. Go to **Logs** tab in Render Dashboard
2. Monitor for:
   - Suspicious requests
   - Failed file access attempts
   - PHP errors

### 4. Render Auto-Deploy Settings

1. Go to **Settings** → **Auto-Deploy**
2. Consider:
   - Deploy only from main branch
   - Require manual approval for deployments

---

## 📋 Render-Specific Checklist

- [ ] Updated `Dockerfile` with security headers (if using Docker)
- [ ] Created `security-headers.php` (if using PHP)
- [ ] Set environment variables in Render dashboard
- [ ] Deleted all malicious files from repository
- [ ] Committed and pushed changes
- [ ] Verified deployment in Render logs
- [ ] Tested website functionality
- [ ] Verified security headers are active
- [ ] Changed all passwords (FTP, Git, etc.)

---

## 🔧 Quick Fix: Add Security Headers via PHP

If you need a quick solution, add this to the top of `index.html` (rename to `index.php`):

```php
<?php
// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
?>
<!doctype html>
<html class="no-js" lang="en-US">
<!-- rest of your HTML -->
```

Or create a `bootstrap.php` file and include it.

---

## 🆘 If Issues Persist on Render

1. **Check Render Logs**:
   - Go to Render Dashboard → Logs
   - Look for PHP errors or deployment issues

2. **Check Build Logs**:
   - Go to Render Dashboard → Builds
   - Check latest build for errors

3. **Verify Service Type**:
   - Ensure service is configured correctly
   - Check if using Docker, PHP buildpack, or static site

4. **Contact Render Support**:
   - If deployment fails
   - If security headers aren't working
   - For Render-specific configuration help

---

## ✅ Success Indicators

Your Render deployment is secure when:
- ✅ Website loads without frame errors
- ✅ Security headers present in response
- ✅ No malicious files in repository
- ✅ Render logs show no errors
- ✅ All pages function correctly

---

**Next Steps**: 
1. Determine your Render service type
2. Apply appropriate security configuration
3. Deploy updated code
4. Verify security headers are active
