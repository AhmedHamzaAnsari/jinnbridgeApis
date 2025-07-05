<?php
include("../../config.php");
session_start();
// error_reporting(E_ALL & ~E_WARNING);

if (isset($_POST)) {
    // ini_set('max_input_vars', 3000);
    $user_id = $_POST['user_id'];
    $dealers = $_POST["c_user_id"];
    $description =  $_POST["description"];


    $userData = count($_POST["dealers_id"]);
    $date = date('Y-m-d H:i:s');

    // echo 'HAmza';
    // print_r( $_POST['text_checkbox']);

    if ($_POST["row_id"] != '') {


    } else {

        for ($i = 0; $i < $userData; $i++) {
            $dealer_checkbox = $_POST['text_checkbox'][$i];
            // echo $dealer_checkbox;

            if ($dealer_checkbox !='0') {
                $inspection_date = $_POST['inspection_date'][$i];
                $pump_id = $_POST['dealers_id'][$i];
                // echo $inspection_date.' '.$pump_id;

                $query = "INSERT INTO `inspector_task`
                (`user_id`,
                `dealer_id`,
                `type`,
                `time`,
                `description`,
                `created_at`,
                `created_by`)
                VALUES
                ('$dealers',
                '$pump_id',
                'Inpection',
                '$inspection_date',
                '$description',
                '$date',
                '$user_id');";


                if (mysqli_query($db, $query)) {


                    $output = 1;

                } else {
                    $output = 'Error' . mysqli_error($db) . '<br>' . $query;

                }

            }

        }

    }



    echo $output;
}
?>