<?php
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'sitara');
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
echo "<h1>Fence Check OUT service .</h1><br>";

$vehicle_name;
$sql = "SELECT * FROM sitara.geo_in_check where log='0'";
$result = mysqli_query($db, $sql);
$count = mysqli_num_rows($result);
// echo $count;
if ($count > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $v_lat = $row["latitude"];
        $v_lng = $row["longitude"];
        $v_num = $row["car_name"];
        $v_id = $row["veh_id"];
        $geo_id = $row["geo_id"];
        $Coordinates = $row["Coordinates"];
        $in_time = $row["in_time"];
        $check_in_id = $row["id"];
        $c_name = $row["consignee_name"];
        $geotype = $row["geotype"];

        $chars = explode(',', $Coordinates);

        $c_lat = $chars[0];
        $c_lng = $chars[1];

        // $km = 0.155;
        $km = 0;

        if ($geotype != "depot") {
            $km = 0.155;

        } else {
            $km = 5;

        }

        $ky = 40000 / 360;
        $kx = cos(pi() * $c_lat / 180.0) * $ky;
        $dx = abs($c_lng - $v_lng) * $kx;
        $dy = abs($c_lat - $v_lat) * $ky;

        if (sqrt(($dx * $dx) + ($dy * $dy)) <= $km == false) {
            // echo ($v_lat + "," + $v_lng + "," + $v_id + "     " + $c_lat + "," + $c_lng + "," + $v_num + "," + $id + "," + $c_name + "," + $location);
            $out_time = date('Y-m-d H:i:s');


            echo '<br/>';

            echo 'IN TIME =>' . $in_time;
            echo '<br/>';

            echo '<br/>';

            echo 'Out TIME =>' . $out_time;
            echo '<br/>';

            $to_time = strtotime($in_time);
            $from_time = strtotime($out_time);
            $hours = round(abs($to_time - $from_time) / 60, 2);

            update($v_id, $geo_id, $out_time, $in_time, $hours, $check_in_id);

        } else {

            echo '<br/>';
            echo 'Consignee name = ' . $c_name;
            echo '<br/>';
            echo '<br/>';
            echo 'name = ' . $v_num . ' id = ' . $v_id;
            echo '<br/>';
            echo 'Lat Lng => ' . $v_lat . ' ' . $v_lng;
            echo '<br/>';

            echo 'In time =>' . $in_time;
            echo '<br/>';

            echo '<br/>';
            echo 'Not Out';
            echo '<br/>';
            echo '------------------------------------------------------------------------';
            echo '<br/>';


        }

    }

} else {
    echo '<h1>No Records Found </h1>';
}

function update($v_id, $geo_id, $out_time, $in_time, $diff_hours, $check_in_id)
{

    $dbss = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    $sql_update = "UPDATE sitara.geo_check set out_time='$out_time' , log=1,in_duration='$diff_hours' where veh_id='$v_id' and geo_id='$geo_id' and id='$check_in_id'";
    if (mysqli_query($dbss, $sql_update)) {
        echo "Log update successfully !";

        $sql_insert = "INSERT INTO sitara.geo_check_audit (`veh_id`,`geo_id`,`in_time`,`out_time`,`in_duration`) VALUES ('$v_id','$geo_id','$in_time',' $out_time','$diff_hours');";
        if (mysqli_query($dbss, $sql_insert)) {
            echo "New record created successfully !";
        } else {
            echo "Error: " . $sql_insert . "
               " . mysqli_error($dbss);
        }
    } else {
        echo "Error: " . $sql_update . "" . mysqli_error($dbss);
    }

}
mysqli_close($db);
?>