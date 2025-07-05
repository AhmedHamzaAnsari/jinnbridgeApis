<?php
include("../../config.php");
session_start();
// error_reporting(E_ALL & ~E_WARNING);

if (isset($_POST)) {
    // ini_set('max_input_vars', 3000);
    $user_id = $_POST['user_id'];
    // $dealers = mysqli_real_escape_string($db, $_POST["dealers"]);
    $description = mysqli_real_escape_string($db, $_POST["description"]);


    // $userData = count($_POST["dealers_id"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    // print_r( $_POST['text_checkbox']);

    if ($_POST["row_id"] != '') {


    } else {


        $inspection_date = $_POST['inspection_date'];
        $pump_id = $_POST['dealers_id'];
        $type = $_POST['type'];
        // echo $inspection_date.' '.$pump_id;

        $query = "INSERT INTO `eng_inspector_task`
                (`user_id`,
                `dealer_id`,
                `type`,
                `time`,
                `description`,
                `created_at`,
                `created_by`)
                VALUES
                ('$user_id',
                '$pump_id',
                '$type',
                '$inspection_date',
                '$description',
                '$date',
                '$user_id');";


        if (mysqli_query($db, $query)) {

            $output = mysqli_insert_id($db);

            // $output = 1;
          


        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }





    }



    echo $output;
}
?>