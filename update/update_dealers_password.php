<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $row_id = $_POST['row_id'];
    $edit_password = $_POST['edit_password'];


    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';

    $query = "UPDATE `dealers` SET 
    `password`='$edit_password'
    WHERE id=$row_id";


    if (mysqli_query($db, $query)) {
        $output = 1;


    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>