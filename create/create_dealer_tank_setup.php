<?php
include("../config.php");
session_start();

if (isset($_POST)) {
    $user_id = mysqli_real_escape_string($db, $_POST["user_id"]);
    $response = $_POST["response"];
    $dealer_id = $_POST["dealer_id"];
    $no_tanks = $_POST["no_tanks"];
    $piping_from_fill_point_to_tanks = $_POST["piping_from_fill_point_to_tanks"];
    $piping_from_tank_to_dispenser_units = $_POST["piping_from_tank_to_dispenser_units"];
    $off_set_tank_dec_area_unit = $_POST["off_set_tank_dec_area_unit"];
    $tank_connected_earth = $_POST["tank_connected_earth"];
    $vent_pip_connected_tank = $_POST["vent_pip_connected_tank"];
    $dip_cap_install_tank = $_POST["dip_cap_install_tank"];
    $date = date('Y-m-d H:i:s');


    $datetime = date('Y-m-d H:i:s');

    $query_main = "INSERT INTO `dealer_tank_setup`
    (`dealer_id`,
    `no_tanks`,
    `json_data`,
    `piping_from_fill_point_to_tanks`,
    `piping_from_tank_to_dispenser_units`,
    `off_set_tank_dec_area_unit`,
    `tank_connected_earth`,
    `vent_pip_connected_tank`,
    `dip_cap_install_tank`,
    `created_at`,
    `created_by`)
    VALUES
    ('$dealer_id',
    '$no_tanks',
    '$response',
    '$piping_from_fill_point_to_tanks',
    '$piping_from_tank_to_dispenser_units',
    '$off_set_tank_dec_area_unit',
    '$tank_connected_earth',
    '$vent_pip_connected_tank',
    '$dip_cap_install_tank',
    '$date',
    '$user_id');";

    if (mysqli_query($db, $query_main)) {
        $output = 1;

    } else {
        $output = 0;
    }
}


echo $output;




?>