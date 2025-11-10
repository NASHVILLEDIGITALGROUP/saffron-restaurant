# Testing Guide - reCAPTCHA Security Implementation

**Date:** January 2025  
**Purpose:** Comprehensive testing guide for secure reCAPTCHA implementation

---

## 🧪 TESTING CHECKLIST

### Phase 1: Automated Security Tests

#### Test 1: Run Security Test Script

1. **Open the test script:**
   - Navigate to: `http://localhost:8080/test_security.php` (or your local URL)
   - Or open: `file:///C:/Users/colle/saffron-restaurant/test_security.php`

2. **Review test results:**
   - All tests should show ✅ PASS
   - Note any ❌ FAIL or ⚠️ WARNING messages
   - Take screenshot of results for documentation

3. **Expected Results:**
   - ✅ .env file exists
   - ✅ config.php loads successfully
   - ✅ Environment variables are set
   - ✅ getRecaptchaSecret() works
   - ✅ Secret key NOT in mail_send.php
   - ✅ .htaccess protects .env
   - ✅ .env in .gitignore

---

### Phase 2: Manual Form Testing

#### Test 2: Contact Form Functionality

1. **Open Contact Page:**
   ```
   Open: contact.html in browser
   Or: http://localhost:8080/contact.html
   ```

2. **Check Browser Console:**
   - Press `F12` to open Developer Tools
   - Go to "Console" tab
   - **Look for:**
     - ✅ No red error messages
     - ✅ reCAPTCHA script loaded
     - ✅ No JavaScript errors

3. **Verify reCAPTCHA Widget:**
   - Scroll to contact form
   - **Look for:**
     - ✅ reCAPTCHA checkbox/widget visible
     - ✅ Widget loads within 2-3 seconds
     - ✅ No error messages in widget area

4. **Test Form Submission:**
   - Fill in all form fields:
     - Name: "Test User"
     - Email: "test@example.com"
     - Subject: "Test Submission"
     - Message: "This is a test message"
   - **Complete reCAPTCHA:**
     - Check the reCAPTCHA box
     - Wait for verification checkmark
   - Click "Send" button
   - **Expected Result:**
     - ✅ Success message appears
     - ✅ Redirects to contact page after 3 seconds
     - ✅ Email is sent/received

5. **Test Error Cases:**
   - **Test 1:** Submit without reCAPTCHA
     - Expected: Error message asking to complete reCAPTCHA
   - **Test 2:** Submit with empty fields
     - Expected: Validation errors (browser or server-side)
   - **Test 3:** Submit with invalid email
     - Expected: Email validation error

---

### Phase 3: Configuration Verification

#### Test 3: PHP Configuration Loading

1. **Check PHP Error Log:**
   ```bash
   # Check for any PHP errors
   # Location depends on your server setup
   # Common locations:
   # - /var/log/apache2/error.log
   # - C:\xampp\apache\logs\error.log
   # - error_log file in project root
   ```

2. **Verify Environment Variables:**
   - Run `test_security.php` (already created)
   - Check Test 4 results
   - Verify both keys are set and not using placeholders

3. **Test Configuration Functions:**
   - The test script automatically tests:
     - `getConfig()` function
     - `getRecaptchaSecret()` function
     - Environment variable loading

---

### Phase 4: Security Verification

#### Test 4: .env File Protection

1. **Test .htaccess Protection:**
   - Try accessing: `http://localhost:8080/.env`
   - **Expected Result:**
     - ✅ 403 Forbidden error
     - ✅ Or "Access Denied" message
     - ❌ Should NOT show file contents

2. **Verify .gitignore:**
   ```bash
   # If using Git, verify .env is ignored
   git status .env
   # Should show: "nothing to commit" or file not listed
   
   git check-ignore .env
   # Should return: .env
   ```

3. **Check Secret Key Not in Code:**
   - The test script checks this automatically
   - Manually verify:
     ```bash
     # Search for secret key in PHP files (should return nothing)
     grep -r "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln" *.php
     # Should only find it in .env file, not in .php files
     ```

---

### Phase 5: Error Handling Test

#### Test 5: Missing .env File Handling

1. **Temporarily Rename .env:**
   ```bash
   # Windows PowerShell
   Rename-Item .env .env.backup
   
   # Or manually rename in file explorer
   ```

2. **Test Form Submission:**
   - Try submitting the contact form
   - **Expected Result:**
     - ✅ User-friendly error message
     - ✅ No file paths exposed
     - ✅ No secret keys visible
     - ✅ Error logged to PHP error log

3. **Check Error Message:**
   - Should see: "reCAPTCHA verification error. Please try again."
   - Should NOT see:
     - File paths
     - Secret keys
     - Configuration details
     - Stack traces

