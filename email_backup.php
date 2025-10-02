<?php
// Alternative email solution using cURL to send emails via external service
// This is a backup method when PHP mail() fails

function sendEmailViaService($to, $subject, $message, $fromEmail, $fromName) {
    // Using EmailJS or similar service as backup
    // For now, we'll use a simple webhook approach
    
    $webhookUrl = 'https://hooks.zapier.com/hooks/catch/your-webhook-url'; // Replace with actual webhook
    
    $data = array(
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
        'from_email' => $fromEmail,
        'from_name' => $fromName,
        'timestamp' => date('Y-m-d H:i:s')
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $webhookUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpCode == 200);
}

// Simple email logging function
function logEmailSubmission($data) {
    $logEntry = date('Y-m-d H:i:s') . " - Contact Form Submission:\n";
    $logEntry .= "Name: " . $data['name'] . "\n";
    $logEntry .= "Email: " . $data['email'] . "\n";
    $logEntry .= "Subject: " . $data['subject'] . "\n";
    $logEntry .= "Message: " . $data['message'] . "\n";
    $logEntry .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $logEntry .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
    $logEntry .= "---\n\n";
    
    return file_put_contents('contact_submissions.log', $logEntry, FILE_APPEND | LOCK_EX);
}
?>
