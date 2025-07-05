<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $myusername = $_GET['username'];
        $mypassword = $_GET['password'];

        $sql_query1 = "SELECT * FROM dealers where contact='$myusername' and password='$mypassword';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $id = $user['id'];
            $status = $user['indent_price'];
            $verify_code = $user['Nozel_price'];
            $contact = $user['contact'];
            if ($status != 0) {
                $thread[] = $user;
                if ($id != 41) {

                    $random_number = rand(1000, 9999);
                    // echo $random_number;
                    $update = "UPDATE `dealers`
                    SET
                    `Nozel_price` = '$random_number'
                    WHERE `id` = '$id';";

                    if (mysqli_query($db, $update)) {
                        // $code_r = sent_message($contact, $random_number);

                        $output = 1;

                    } else {
                        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

                    }

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

    // echo "Hamza";
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://connect.jazzcmt.com/sendsms_url.html?Username=03022026441&Password=Jazz%40123&From=&To=' . $contact . '&Message=Your Verification Code : ' . $code . '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;

}

?>