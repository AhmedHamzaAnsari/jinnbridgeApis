<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $dealer_id = $_POST['dealer_id'];
    $task_id = $_POST['task_id'];
    $datetime = date('Y-m-d H:i:s');

    $appreation = $_POST["appreation"];
    $measure_taken = $_POST['measure_taken'];
    $warning = $_POST["warning"];
    $pmg_ogra_price = $_POST["pmg_ogra_price"];
    $pmg_pump_price = $_POST["pmg_pump_price"];
    $pmg_variance = $_POST["pmg_variance"];
    $hsd_ogra_price = $_POST["hsd_ogra_price"];
    $hsd_pump_price = $_POST["hsd_pump_price"];
    $hsd_variance = $_POST["hsd_variance"];
    $dispenser_measre = $_POST["dispenser_measre"];


    // $product = '[{"dispenser_id":"1","pmg_accurate":"25","pmg_shortage":"292","hsd_accurate":"HSD","hsd_shortage":7300},{"dispenser_id":"2","pmg_accurate":"32","pmg_shortage":"280","hsd_accurate":"PMG","hsd_shortage":8960},{"dispenser_id":"3","pmg_accurate":"12","pmg_shortage":"300","hsd_accurate":"HOBC","hsd_shortage":3600}]';

    if ($dealer_id != "") {
        if ($_POST["row_id"] != '') {


        } else {


            $query_main = "INSERT INTO `dealer_measurement_pricing_action`
            (`appreation`,
            `task_id`,
            `dealer_id`,
            `measure_taken`,
            `warning`,
            `pmg_ogra_price`,
            `pmg_pump_price`,
            `pmg_variance`,
            `hsd_ogra_price`,
            `hsd_pump_price`,
            `hsd_variance`,
            `created_at`,
            `created_by`)
            VALUES
            ('$appreation',
            '$task_id',
            '$dealer_id',
            '$measure_taken',
            '$warning',
            '$pmg_ogra_price',
            '$pmg_pump_price',
            '$pmg_variance',
            '$hsd_ogra_price',
            '$hsd_pump_price',
            '$hsd_variance',
            '$datetime',
            '$user_id');";

            if (mysqli_query($db, $query_main)) {
                $active = mysqli_insert_id($db);

                $dataArray = json_decode($dispenser_measre, true);

                // Check if the decoding was successful
                if (is_array($dataArray)) {
                    // Iterate through the array using a foreach loop
                    foreach ($dataArray as $item) {


                        $dispenser_id = $item['dispenser_id'];
                        $pmg_accurate = $item['pmg_accurate'];
                        $pmg_shortage = $item['pmg_shortage'];
                        $hsd_accurate = $item['hsd_accurate'];
                        $hsd_shortage = $item['hsd_shortage'];
                        if ($dispenser_id != "") {
                            $sql1 = "INSERT INTO `dealer_measurement_pricing`
                            (`dispenser_id`,
                            `main_id`,
                            `dealer_id`,
                            `pmg_accurate`,
                            `shortage_pmg`,
                            `hsd_accurate`,
                            `shortage_hsd`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$dispenser_id',
                            '$active',
                            '$dealer_id',
                            '$pmg_accurate',
                            '$pmg_shortage',
                            '$hsd_accurate',
                            '$hsd_shortage',
                            '$datetime',
                            '$user_id');";

                            if (mysqli_query($db, $sql1)) {
                                $output = 1;

                            }

                        }

                    }
                } else {
                    echo "Failed to decode JSON string.";
                }




            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $query_main;

            }
        }

    } else {
        $output = 0;
    }




    echo $output;
}
?>