# 🚀 Render SSL Setup - Step by Step

## Quick Fix for SSL Error

The SSL error is happening because **SSL needs to be configured in Render Dashboard**. Here's exactly what to do:

---

## Step-by-Step Instructions

### Step 1: Log into Render Dashboard
1. Go to: https://dashboard.render.com
2. Log in with your account
3. Find your **Web Service** for saffron-restaurant

### Step 2: Add Custom Domain
1. Click on your **Web Service**
2. Go to **Settings** tab
3. Scroll down to **"Custom Domains"** section
4. Click **"Add Custom Domain"**
5. Enter: `www.saffrontheindiankitchen.com`
6. Click **"Save"**

### Step 3: Configure DNS (If Not Done)
Render will show you DNS instructions. You need:

**Option A: CNAME Record (Recommended)**
```
Type: CNAME
Name: www
Value: [your-service-name].onrender.com
TTL: 3600
```

**Option B: A Record**
```
Type: A
Name: @ (or blank)
Value: [Render's IP - shown in dashboard]
TTL: 3600
```

### Step 4: Wait for SSL Certificate
1. After adding domain, Render automatically provisions SSL
2. Status will show: **"Provisioning"** → **"Active"**
3. This takes **5-10 minutes** usually
4. You'll see a green checkmark when ready

### Step 5: Test Your Site
1. Wait for SSL status to be **"Active"**
2. Visit: https://www.saffrontheindiankitchen.com
3. Should load without SSL errors

---

## What Render Does Automatically

✅ **Automatic SSL**: Render uses Let's Encrypt for free SSL certificates  
✅ **Auto-Renewal**: Certificates renew automatically  
✅ **HTTPS Redirect**: Render can redirect HTTP to HTTPS  
✅ **Load Balancer**: SSL terminates at Render's load balancer (not your container)

---

## Important Notes

1. **Your Docker container runs on HTTP (port 80)** - This is correct!
2. **Render handles SSL** at the infrastructure level
3. **No SSL configuration needed in Dockerfile** - Render does it
4. **The `.htaccess` HTTPS redirect won't work** - Render handles redirects

---

## Troubleshooting

### Issue: "Domain not found" or DNS error
**Solution**: 
- Verify DNS records are correct
- Wait 24-48 hours for DNS propagation
- Use `nslookup www.saffrontheindiankitchen.com` to check

### Issue: SSL certificate stuck on "Provisioning"
**Solution**:
- Wait 15-20 minutes
- Check DNS is correctly configured
- Contact Render Support if stuck > 1 hour

### Issue: "Invalid response" or SSL error persists
**Solution**:
- Check Render service is running (Dashboard → Status)
- Check Render logs for errors
- Verify domain is correctly added
- Try accessing via Render URL first: `https://[service].onrender.com`

---

## Verification Checklist

After setup, verify:
- [ ] Domain added in Render Dashboard
- [ ] DNS records configured correctly
- [ ] SSL certificate status: "Active"
- [ ] Service status: "Live"
- [ ] Can access via HTTPS
- [ ] No SSL errors in browser
- [ ] Green padlock shows in browser

---

## Need Help?

If SSL setup is confusing or you need assistance:
1. **Render Documentation**: https://render.com/docs/custom-domains
2. **Render Support**: Available in dashboard
3. **Check Service Logs**: Render Dashboard → Logs tab

---

**Action Required**: Go to Render Dashboard and add your custom domain NOW!
