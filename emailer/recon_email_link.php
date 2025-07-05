<?php
ini_set('max_execution_time', '0'); // No time limit for script execution
ini_set('memory_limit', '-1'); // No memory limit

$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 45; URL=$url1");

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'jinnbridge');

$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

include('class/class.phpmailer.php');
include('pdf.php');

// Current Date and Time
$today = date("Y-m-d");
$_to_today = date("Y-m-d H:i:s");
echo $_to_today . ' Run time <br>';

$dur_time = $today . ' 00:00:00 ' . ' - ' . $_to_today;
$report_time = 1;
$email = 'ahmedhamzaansari.99@gmail.com';
$report = 'vehicle';
$user_id;
$privilege = 'Admin';
$time_1 = "";
$black_1 = "";
$cartraige_name = "";
$report_timing = "";

// Current Time and Day
$currentHour = date('H:i');
$currentDay = date('l');

// Check if current time is 9:00 AM on Monday
if ($currentHour == '09:00' && $currentDay == 'Monday') {
    echo 'Hamza';
    echo smtp_mailer('insanehamza1@gmail.com', $db);
    echo smtp_mailer('demo@jinn.com', $db);
    echo smtp_mailer('abasit9119@gmail.com.com', $db);

} else {
    echo "It's not 9 AM yet. Current time: " . $currentHour;
}

$connect = new PDO("mysql:host=localhost;dbname=jinnbridge", "root", "");

// SMTP Mailer Function
function smtp_mailer($to, $db)
{
    $alert_today = date("Y-m-d");
    $alert_today_time = date("Y-m-d H:i:s");

    $verificationCode = generateVerificationCode();
    $alert_link = "http://151.106.17.246:8080/JinnBridge/all_dealers_recons_product_wise_emailer_link.php?e_id=$to";

    // HTML Content for the email
    $html_code = '<div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 style="font-weight: bold; color: #3e3ea7;font-size: 72px;font-style: italic;font-weight: bold;text-decoration: underline">Jinn</h2>
                        </div>
                        <h6>Report Name : Reconciliation Analyzing Report</h6>
                    </div>
                </div>';

    // Generate PDF
    $pdf = new Pdf();
    $pdf->load_html($html_code);
    $pdf->render();

    // PHPMailer setup
    $mail = new PHPMailer();
    $mail->SMTPDebug = 3;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "jinn.alertinfo@gmail.com";
    $mail->Password = "lbbsvqnavfl333333";
    $mail->SetFrom("jinn.alertinfo@gmail.com");
    $mail->AddAddress($to);
    $mail->WordWrap = 50;
    $mail->Subject = 'Jinn Reconciliation Analyzing Report ' . date('Y-m-d');
    $mail->Body = '<h1>Jinn.</h1><h3>Please open link for Reconciliation Analyzing Report and use this code for verification. Code: ' . $verificationCode . ' URL: ' . $alert_link;

    // Send the email
    if ($mail->Send()) {
        echo '<label class="text-success">Customer Details have been sent successfully...</label>';

        // Date calculations for the current week (Monday to Sunday)
        date_default_timezone_set('Asia/Karachi');

        $today = strtotime("today");
        $startOfWeek = strtotime("last Monday", $today);
        if (date('l', $today) == 'Monday') {
            $startOfWeek = $today;
        }

        $endOfWeek = strtotime("next Sunday", $today);
        if (date('l', $today) == 'Sunday') {
            $endOfWeek = $today;
        }

        $fromDate = date('Y-m-d', $startOfWeek);
        $toDate = date('Y-m-d', $endOfWeek);

        // Insert the record into the database
        $verify = "INSERT INTO `recon_email_link`
                    (`email`, `from`, `to`, `password`, `created_at`)
                    VALUES ('$to', '$fromDate', '$toDate', '$verificationCode', '$alert_today_time')";
        mysqli_query($db, $verify);

    } else {
        echo 'Mail not sent. Error: ' . $mail->ErrorInfo;
    }
}

// Generate a verification code
function generateVerificationCode($length = 6)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

?>
