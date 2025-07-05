<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$task_id = $_GET["task_id"];
if ($pass != '') {
    $id = $_GET["id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT it.*,us.name as username FROM inspector_task_reschedule as it
        join users as us on us.id=it.created_by where it.task_id='$task_id' order by it.id desc;";

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