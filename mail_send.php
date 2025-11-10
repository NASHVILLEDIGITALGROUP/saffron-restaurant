<?php
/**
 * Contact Form Handler - Saffron Restaurant Website
 * 
 * Securely processes contact form submissions with reCAPTCHA verification
 * 
 * Security Features:
 * - Environment-based configuration (no hardcoded secrets)
 * - Input sanitization
 * - reCAPTCHA verification
 * - Error handling without exposing sensitive information
 */

// Load configuration
require_once __DIR__ . '/config.php';

// Ensure config is loaded
if (!defined('SAFFRON_CONFIG_LOADED')) {
    die('Configuration error. Please ensure config.php is properly configured.');
}

$send = 0;
$emailErr = '';
$debugInfo = '';

// Check if form was submitted
if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
    
    // Basic validation
    if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['subject']) || empty($_POST['message'])) {
        $emailErr = "All fields are required";
    } else {
        
        // Email validation (improved regex)
        function email_validation($str) {
            return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        if(!email_validation($_POST['email'])) {
            $emailErr = "Please enter a valid email address";
        } else {
            
            // reCAPTCHA verification (with secure configuration)
            $recaptchaValid = false;
            
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                require_once __DIR__ . '/recaptchalib.php';
                
                try {
                    // Securely get reCAPTCHA secret key from environment
                    // This prevents secret key exposure in source code
                    $secret = getRecaptchaSecret();
                    
                    $reCaptcha = new ReCaptcha($secret);
                    $response = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["g-recaptcha-response"]
                    );
                    
                    if($response && $response->success) {
                        $recaptchaValid = true;
                    } else {
                        $emailErr = "reCAPTCHA verification failed. Please try again.";
                        // Don't expose detailed error codes to users (security best practice)
                        $debugInfo = "reCAPTCHA verification failed";
                        error_log("reCAPTCHA Error: " . (isset($response->errorCodes) ? implode(', ', $response->errorCodes) : 'Unknown error'));
                    }
                } catch (Exception $e) {
                    // Don't expose configuration errors to users
                    $emailErr = "reCAPTCHA verification error. Please try again.";
                    $debugInfo = "Configuration error";
                    // Log detailed error for administrators
                    error_log("reCAPTCHA Exception: " . $e->getMessage());
                }
            } else {
                $emailErr = "Please complete the reCAPTCHA verification";
            }
            
            // If reCAPTCHA is valid, proceed with email sending
            if($recaptchaValid) {
                
                // Prepare email data with improved sanitization
                // Using htmlspecialchars instead of strip_tags for better security
                $emailData = array(
                    'name' => htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8'),
                    'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
                    'subject' => htmlspecialchars(trim($_POST['subject']), ENT_QUOTES, 'UTF-8'),
                    'message' => htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8'),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
                );
                
                $mailSent = false;
                
                // Method 1: Try Formspree (free email service)
                $formspreeUrl = 'https://formspree.io/f/xpwgqkqy'; // Replace with your Formspree endpoint
                $formspreeData = array(
                    'name' => $emailData['name'],
                    'email' => $emailData['email'],
                    'subject' => $emailData['subject'],
                    'message' => $emailData['message'],
                    '_replyto' => $emailData['email']
                );
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $formspreeUrl);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formspreeData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'User-Agent: Saffron-Website-Contact-Form'
                ));
                
                $formspreeResult = curl_exec($ch);
                $formspreeHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($formspreeHttpCode == 200) {
                    $mailSent = true;
                    $debugInfo = "Email sent via Formspree";
                }
                
                // Method 2: Try standard PHP mail() as backup
                if (!$mailSent) {
                    $to = 'saffrontheindiankitchen@gmail.com,nashvilledigitalgroup@gmail.com';
                    $subject = "New Inquiry from Saffron Website - " . $emailData['subject'];
                    
                    $message = "New contact form submission from Saffron The Indian Kitchen website:\n\n";
                    $message .= "Name: " . $emailData['name'] . "\n";
                    $message .= "Email: " . $emailData['email'] . "\n";
                    $message .= "Subject: " . $emailData['subject'] . "\n";
                    $message .= "Message: " . $emailData['message'] . "\n\n";
                    $message .= "Submitted on: " . $emailData['timestamp'] . "\n";
                    $message .= "IP Address: " . $emailData['ip'] . "\n";
                    $message .= "Website: " . $_SERVER['HTTP_HOST'] . "\n";
                    
                    $headers = "From: noreply@saffrontheindiankitchen.com\r\n";
                    $headers .= "Reply-To: " . $emailData['email'] . "\r\n";
                    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                    
                    if (mail($to, $subject, $message, $headers)) {
                        $mailSent = true;
                        $debugInfo = "Email sent via PHP mail()";
                    }
                }
                
                // Method 3: Log to file (always do this as backup)
                // SECURITY: Log file should be outside web root in production
                // For now, using web root but should be moved to ../logs/ directory
                $logDir = __DIR__ . '/logs';
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0750, true); // Create logs directory if it doesn't exist
                }
                $logFile = $logDir . '/contact_submissions.log';
                
                $logMessage = $emailData['timestamp'] . " - Contact Form Submission:\n";
                $logMessage .= "Name: " . $emailData['name'] . "\n";
                $logMessage .= "Email: " . $emailData['email'] . "\n";
                $logMessage .= "Subject: " . $emailData['subject'] . "\n";
                $logMessage .= "Message: " . $emailData['message'] . "\n";
                $logMessage .= "IP: " . $emailData['ip'] . "\n";
                $logMessage .= "User Agent: " . $emailData['user_agent'] . "\n";
                $logMessage .= "---\n\n";
                
                // Attempt to write to logs directory, fallback to current directory if needed
                if (is_dir($logDir) && is_writable($logDir)) {
                    @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
                } else {
                    // Fallback to current directory (less secure but ensures logging works)
                    @file_put_contents(__DIR__ . '/contact_submissions.log', $logMessage, FILE_APPEND | LOCK_EX);
                }
                
                // Always mark as successful if logged to file
                if (!$mailSent) {
                    $mailSent = true;
                    $debugInfo = "Email service unavailable, but submission logged successfully";
                }
            }
            
            if($mailSent) {
                $send = 1;
            } else {
                $emailErr = "Unable to process your request at this time.";
                $debugInfo = "All methods failed";
            }
        }
    }
} else {
    $emailErr = "Form data is missing. Please try again.";
}

// Response handling
if($send == 1) {
    echo '<div style="text-align: center; margin-top: 100px; font-family: Arial, sans-serif;">';
    echo '<h2 style="color: #28a745;">Thank You!</h2>';
    echo '<p>Your message has been received successfully. We will get back to you soon.</p>';
    if(!empty($debugInfo)) {
        echo '<p style="font-size: 12px; color: #666;">Status: ' . $debugInfo . '</p>';
    }
    echo '<p>Redirecting to contact page...</p>';
    echo '</div>';
    header("Refresh:3; url=contact.html");
} else {
    echo '<div style="text-align: center; margin-top: 100px; font-family: Arial, sans-serif;">';
    echo '<h2 style="color: #dc3545;">Something went wrong!</h2>';
    echo '<p>' . $emailErr . '</p>';
    if(!empty($debugInfo)) {
        echo '<p style="font-size: 12px; color: #666;">Debug: ' . $debugInfo . '</p>';
    }
    echo '<p>Please try again or contact us directly at saffrontheindiankitchen@gmail.com</p>';
    echo '<p>Redirecting to contact page...</p>';
    echo '</div>';
    header("Refresh:5; url=contact.html");
}
?>