<?php
error_reporting(0);
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'jinnbridge');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


//index.php
ini_set('memory_limit', '-1');
set_time_limit(500);

include ('class/class.phpmailer.php');
include ('pdf.php');
$today = date("Y-m-d");
// $email = $_GET['email'];

$time = date('Y-m-d H:i:s');

$message = '';

$connect = new PDO("mysql:host=localhost;dbname=jinnbridge", "root", "");


// $dealers_id = $_GET['dealers_id'];
$order_id = $_GET['order_id'];



// echo smtp_mailer('abasit9119@gmail.com', $time, $db, $api, $api_name);
// echo smtp_mailer('jahanzaib.javed@gno.com.pk', $time, $db, $api, $api_name);

$eng = "SELECT dl.name as dealer_name,us.name as tm_name,us.login as tm_email FROM order_main as om 
join dealers as dl on dl.sap_no=om.dealer_sap
join users as us on us.id=dl.asm where om.id=$order_id;";

$result_eng = mysqli_query($db, $eng);

$count_eng = mysqli_num_rows($result_eng);
// echo $count_contact . ' hamza <br>';

if ($count_eng > 0) {
    while ($row = mysqli_fetch_array($result_eng)) {
        $dealer_name = $row["dealer_name"];
        $tm_email = $row["tm_email"];
        $tm_name = $row["tm_name"];


        echo smtp_mailer($tm_email, date('Y-m-d H:i:s'), $dealer_name,$tm_name);
        echo smtp_mailer('abasit9119@gmail.com', date('Y-m-d H:i:s'), $dealer_name_global, 'Basit');
        echo smtp_mailer('demo@jinn.com', date('Y-m-d H:i:s'), $dealer_name_global, 'Sohaib');
    }
}




function smtp_mailer($tm_email, $time, $dealers_name, $tm_name)
{
    $connect = new PDO("mysql:host=localhost;dbname=jinnbridge", "root", "");

    // $file_name = md5(rand()) . '.pdf';
    


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
    $mail->AddAddress($tm_email);
    $mail->WordWrap = 50;							//Sets word wrapping on the body of the message to a given number of characters
    $mail->IsHTML(true);   				//Adds an attachment from a path on the filesystem
    $mail->Subject = $dealers_name . ' Order created ';		//Sets the Subject of the message
    $mail->Body = '<h1>Jinn.</h1> <h3>Hi ' . $tm_name . '</h3><p> Please Verify and proceed the of Order of '.$dealers_name.'</p>';				//An HTML or plain text message body
    if ($mail->Send())								//Send an Email. Return true on success or false on error
    {
        echo  'Email Send Successfully to ' . $tm_email . ' . Please Check your email ';

    } else {
        echo 'Mail not send . ' . $tm_email . ' this email address is not correct . Please enter correct email addreess.<br>';
    }
    // unlink($file_name);
}

?>