<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $followup_id = $_GET["followup_id"];
        $sql_query1 = "SELECT cl.*,us.name,us.privilege FROM followup_catlog_eng as cl
        join users as us on us.id=cl.user_id
        where cl.followup_id=$followup_id order by cl.id desc;";

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