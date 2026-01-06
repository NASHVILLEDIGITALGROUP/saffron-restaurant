# 🚀 Render Deployment Guide - Security Fix

## ✅ What's Been Fixed

1. ✅ **Malicious files deleted** from codebase
2. ✅ **Dockerfile updated** with security headers and malware blocking
3. ✅ **Gravitec script** fixed with error handling
4. ✅ **HTML files** updated

---

## 📋 Render Deployment Steps

### Step 1: Commit All Changes

```bash
# Add all changes
git add .

# Commit with descriptive message
git commit -m "Security fix: Remove malware, add security headers, fix frame errors"

# Push to your repository
git push origin main
```

### Step 2: Verify Render Auto-Deploy

1. Go to **Render Dashboard** → Your Web Service
2. Check if **Auto-Deploy** is enabled
3. If enabled, Render will automatically deploy after you push
4. If not enabled, click **Manual Deploy** → **Deploy latest commit**

### Step 3: Monitor Deployment

1. Go to **Logs** tab in Render Dashboard
2. Watch for:
   - ✅ Build successful
   - ✅ Container started
   - ❌ Any errors (report if found)

### Step 4: Set Environment Variables

Go to **Environment** tab in Render Dashboard and add:

```
DISPLAY_ERRORS=0
ERROR_REPORTING=0
RECAPTCHA_SECRET_KEY=your_actual_secret_key
RECAPTCHA_SITE_KEY=your_actual_site_key
```

**Important**: Replace `your_actual_secret_key` and `your_actual_site_key` with your real reCAPTCHA keys.

### Step 5: Verify Security Headers

After deployment:

1. Visit: https://www.saffrontheindiankitchen.com
2. Open **Browser DevTools** (F12)
3. Go to **Network** tab
4. Reload page
5. Click on any request (usually `index.html`)
6. Check **Response Headers** for:
   - `X-Content-Type-Options: nosniff`
   - `X-Frame-Options: SAMEORIGIN`
   - `X-XSS-Protection: 1; mode=block`
   - `Content-Security-Policy: ...`

---

## 🔍 Render Service Configuration

### If Using Docker (Your Current Setup)

**Service Type**: Web Service  
**Dockerfile Path**: `Dockerfile` (in root)  
**Build Command**: (Leave empty - Render uses Dockerfile)  
**Start Command**: (Leave empty - Dockerfile has CMD)

### Render Settings to Verify

1. **Build & Deploy**:
   - ✅ Dockerfile path: `Dockerfile`
   - ✅ Build command: (empty)
   - ✅ Start command: (empty)

2. **Environment**:
   - ✅ Set environment variables (see Step 4 above)

3. **Health Check** (Optional but recommended):
   - Path: `/`
   - Expected status: 200

---

## 🛡️ Security Features Now Active

After deployment, these security features are active:

1. ✅ **Security Headers**: X-Frame-Options, CSP, etc.
2. ✅ **PHP Blocked in Asset Directories**: js/, css/, img/, fonts/
3. ✅ **Malicious Directories Blocked**: RBw3WC9wE7, 2B1sdvjTnX, Qp4XeVAvjj
4. ✅ **Directory Browsing Disabled**: Options -Indexes
5. ✅ **Proper File Permissions**: 755 for directories, 644 for files

---

## 🧪 Testing After Deployment

### 1. Test Homepage
- ✅ Visit: https://www.saffrontheindiankitchen.com
- ✅ Should load without frame errors
- ✅ Check browser console (F12) for errors

### 2. Test Contact Page
- ✅ Visit: https://www.saffrontheindiankitchen.com/contact.html
- ✅ Form should work
- ✅ reCAPTCHA should load

### 3. Test Security Headers
- ✅ Use browser DevTools → Network → Headers
- ✅ Verify security headers are present

### 4. Test Malware Blocking
- ✅ Try accessing: https://www.saffrontheindiankitchen.com/js/RBw3WC9wE7/
- ✅ Should return 403 Forbidden

---

## 🆘 Troubleshooting

### Issue: Build Fails

**Check Render Logs**:
1. Go to Render Dashboard → Logs
2. Look for error messages
3. Common issues:
   - Dockerfile syntax error
   - Missing dependencies
   - Permission issues

**Solution**: Fix the error in Dockerfile and push again.

### Issue: Website Still Shows Frame Error

**Possible Causes**:
1. Browser cache (clear completely)
2. CDN cache (if using Cloudflare, purge cache)
3. Deployment not complete (check Render logs)

**Solution**:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Try incognito/private mode
3. Wait 5-10 minutes for CDN to update
4. Check Render deployment status

### Issue: Security Headers Not Showing

**Check**:
1. Verify `a2enmod headers` is in Dockerfile (it is)
2. Check Render logs for Apache errors
3. Verify deployment completed successfully

**Solution**: If headers still missing, check Render logs for Apache configuration errors.

### Issue: Contact Form Not Working

**Check**:
1. Environment variables set correctly
2. reCAPTCHA keys are valid
3. Formspree endpoint is correct

**Solution**: Verify environment variables in Render dashboard.

---

## 📊 Render Monitoring

### Check These Regularly:

1. **Deployment Logs**: Ensure successful deployments
2. **Runtime Logs**: Monitor for errors
3. **Metrics**: CPU, Memory usage
4. **Uptime**: Ensure service is running

### Set Up Alerts (Optional):

1. Go to **Settings** → **Alerts**
2. Enable:
   - Deployment failures
   - Service downtime
   - High resource usage

---

## 🔄 Future Updates

When updating your site:

1. **Make changes locally**
2. **Test locally** (using Docker)
3. **Commit and push** to Git
4. **Render auto-deploys** (if enabled)
5. **Verify deployment** in Render logs
6. **Test live site**

---

## ✅ Deployment Checklist

Before considering deployment complete:

- [ ] All changes committed to Git
- [ ] Changes pushed to repository
- [ ] Render deployment successful (check logs)
- [ ] Environment variables set
- [ ] Website loads without errors
- [ ] Security headers present
- [ ] Contact form works
- [ ] No frame errors
- [ ] Browser console shows no critical errors

---

## 📞 Need Help?

If you encounter issues:

1. **Check Render Logs** first
2. **Review this guide** for common solutions
3. **Contact Render Support** if deployment fails
4. **Check Render Status Page** for service issues

---

**Last Updated**: 2025-01-27  
**Status**: Ready for Deployment
