<?php
include ("../config.php");
session_start();
if (isset ($_POST)) {


    $name = $_POST['name'];
    $location = $_POST['location'];

    $file1 = rand(1000, 100000) . "-" . $_FILES['image']['name'];
    $file_loc1 = $_FILES['image']['tmp_name'];
    $file_size1 = $_FILES['image']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder1 = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc1, $folder1 . $file1);




    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';




    $log = "INSERT INTO `offline_clusters`
        (`name`,
        `location`,
        `image`)
        VALUES
        ('$name',
        '$location',
        '$file1');";
    if (mysqli_query($db, $log)) {
        $output = 1;

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $log;

    }






    echo $output;
}
?>