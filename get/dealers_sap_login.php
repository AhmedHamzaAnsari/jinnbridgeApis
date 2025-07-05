<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $myusername = $_GET['username'];
        $mypassword = $_GET['password'];

        $sql_query1 = "SELECT * FROM dealers where sap_no='$myusername' and password='$mypassword';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $id = $user['id'];
            $status = $user['indent_price'];
            $verify_code = $user['Nozel_price'];
            $contact = $user['contact'];
            if ($status != 0) {
                $thread[] = $user;

                $random_number = rand(1000, 9999);
                // echo $random_number;
                $update = "UPDATE `dealers`
                    SET
                    `Nozel_price` = '1234'
                    WHERE `id` = '$id';";

                if (mysqli_query($db, $update)) {
                    // $code_r = sent_message($contact, $random_number);
                    // echo $code_r;
                    $output = 1;

                } else {
                    $output = 'Error' . mysqli_error($db) . '<br>' . $query;

                }



            } else {
                array_push($thread, "Your Are Not Verified");
            }
        }
        echo json_encode($thread);


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


function sent_message($contact, $code)
{
    $url = 'https://connect.jazzcmt.com/sendsms_url.html?' . http_build_query([
        'Username' => '03205471221',
        'Password' => 'Jazz@123423423',
        'From' => 'ASLAM SONS',
        'To' => $contact,
        'Message' => 'Your OTP Code : ' . $code
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30, // Increase timeout
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
        CURLOPT_SSL_VERIFYHOST => false, // Disable SSL host verification
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);

    curl_close($curl);

    if ($error) {
        return "cURL Error: " . $error;
    }

    return $response;
}

// Example call
// echo send_message('03001234567', '1234');


?>