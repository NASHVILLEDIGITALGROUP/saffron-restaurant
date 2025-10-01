<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$send = 0;
$emailErr = '';
require_once "recaptchalib.php";
	// echo '<pre>';
	// print_r($_POST);
	// exit;
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




	$to      ='saffrontheindiankitchen@gmail.com,nashvilledigitalgroup@gmail.com';//'myadav00349@gmail.com';//'//'ravishetkar@gmail.com';
	$subject = "New Inquery";
	$messagess = $_POST['name']."\n - ".$_POST['email']."\n - ".$_POST['subject']."\n - ".$_POST['message']; 
	$msg = wordwrap($messagess,70);

	$headers = "New Inquery to Saffron The Indian Kitchen!";

	$mails = mail($to, $subject, $messagess, $headers);

	if($mails && $response->success) {
		$send = 1;
	} else {
		$send = 0;
	}


	
} 

// header("Location; url=contact");


if($send == 1) {

	echo '<h2 style="margin-top:100px;" align="center">Thank You for filling form</h2>';

	header("Refresh:2; url=contact");
} else {
	echo '<h2>Something is wrong please try again! </h2>';
	echo "\n";
	echo $emailErr;

	header("Refresh:2; url=contact");
}

?>