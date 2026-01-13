<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer/src/Exception.php';
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';

session_start();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: https://greenacrepair.com/green-air/form-error.html');
    exit;
}

// Rate limiting - prevent spam submissions
$lastSubmit = $_SESSION['last_submit'] ?? 0;
if (time() - $lastSubmit < 60) {
    header('Location: https://greenacrepair.com/green-air/form-error.html');
    exit;
}

// Honeypot spam protection
if (!empty($_POST['website'])) {
    exit; // Bot detected
}

// Get and sanitize form data
$name    = trim($_POST['full_name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');

// Validation
if ($name === '' || $email === '' || $phone === '' || $service === '' || 
    !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: https://greenacrepair.com/green-air/form-error.html');
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isMail();
    
    // 🔥 CHANGE THIS: Use client's domain email
    $mail->setFrom('development@astraresults.com', 'Green Air');
    
    // 🔥 ADD THIS LINE: Forces the envelope sender (the key fix!)
    $mail->Sender = 'development@astraresults.com';
    
    // Recipients
    $mail->addAddress('development@astraresults.com');
    $mail->addCC('development@astraresults.com');
    
    $mail->addCC('development@astraresults.com');
    
    // Reply-To: customer's email for easy response
    $mail->addReplyTo($email, $name);
    
    // Email settings
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'New Service Request from ' . $name;
    
    // HTML email body
    $mail->Body = "
        <h3>New Request Form Submission</h3>
        <p><strong>Full Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Service:</strong> $service</p>
    ";
    
    // Send email
    $mail->send();
    
    // Update session to prevent rapid resubmission
    $_SESSION['last_submit'] = time();
    
    // Redirect to success page
    header('Location: https://greenacrepair.com/green-air/thank-you.html');
    exit;
    
} catch (Exception $e) {
    // Log error (check your cPanel error logs)
    error_log("Morata Form Error: " . $mail->ErrorInfo);
    
    // Redirect to error page
    header('Location: https://greenacrepair.com/green-air/form-error.html');
    exit;
}
?>