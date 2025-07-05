<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Karachi");
// $username = "root";
// $password = "";
// $database = "jinnbridge";
// $db = mysqli_connect('localhost', $username, $password, $database);
define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'Jinn');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
if (!$db) {
    die('Not connected : ' . mysqli_error($db));
}
set_time_limit(100000);
if (isset($_POST)) {
    $users_arr_date = array();
    $mile = 0;
    $pre_mile = 0;
    $next_mile = 0;
    $final_mile = 0;

    $first_start = '';
    $last_stop = '';
    $start_time;
    $vehicle_name = '';
    $pre_time = 0;
    $final_time = 0;
    $total_stop_time = 0;

    $idel_first_start = '';
    $idel_last_stop = '';
    $idel_start_time;
    $idel_vehicle_name;
    $idel_pre_time = 0;
    $idel_final_time = 0;
    $idel_total_stop_time = 0;

    $start_speed;
    $next_speed = 0;
    $pre_speed = 0;
    $total_event;
    $min_ = 0;
    $max_ = 0;
    $Averagespeed = 0;
    $calculated_odo_ = 0;
    $location;
    $time_;
    $lati;
    $lngi;
    $last_move = '';
    $overspeed = 0;
    $overspeed_1 = 0;
    $overspeed_2 = 0;

    $all_users = "SELECT * FROM Jinn.users where privilege='admin' or privilege='Cartraige'";
    $result_all_users = $db->query($all_users) or die("Error :" . mysqli_error($db));

    while ($user_row = $result_all_users->fetch_assoc()) {

        $user = $user_row['id'];

        $sql_car = "SELECT id as uniqueId,name,time,latestPosition_id FROM Jinn.devicesnew as dc 
        join Jinn.users_devices_new as ud on ud.devices_id=dc.id
        where dc.speed>0 and dc.ignition='On' and dc.time>= DATE_SUB(NOW(), INTERVAL 24 HOUR) and ud.users_id=$user;";
        $result_car = mysqli_query($db, $sql_car);

        while ($row_car = mysqli_fetch_array($result_car)) {

            $car_id = $row_car['uniqueId'];
            echo '-----------------------------------'.$row_car['name'].'----------------------------------------<br>';
            // $users_arr = array();
            // $sql="SELECT * FROM `positions` where time>='$from' and time<='$to' and device_id=$vehicle";DATE_FORMAT(time,'%H:%i:%s')
            $sql = "SELECT time,speed,power,odometer as imileage ,device_id as vehicle_id,vehicle_name FROM Jinn.positionsnew where device_id='$car_id' and time >= DATE_SUB(NOW(), INTERVAL 250 MINUTE) order by time asc";

            // echo $sql;
            $result = mysqli_query($db, $sql);
            $number = mysqli_num_rows($result);
            if ($number > 0) {

                $j = 1;

                $i = 0;
                $k = 0;
                $l = 0;

                while ($row = mysqli_fetch_array($result)) {

                    $speed = $row['speed'];
                    $time = $row['time'];
                    $power = $row['power'];
                    $imileage = $row['imileage'];
                    $vehicle_id = $row['vehicle_id'];
                    $vehicle_name = $row['vehicle_name'];
                    $start_speed = $speed;
                    $start_time = $time;
                   

                    if ($speed > 55) {
                        if ($overspeed_1 > 55) {


                        } else {
                            $overspeed_1 = $speed;
                            $overspeed++;
                        }
                    } else {
                        $overspeed_1 = $speed;

                    }

                    // -------------------------------------------------------------------------------------------------------------------------------
                    if ($start_speed > 0) {

                        if ($i == 0) {
                            $pre_time = $time;
                        }
                        // echo $time . ' => ' . $speed . '<br>';
                        $d1 = strtotime($start_time);
                        $d2 = strtotime($pre_time);

                        $totalSecondsDiff = abs($d1 - $d2); //42600225
                        // echo $start_time . ' - ' . $pre_time . '<br>';
                        $totalMinutesDiff = $totalSecondsDiff / 60;
                        // echo 'Diff => ' . $totalMinutesDiff . '<br>';
                        $final_time = $final_time + $totalMinutesDiff;
                       


                        $pre_speed = $start_speed;
                        $pre_time = $start_time;
                        $i++;
                        $pre_speed = $start_speed;
                        $pre_time = $start_time;
                    } else {
                        // $pre_speed = $time;
                        $pre_time = $start_time;
                    }
                    // ---------------------------------------------------------------------------------------------------------------------------------
                    if ($start_speed == 0) {
                        break;
                        // $idel_start_time = $time;

                        // if ($k == 0) {
                        //     $idel_pre_time = $time;
                        // }
                        // // echo $time . ' => ' . $speed . '<br>';
                        // $idel_d1 = strtotime($idel_start_time);
                        // $idel_d2 = strtotime($idel_pre_time);

                        // $idel_totalSecondsDiff = abs($idel_d1 - $idel_d2); //42600225
                        // // echo $idel_start_time . ' - ' . $idel_pre_time . '<br>';
                        // $idel_totalMinutesDiff = $idel_totalSecondsDiff / 60;
                        // // echo 'Diff => ' . $idel_totalMinutesDiff . '<br>';
                        // $idel_final_time = $idel_final_time + $idel_totalMinutesDiff;
                        // // echo 'cal => ' . $idel_final_time . '<br>';
                        // $idel_pre_time = $idel_start_time;
                        // $k++;

                    }


                    if ($j == $number) {
                        $last_stop = $time;



                    } else if ($j == 1) {
                        $first_start = $time;

                    }



                    $j++;


                }





                echo 'cal => ' . $final_time . '<br>';

                $c_d1 = strtotime($first_start);
                $c_d2 = strtotime($last_stop);

                $c_totalSecondsDiff = abs($c_d2 - $c_d1); //42600225
                // echo $start_time . ' - ' . $pre_time . '<br>';
                $c_totalMinutesDiff = $c_totalSecondsDiff / 60;
                // echo 'Total time => ' . $c_totalMinutesDiff . '<br>';
                // echo 'Idel Duration => ' . convert_time($idel_final_time * 60) . '<br>';
                // echo 'Stop Duration => ' . convert_time($total_stop_time * 60) . '<br>';
                // echo 'Moving Duration => ' . convert_time($final_time * 60) . '<br>';

                if ($final_time > 240) {
                    $extra_driving = $final_time-240;

                    $checking_time = "SELECT * FROM Jinn.axcess_driving_alerts where  vehicle_id='$car_id'  and created_by='$user' and created_at>=curdate() order by id desc limit 1;";
                    $result_checking_time = mysqli_query($db, $checking_time);

                    $count_checking_time = mysqli_num_rows($result_checking_time);
                    if ($count_checking_time > 0) {
                        $row_checking_time = mysqli_fetch_array($result_checking_time);

                        $last_alert_time = $row_checking_time['created_at'];
                        $cur_time = date("Y-m-d H:i:s");
                        $to_time = strtotime($cur_time);
                        $from_time = strtotime($last_alert_time);
                        $diff = round(abs($to_time - $from_time) / 60, 2);
                        if ($diff > 60) {
                            $message = "" . $vehicle_name . " has exceeded Driving limit by " . round($extra_driving) . " minutes of allow driving time.";

                            $insert_alert = "INSERT INTO `jinnbridge`.`axcess_driving_alerts`
                            (`vehicle_id`,
                            `vehicle_name`,
                            `message`,
                            `duration`,
                            `created_at`,
                            `created_by`)
                            VALUES
                            ('$car_id',
                            '$vehicle_name',
                            '$message',
                            '" . round($extra_driving) . "',
                            '" . date('Y-m-d H:i:s') . "',
                            '$user');";

                            mysqli_query($db, $insert_alert);
                        }

                    } else {
                        $message = "" . $vehicle_name . " has exceeded Driving limit by " . round($extra_driving) . " minutes of allow driving time.";

                        $insert_alert = "INSERT INTO `jinnbridge`.`axcess_driving_alerts`
                        (`vehicle_id`,
                        `vehicle_name`,
                        `message`,
                        `duration`,
                        `created_at`,
                        `created_by`)
                        VALUES
                        ('$car_id',
                        '$vehicle_name',
                        '$message',
                        '" . round($extra_driving) . "',
                        '" . date('Y-m-d H:i:s') . "',
                        '$user');";

                        mysqli_query($db, $insert_alert);
                    }

                    $users_arr_date[] = array(
                        'date' => date('Y-m-d'),
                        'vehicle_name' => $vehicle_name,
                        'car_id' => $car_id,
                        'start_time' => date("h:i A", strtotime($first_start)),
                        'end_time' => date("h:i A", strtotime($last_stop)),
                        'moving_duration_mint' => round($final_time),



                    );
                }



                // echo json_encode($users_arr);
                // $users_arr_date[] = array($users_arr);


                $final_time = 0;
                $idel_final_time = 0;
                $final_mile = 0;
                $calculated_odo_ = 0;
                // $l=0;
            }

        }

    }


    echo json_encode($users_arr_date);
}

mysqli_close($db);


function displayDates($date1, $date2, $format = 'Y-m-d')
{
    $dates = array();
    $current = strtotime($date1);
    $date2 = strtotime($date2);
    $stepVal = '+1 day';
    while ($current <= $date2) {
        $dates[] = date($format, $current);
        $current = strtotime($stepVal, $current);
    }
    return $dates;
}

function convert_time($mili)
{

    $init = $mili;
    $hours = floor($init / 3600);
    $minutes = floor(($init / 60) % 60);
    $seconds = $init % 60;

    // echo "$hours:$minutes:$seconds";
    $con = $hours . ':' . $minutes . ':' . $seconds . '';
    return $con;

}


?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="refresh" content="30">
    <title>Excess Driving Alert SERVICE</title>
</head>

<body style="background: #fff;">
    <br>
    <?php echo date("d-m-Y H:i:s", time()); ?>
</body>

</html>