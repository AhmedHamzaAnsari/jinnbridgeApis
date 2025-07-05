<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $task_id = $_POST['task_id'];
    $dealer_id = $_POST['dealer_id'];
    $date = date('Y-m-d H:i:s');
    $val = '';

    // echo 'HAmza';



    $query = "UPDATE `inspector_task`
    SET
    `approve_status` = '1',
    `approved_decline_time` = '$date'
    WHERE `id` = '$task_id' and dealer_id = '$dealer_id';";


    if (mysqli_query($db, $query)) {

        
        $output = 1;
       
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>