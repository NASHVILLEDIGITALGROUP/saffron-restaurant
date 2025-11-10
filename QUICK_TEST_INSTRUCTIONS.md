# Quick Test Instructions - Step by Step

## 🚀 FASTEST WAY TO TEST (Using File System)

Since you're testing locally, here's the easiest method:

---

## Test 1: Automated Security Test (2 minutes)

### Step 1: Open Test Script
1. In File Explorer, navigate to: `C:\Users\colle\saffron-restaurant`
2. Double-click `test_security.php`
3. It should open in your default browser

**OR** Right-click → Open with → Choose your browser

### Step 2: Review Results
- Look at the page - it will show colored boxes (green = pass, red = fail)
- Scroll through all test sections
- Take a screenshot if you want

**What You Should See:**
- ✅ Green boxes = Tests passed
- ❌ Red boxes = Tests failed (need to fix)
- ⚠️ Yellow boxes = Warnings (usually OK)

### Step 3: Check Summary
- Scroll to bottom of page
- See "Test Summary" section
- Should say "ALL CRITICAL TESTS PASSED" if everything works

---

## Test 2: Contact Form Test (3 minutes)

### Step 1: Open Contact Page
1. In File Explorer, navigate to: `C:\Users\colle\saffron-restaurant`
2. Double-click `contact.html`
3. It should open in your browser

### Step 2: Open Browser Developer Tools
1. Press **F12** (or Right-click → Inspect)
2. Click **Console** tab
3. Look for any **red error messages**

**What to Look For:**
- ✅ No red errors = Good
- ❌ Red errors = Problem (tell me what they say)

### Step 3: Check reCAPTCHA Widget
1. Scroll down to the contact form
2. Look for a box that says "I'm not a robot" or shows reCAPTCHA
3. **If you see it:** ✅ Good!
4. **If you don't see it:** ❌ Problem (check console for errors)

**Note:** reCAPTCHA needs internet connection to load

### Step 4: Test Form Submission
1. Fill in the form:
   - Name: "Test"
   - Email: "test@test.com"
   - Subject: "Test"
   - Message: "Testing"
2. Check the reCAPTCHA box (if visible)
3. Click "Send" button
4. **What happens?**
   - ✅ Success message = Good!
   - ❌ Error message = Tell me what it says

**Note:** Since you're opening from file://, the form might not submit to PHP. That's OK - we're just checking if everything loads.

---

## Test 3: Error Handling Test (1 minute)

### Step 1: Rename .env File
1. In File Explorer, go to: `C:\Users\colle\saffron-restaurant`
2. Find `.env` file
3. Right-click → Rename
4. Change name to: `.env.backup`

### Step 2: Try Form Again
1. Refresh contact.html page
2. Try submitting form again
3. **What error do you see?**
   - Should be user-friendly (not showing file paths)

### Step 3: Restore .env
1. Rename `.env.backup` back to `.env`
2. Form should work again

---

## Test 4: Security Check (30 seconds)

### Check Secret Key Not in Code
1. Open `mail_send.php` in a text editor
2. Press **Ctrl+F** to search
3. Search for: `6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln`
4. **Result:**
   - ✅ Not found = Good! (secret key removed)
   - ❌ Found = Problem (still hardcoded)

### Check .env File Protection
1. Try opening `.env` file in browser:
   - Type in address bar: `file:///C:/Users/colle/saffron-restaurant/.env`
2. **What happens?**
   - ✅ Can't access or shows error = Good!
   - ❌ Shows file contents = Security issue

---

## 📋 QUICK CHECKLIST

Run through these quickly:

- [ ] `test_security.php` opens and shows test results
- [ ] All tests show ✅ PASS (or mostly pass)
- [ ] `contact.html` opens without errors
- [ ] Browser console (F12) shows no red errors
- [ ] reCAPTCHA widget appears (if internet available)
- [ ] Form fields are visible and fillable
- [ ] Secret key NOT found in `mail_send.php` (search for it)
- [ ] `.env` file exists in folder

---

## 🎯 WHAT TO REPORT BACK

After testing, tell me:

1. **Test Script Results:**
   - How many tests passed?
   - Any tests failed? (which ones?)

2. **Contact Form:**
   - Does it load?
   - Any console errors?
   - Does reCAPTCHA appear?

3. **Issues Found:**
   - List any problems you see

---

## ⚠️ COMMON ISSUES

### Issue: "test_security.php shows as download"
**Solution:** Right-click → Open with → Choose browser

### Issue: "reCAPTCHA doesn't appear"
**Possible Reasons:**
- No internet connection
- Ad blocker blocking it
- JavaScript disabled

**Solution:** Check internet, disable ad blocker temporarily

### Issue: "Form doesn't submit"
**Reason:** Opening from file:// won't execute PHP
**Solution:** This is expected - we're just checking if everything loads

---

## 🎉 SUCCESS CRITERIA

You've successfully tested if:

- ✅ Test script shows mostly green checkmarks
- ✅ Contact form loads without errors
- ✅ reCAPTCHA widget appears (or loads)
- ✅ No secret key in mail_send.php
- ✅ .env file exists

**That's it!** Let me know what you find! 🚀

