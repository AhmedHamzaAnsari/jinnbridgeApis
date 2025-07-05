<?php
include("../config.php");
// session_start();


if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $nozel_counts = count($_POST['nozzels_id']);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $tank_id = mysqli_real_escape_string($db, $_POST["tank_id"]);
    $date = date('Y-m-d H:i:s');
    $output = '';
    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        if ($nozel_counts > 0) {
            for ($i = 0; $i < $nozel_counts; $i++) {
                $nozzels_id = $_POST["nozzels_id"][$i];

                $query = "INSERT INTO `dealers_tanks_nozels`
                (`tank_id`,
                `nozel_id`,
                `dealer_id`,
                `created_at`,
                `created_by`)
                VALUES
                ('$tank_id',
                '$nozzels_id',
                '$dealer_id',
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