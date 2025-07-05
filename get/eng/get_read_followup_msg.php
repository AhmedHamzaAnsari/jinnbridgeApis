<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
if ($pass != '') {
    if ($pass == $access_key) {

        if ($pre != 'Admin') {
            $dpt_id = $_GET["dpt_id"];
            $sql_query1 = "SELECT us.name as username,fu.id as ticket_no,nn.created_at FROM followup_notification_eng as nn
            join follow_ups_eng as fu on fu.id=nn.followup_id 
            join users as us on us.id=nn.created_by where fu.dpt_id=$dpt_id and nn.status=1 and nn.created_at>=curdate() order by nn.id desc;";
        } else {
            $sql_query1 = "SELECT us.name as username,fu.id as ticket_no,nn.created_at FROM followup_notification_eng as nn
            join follow_ups_eng as fu on fu.id=nn.followup_id 
            join users as us on us.id=nn.created_by where nn.status=1 and nn.created_at>=curdate() order by nn.id desc;";
        }

        

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