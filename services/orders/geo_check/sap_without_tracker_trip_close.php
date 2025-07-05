<?php
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 20; URL=$url1");
// error_reporting(0);

include ("../../../config.php");
set_time_limit(5000);

echo "<h1>Sap With-out Tracker Trip Close service .</h1><br>";

$sql = "SELECT oi.*,dl.`co-ordinates` as co,dc.id as vehicle_id FROM order_info as oi 
join dealers as dl on dl.sap_no=oi.customer_id
left join devicesnew as dc on TRIM(SUBSTRING_INDEX(dc.name, ' ', 1))=oi.vehicle 
where oi.status=1 and oi.is_tracker=0 and  oi.created_at>='2024-05-22 00:00:00' order by oi.eta desc";

$result = mysqli_query($db, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

$count = mysqli_num_rows($result);

if ($count > 0) {
    while ($row = mysqli_fetch_array($result)) {
        $co = $row['co'];
        $sub_order_id = $row['id'];
        $v_num = $row['vehicle'];
        $vehicle_id = $row['vehicle_id'];
        $eta = $row['eta'];

        $sql_update = "UPDATE order_info SET close_time='$eta', status=2 WHERE id='$sub_order_id'";
        if (mysqli_query($db, $sql_update)) {
            echo "Trip Closed successfully !";
        } else {
            echo "Error: " . $sql_update . " " . mysqli_error($db);
        }
    }
} else {
    echo '<h1>No Records Found to send Msg</h1>';
}



mysqli_close($db);
echo "Last Run " . date('Y-m-d H:i:s');
?>