<?php
include("../config.php");
// session_start();


if (isset($_POST)) {
    $user_id = $_POST['user_id'];

    $dealers_count = count($_POST['dealers']);


    $zm = $_POST['zm'];
    $tm = $_POST['tm'];
    $asm = $_POST['asm'];


    $date = date('Y-m-d H:i:s');
    $output = '';
    // echo 'HAmza';
    if ($dealers_count > 0) {
        for ($i = 0; $i < $dealers_count; $i++) {
            $dealers = $_POST["dealers"][$i];

            $query = "UPDATE `dealers`
                SET
                `zm` = '$zm',
                `tm` = '$tm',
                `asm` = '$asm'
                WHERE `id` = '$dealers';";

            if (mysqli_query($db, $query)) {
                $output = 1;

            } else {

                $output = 'Error' . mysqli_error($db) . '<br>' . $query;
            }


        }
    }




    echo $output;
}
?>