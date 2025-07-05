<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $nozel_id = $_GET["nozel_id"];
    $task_id = $_GET["task_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM dealer_stock_recon_new_files where task_id='$task_id' and nozel_id='$nozel_id' order by id desc;";

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