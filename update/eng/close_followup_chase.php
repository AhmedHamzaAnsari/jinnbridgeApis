<?php
include("../../config.php");
session_start();
if (isset($_POST)) {


   $message_des=$_POST['message_des'];
   $action_id=$_POST['action_id'];
   $user_id=$_POST['user_id'];
   
   $file = rand(1000, 100000) . "-" . $_FILES['action_file']['name'];
    $file_loc = $_FILES['action_file']['tmp_name'];
    $file_size = $_FILES['action_file']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc, $folder . $file);

   $date = date('Y-m-d H:i:s');
    // echo 'HAmza';

    $query = "UPDATE `follow_ups_eng`
    SET
    `action_user_id` = '$user_id',
    `action_files` = '$file',
    `action_time` = '$date',
    `action_description` = '$message_des',
    `status` = '1'
    WHERE `id` = '$action_id';";


    if (mysqli_query($db, $query)) {

        $output= 1;
    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }

    echo $output;
}
?>
