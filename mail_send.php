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
                
                $to = 'saffrontheindiankitchen@gmail.com,nashvilledigitalgroup@gmail.com';
                $subject = "New Inquiry from Saffron Website - " . $_POST['subject'];
                
                $message = "New contact form submission from Saffron The Indian Kitchen website:\n\n";
                $message .= "Name: " . strip_tags($_POST['name']) . "\n";
                $message .= "Email: " . strip_tags($_POST['email']) . "\n";
                $message .= "Subject: " . strip_tags($_POST['subject']) . "\n";
                $message .= "Message: " . strip_tags($_POST['message']) . "\n\n";
                $message .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";
                $message .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
                $message .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
                $message .= "Website: " . $_SERVER['HTTP_HOST'] . "\n";
                
                // Improved headers for better email delivery
                $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
                $headers .= "Reply-To: " . strip_tags($_POST['email']) . "\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                $headers .= "X-Priority: 3\r\n";
                
                // Send email
                $mailSent = mail($to, $subject, $message, $headers);
                
                if($mailSent) {
                    $send = 1;
                } else {
                    $emailErr = "Failed to send email. Please try again or contact us directly.";
                    $debugInfo = "Mail function returned false";
                }
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
    echo '<p>Your message has been sent successfully. We will get back to you soon.</p>';
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