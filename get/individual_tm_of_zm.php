<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
$zm_id = $_GET["zm_id"];

    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM `users_zm_tm` as zt join users as us on us.id=zt.tm_id where zt.zm_id='$zm_id';";

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


?>