4. **Restore .env File:**
   ```bash
   # Windows PowerShell
   Rename-Item .env.backup .env
   ```

5. **Verify Form Works Again:**
   - Submit form again
   - Should work normally

---

## 📋 TEST RESULTS TEMPLATE

### Test Results Summary

**Date:** _______________  
**Tester:** _______________  
**Environment:** [ ] Local [ ] Staging [ ] Production

#### Automated Tests (test_security.php):

| Test | Status | Notes |
|------|--------|-------|
| .env file exists | [ ] Pass [ ] Fail | |
| config.php loads | [ ] Pass [ ] Fail | |
| Environment variables set | [ ] Pass [ ] Fail | |
| getRecaptchaSecret() works | [ ] Pass [ ] Fail | |
| Secret key removed from code | [ ] Pass [ ] Fail | |
| .htaccess protection | [ ] Pass [ ] Fail | |
| .gitignore configured | [ ] Pass [ ] Fail | |

#### Manual Tests:

| Test | Status | Notes |
|------|--------|-------|
| Contact form loads | [ ] Pass [ ] Fail | |
| No JavaScript errors | [ ] Pass [ ] Fail | |
| reCAPTCHA widget displays | [ ] Pass [ ] Fail | |
| Form submission works | [ ] Pass [ ] Fail | |
| Email received | [ ] Pass [ ] Fail | |
| Error handling (no .env) | [ ] Pass [ ] Fail | |
| .env file protected | [ ] Pass [ ] Fail | |

#### Issues Found:

1. _______________________________________
2. _______________________________________
3. _______________________________________

#### Overall Status:

[ ] ✅ All tests passed - Ready for production  
[ ] ⚠️ Some warnings - Review before production  
[ ] ❌ Tests failed - Fix issues before proceeding

---

## 🔍 WHAT TO LOOK FOR

### ✅ Good Signs:

1. **Browser Console:**
   - No red error messages
   - reCAPTCHA script loaded successfully
   - Form validation works

2. **Form Submission:**
   - Success message appears
   - Email is sent/received
   - No errors in PHP log

3. **Security:**
   - .env file not accessible via browser
   - Secret key not in source code
   - Error messages don't expose paths

### ❌ Warning Signs:

1. **Browser Console:**
   - JavaScript errors
   - reCAPTCHA script failed to load
   - CORS errors

2. **Form Submission:**
   - Form doesn't submit
   - No success message
   - Errors in PHP log

3. **Security:**
   - .env file accessible via browser
   - Secret key visible in source code
   - Error messages show file paths

---

## 🚨 TROUBLESHOOTING

### Issue: reCAPTCHA widget doesn't load

**Possible Causes:**
- JavaScript blocked
- Network issue
- Site key incorrect

**Solutions:**
1. Check browser console for errors
2. Verify site key in `.env` matches contact.html
3. Check internet connection
4. Try different browser

### Issue: Form submission fails

**Possible Causes:**
- .env file missing
- Secret key incorrect
- PHP errors

**Solutions:**
1. Check `.env` file exists
2. Verify secret key in `.env`
3. Check PHP error log
4. Run `test_security.php` to diagnose

### Issue: "Configuration error" message

**Possible Causes:**
- .env file missing
- config.php not loading
- Environment variables not set

**Solutions:**
1. Verify `.env` file exists
2. Check file permissions (600)
3. Verify `config.php` is in root directory
4. Check PHP error log for details

---

## ✅ SUCCESS CRITERIA

All of the following must be true:

- [ ] `test_security.php` shows all tests passing
- [ ] Contact form loads without errors
- [ ] reCAPTCHA widget displays correctly
- [ ] Form submission works
- [ ] Email is sent/received
- [ ] .env file is protected (403 error)
- [ ] Secret key not in source code
- [ ] Error handling works (user-friendly messages)
- [ ] No sensitive data in error messages

---

## 🗑️ CLEANUP

After testing is complete:

1. **Delete test file:**
   ```bash
   # Delete test_security.php
   Remove-Item test_security.php
   ```

2. **Remove .env.backup (if created):**
   ```bash
   Remove-Item .env.backup
   ```

3. **Document test results:**
   - Save test results summary
   - Note any issues found
   - Document fixes applied

---

## 📞 NEXT STEPS

After successful testing:

1. ✅ Proceed to Phase 1, Issue #3: Input Sanitization (XSS Protection)
2. ✅ Proceed to Phase 1, Issue #4: CSRF Protection
3. ✅ Continue with other security fixes

---

**Ready to test?** Follow the phases above and document your results!

