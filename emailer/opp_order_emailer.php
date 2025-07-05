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
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$dealer_name_global = '';

// Fetch data for email recipients
$eng_query = "SELECT dl.name as dealer_name, us.name as tm_name, us.login as tm_email, dl.email as dealer_email 
              FROM order_main as om 
              JOIN dealers as dl ON dl.sap_no = om.dealer_sap
              JOIN users as us ON us.id = dl.asm 
              WHERE om.id = $order_id";

$result_eng = mysqli_query($db, $eng_query);

// Check if query executed successfully
if (!$result_eng) {
    die('Query failed: ' . mysqli_error($db));
}

$count_eng = mysqli_num_rows($result_eng);

// Process each row from the query result
if ($count_eng > 0) {
    while ($row = mysqli_fetch_array($result_eng)) {
        $dealer_name = $row["dealer_name"];
        $dealer_name_global = $row["dealer_name"];
        $tm_email = $row["tm_email"];
        $tm_name = $row["tm_name"];
        $dealer_email = $row["dealer_email"];

        // Compose email message
        $msg_subject_tm = $dealer_name_global . ' order Forward successfully';
        $msg_body_tm = '<p>Hi ' . $tm_name . ',</p><p>Order of ' . $dealer_name_global . ' has been Forward successfully.</p>';

        // Compose email message for dealer
        $msg_subject_dealer = $dealer_name_global . ' order Forward successfully';
        $msg_body_dealer = '<p>Hi ' . $dealer_name . ',</p><p>Your order of ' . $dealer_name_global . ' has been Forward successfully.</p>';

        // Send email to TM and dealer
        echo smtp_mailer($tm_email, date('Y-m-d H:i:s'), $dealer_name_global, $tm_name, $msg_subject_tm, $msg_body_tm);
        echo smtp_mailer($dealer_email, date('Y-m-d H:i:s'), $dealer_name_global, $dealer_name, $msg_subject_dealer, $msg_body_dealer);
    }
}

// Fetch data for App_order privilege users
$app_order_query = "SELECT * FROM users WHERE privilege='App_order'";
$result_App_order = mysqli_query($db, $app_order_query);

if (!$result_App_order) {
    die('Query failed: ' . mysqli_error($db));
}

$count_App_order = mysqli_num_rows($result_App_order);

// Process each row from the query result
if ($count_App_order > 0) {
    while ($row = mysqli_fetch_array($result_App_order)) {
        $login = $row["login"];
        $name = $row["name"];

        // Compose email message for App_order user
        $msg_subject = $dealer_name_global . ' order Forward successfully ';
        $msg_body = '<p>Hi ' . $name . '</h3><p>Order of ' . $dealer_name_global . ' Forward successfully.</p>';

        // Send email to App_order user
        echo smtp_mailer($login, date('Y-m-d H:i:s'), $dealer_name_global, $name, $msg_subject, $msg_body);
    }
}

$forwarded_order_query = "SELECT * FROM users WHERE privilege='Forward_order'";
$result_forwarded_order = mysqli_query($db, $forwarded_order_query);

if (!$result_forwarded_order) {
    die('Query failed: ' . mysqli_error($db));
}

$count_forwarded_order = mysqli_num_rows($result_forwarded_order);

// Process each row from the query result
if ($count_App_order > 0) {
    while ($row = mysqli_fetch_array($result_forwarded_order)) {
        $login = $row["login"];
        $name = $row["name"];

        // Compose email message for App_order user
        $msg_subject = $dealer_name_global . ' order Forward successfully ';
        $msg_body = '<p>Hi ' . $name . '</h3><p>Order of ' . $dealer_name_global . ' Forward successfully. Please Verify and Push.</p>';

        // Send email to App_order user
        echo smtp_mailer($login, date('Y-m-d H:i:s'), $dealer_name_global, $name, $msg_subject, $msg_body);
        echo smtp_mailer('abasit9119@gmail.com', date('Y-m-d H:i:s'), $dealer_name_global, 'Basit', $msg_subject, $msg_body);
        echo smtp_mailer('demo@jinn.com', date('Y-m-d H:i:s'), $dealer_name_global, 'Sohaib', $msg_subject, $msg_body);
    }
}

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
