<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$sap = $_GET["sap"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM request_for_dealers_ledger where customer_sap=$sap order by id desc;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>