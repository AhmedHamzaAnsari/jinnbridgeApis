<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
$tm_id = $_GET["tm_id"];

    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM `users_asm_tm` as zt join users as us on us.id=zt.asm_id where zt.tm_id=$tm_id;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

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
