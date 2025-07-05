<?php
include("../config.php");
session_start();
if (isset($_POST)) {


    $user_id = $_POST['user_id'];
    $ledger_amount = $_POST['ledger_amount'];

    $ledger_old_value = mysqli_real_escape_string($db, $_POST['ledger_old_value']);
    $dealer_id = mysqli_real_escape_string($db, $_POST['dealer_id']);
    $ledger_description = mysqli_real_escape_string($db, $_POST['ledger_description']);
    $actione_time = mysqli_real_escape_string($db, $_POST['actione_time']);


    $datetime = date('Y-m-d H:i:s');

    // echo 'HAmza';



    $query = "UPDATE `dealers` 
    SET `acount` = `acount` + $ledger_amount 
    WHERE id = $dealer_id";


    if (mysqli_query($db, $query)) {


        $log = "INSERT INTO `dealer_ledger_log`
        (`dealer_id`,
        `type`,
        `old_ledger`,
        `new_ledger`,
        `datetime`,
        `description`,
        `created_at`,
        `created_by`)
        VALUES
        ('$dealer_id',
        'Primary',
        '$ledger_old_value',
        '$ledger_amount',
        '$actione_time',
        '$ledger_description',
        '$datetime',
        '$user_id');";
        if (mysqli_query($db, $log)) {
            $output = 1;

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $log;

        }

    } else {
        $output = 'Error' . mysqli_error($db) . '<br>' . $query;

    }




    echo $output;
}
?>