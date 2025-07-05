<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $color = mysqli_real_escape_string($db, $_POST["color"]);
    $date = date('Y-m-d H:i:s');

    $file = rand(1000, 100000) . "-" . $_FILES['logo']['name'];
    $file_loc = $_FILES['logo']['tmp_name'];
    $file_size = $_FILES['logo']['size'];
    //  $file_type = $_FILES['file']['type'];
    $folder = "../../jinnBridge_files/uploads/";
    move_uploaded_file($file_loc, $folder . $file);

    $tdate = date('Y-m-d H:i:s');
    $num = mt_rand(100000, 999999);
    // printf("%d", $num);

    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $query = "INSERT INTO `settings`
        (`name`,
        `color`,
        `logo`,
        `created_at`,
        `created_by`)
        VALUES
        ('$name',
        '$color',
        '$file',
        '$date',
        '$user_id');";


        if (mysqli_query($db, $query)) {


            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }



    echo $output;
}
?>