<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $task_id = $_POST['task_id'];

    $recon_approval = isset($_POST['recon_approval']) ? $_POST['recon_approval'] : '0';
    $recon_approval = $recon_approval == '1' ? '1' : '0';

    $inspection = '0';
    $comment_recon = $_POST['comment_recon'];
    $comment_inspection = $_POST['comment_inspection'];
    $status = 1;
    $rm_id = $_POST['rm_id'];
    $app_dealer_id = $_POST['app_dealer_id'];


    $date = date('Y-m-d H:i:s');
    $val = '';

    // echo 'HAmza';



    $query = "UPDATE `inspector_task_response`
    SET
    `recon_approval` = '$recon_approval',
    `inspection` = '$inspection',
    `comment_inspection` = '$comment_inspection',
    `comment_recon` = '$comment_recon',
    `approved_status` = '$status',
    `approved_at` = '$date',
    `approved_by` = '$rm_id'
    WHERE `task_id` = '$task_id';";


    if (mysqli_query($db, $query)) {

        $query1 = "UPDATE `inspector_task`
            SET
            `approve_status` = '$status',
            `approved_decline_time` = '$date'
            WHERE `id` = '$task_id';";


        if (mysqli_query($db, $query1)) {

            $query2 = "INSERT INTO `inspection_approved_log`
            (`task_id`,
            `dealer_id`,
            `inspection_status`,
            `inspection_commet`,
            `recon_status`,
            `recon_comment`,
            `approved_at`,
            `approved_by`)
            VALUES
            ('$task_id',
            '$app_dealer_id',
            '$inspection',
            '$comment_inspection',
            '$recon_approval',
            '$comment_recon',
            '$date',
            '$rm_id');";
            mysqli_query($db, $query2);
            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query1;

        }


        // $output = 1;

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>