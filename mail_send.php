<?php
// Production environment settings
ini_set('display_errors', 0); // Hide errors in production
error_reporting(0);

$send = 0;
$emailErr = '';
require_once "recaptchalib.php";

if(isset($_POST['g-recaptcha-response'])) {
    // your secret key
    $secret = "6Le0uBwbAAAAAKJPiq02exawKpQme3l9mPZ3_Tln";

    // empty response
    $response = null;
     
    // check secret key
    $reCaptcha = new ReCaptcha($secret);
    
    if ($_POST["g-recaptcha-response"]) {
        $response = $reCaptcha->verifyResponse(
            $_SERVER["REMOTE_ADDR"],
            $_POST["g-recaptcha-response"]
        );
    }
    
    if($response == null) {
        $emailErr = "Please Select Captcha";
    }
    
    function email_validation($str) {
        return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str))
            ? FALSE : TRUE;
    }
  
    // Function call
    if(!email_validation($_POST['email'])) {
        $status = 1;
        $emailErr = "Email Not Valid";
    }

    $to = 'saffrontheindiankitchen@gmail.com,nashvilledigitalgroup@gmail.com';
    $subject = "New Inquiry from Saffron Website";
    $messagess = "Name: " . $_POST['name'] . "\n" . 
                 "Email: " . $_POST['email'] . "\n" . 
                 "Subject: " . $_POST['subject'] . "\n" . 
                 "Message: " . $_POST['message'] . "\n\n" .
                 "Sent from: " . $_SERVER['HTTP_HOST'];
    
    $msg = wordwrap($messagess, 70);

    // Improved headers for better email delivery
    $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: " . $_POST['email'] . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $mails = mail($to, $subject, $messagess, $headers);

    if($mails && $response->success) {
        $send = 1;
    } else {
        $send = 0;
    }
}

if($send == 1) {
    echo '<h2 style="margin-top:100px;" align="center">Thank You for filling form</h2>';
    header("Refresh:2; url=contact.html");
} else {
    echo '<h2>Something is wrong please try again!</h2>';
    echo "\n";
    echo $emailErr;
    header("Refresh:2; url=contact.html");
}
?>