<?php
include("../../config.php");
session_start();
if (isset($_POST)) {



    $task_id = $_POST['task_id'];
    $table_name = $_POST['table_name'];






    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `eng_inspector_task` SET 
    `$table_name`='1'
    WHERE id=$task_id";


    if (mysqli_query($db, $query)) {
        $output = 1;

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>