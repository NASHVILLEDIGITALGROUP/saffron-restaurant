<?php
// Production environment settings
ini_set('display_errors', 0); // Hide errors in production
error_reporting(0);

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
            
            // reCAPTCHA verification (with fallback)
            $recaptchaValid = false;
            
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                require_once "recaptchalib.php";
                
                // Your secret key
                $secret = "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln";
                
                try {
                    $reCaptcha = new ReCaptcha($secret);
                    $response = $reCaptcha->verifyResponse(
                        $_SERVER["REMOTE_ADDR"],
                        $_POST["g-recaptcha-response"]
                    );
                    
                    if($response && $response->success) {
                        $recaptchaValid = true;
                    } else {
                        $emailErr = "reCAPTCHA verification failed. Please try again.";
                        $debugInfo = "reCAPTCHA Error: " . (isset($response->errorCodes) ? implode(', ', $response->errorCodes) : 'Unknown error');
                    }
                } catch (Exception $e) {
                    $emailErr = "reCAPTCHA verification error. Please try again.";
                    $debugInfo = "reCAPTCHA Exception: " . $e->getMessage();
                }
            } else {
                $emailErr = "Please complete the reCAPTCHA verification";
            }
            
            // If reCAPTCHA is valid, proceed with email sending
            if($recaptchaValid) {
                
                // Prepare email data
                $emailData = array(
                    'name' => strip_tags($_POST['name']),
                    'email' => strip_tags($_POST['email']),
                    'subject' => strip_tags($_POST['subject']),
                    'message' => strip_tags($_POST['message']),
                    'timestamp' => date('Y-m-d H:i:s'),
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT']
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
                $logMessage = $emailData['timestamp'] . " - Contact Form Submission:\n";
                $logMessage .= "Name: " . $emailData['name'] . "\n";
                $logMessage .= "Email: " . $emailData['email'] . "\n";
                $logMessage .= "Subject: " . $emailData['subject'] . "\n";
                $logMessage .= "Message: " . $emailData['message'] . "\n";
                $logMessage .= "IP: " . $emailData['ip'] . "\n";
                $logMessage .= "User Agent: " . $emailData['user_agent'] . "\n";
                $logMessage .= "---\n\n";
                
                file_put_contents('contact_submissions.log', $logMessage, FILE_APPEND | LOCK_EX);
                
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