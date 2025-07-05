<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $varify_code = $_GET["varify_code"];
    $e_id = $_GET["e_id"];
    // $id = $_GET["id"];
    $current_date = date('Y-m-d');
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * 
        FROM recon_email_link 
        WHERE  email = '$e_id' 
          AND password = '$varify_code' 
          AND DATE(`from`) <= '$current_date' 
          AND DATE(`to`) >= '$current_date';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} 
else 
{
    echo 'Key is Required';
}


?>