<?php
// Error reporting disabled for production
error_reporting(0);

// Database connection constants
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'jinnbridge');

// Establish MySQLi connection
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set PHP settings
ini_set('memory_limit', '-1'); // Set memory limit to unlimited
set_time_limit(500); // Set maximum execution time to 500 seconds

// Include necessary files
require_once 'class/class.phpmailer.php'; // Use require_once for file inclusion
require_once 'pdf.php'; // Include any other necessary files

// Fetch current date
$today = date("Y-m-d");

// Fetch order_id from GET parameter (validate input!)
echo smtp_mailer('ahmedhamzaansari.99@gmail.com', date('Y-m-d H:i:s'), 'Test', 'Test', 'Test', 'Test');


// Function to send email using PHPMailer
function smtp_mailer($tm_email, $time, $dealers_name, $tm_name, $msg_subject, $msg_body)
{
    // Establish PDO connection for logging (consider logging in a separate function)
    $connect = new PDO("mysql:host=localhost;dbname=jinnbridge", "root", "");

    // Initialize PHPMailer
    $mail = new PHPMailer();
    $mail->SMTPDebug = 0; // Set to 0 for production, 2 or 3 for debugging
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->Username = "jinn.alertinfo@gmail.com";
    $mail->Password = "lbbsvqnavfl333333";
    $mail->SetFrom("jinn.alertinfo@gmail.com");
    $mail->AddAddress($tm_email);
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = $msg_subject;
    $mail->Body = $msg_body;

    if ($mail->Send()) {
        echo 'Email sent successfully to ' . $tm_email . '. Please check your email.';
    } else {
        echo 'Mail not sent to ' . $tm_email . '. Error: ' . $mail->ErrorInfo;
    }
}
?>
