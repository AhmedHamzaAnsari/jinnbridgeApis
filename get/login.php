<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $myusername = $_GET['username'];
        $mypassword = $_GET['password'];

        $sql = "SELECT * FROM users WHERE login = '$myusername' and description = '$mypassword'";

        // echo $sql;

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $count = mysqli_num_rows($result);
        // echo $count;
        $response = new stdClass(); 
        if ($count > 0) {
            $status = $row['status'];
            if ($status != '1') {
                $response->result = 2;
                $response->data = '';
            } else {

                $response->result = 1;
                $response->data = $row;



            }
        } else {
            $response->result = 0;
            $response->data = '';
        }
        echo json_encode($response);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>