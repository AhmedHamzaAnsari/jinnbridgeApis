<?php
include("../config.php");

header('Content-Type: application/json');
date_default_timezone_set('Asia/Karachi');

$sql = "SELECT * FROM work_order ORDER BY created_at DESC";
$result = $db->query($sql);

$data = [];
$sn = 1;
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'sno'                  => $sn++,
        'order_num'           => $row['order_num'],
        'date_issued'         => $row['date_issued'],
        'requested_by'        => $row['requested_by'],
        'desgination'         => $row['desgination'],
        'station_location'    => $row['station_location'],
        'dealer'              => $row['dealer'],
        'equip_name'          => $row['equip_name'],
        'quantity'            => $row['quantity'],
        'unit'                => $row['unit'],
        'date_completed'      => $row['date_completed'],
        'created_at'          => $row['created_at'],
        'description_of_work' => $row['description_of_work'],
        'priority'            => $row['priority'],
        'start_date'          => $row['start_date'],
        'remarks'             => $row['remarks']
    ];
    
}

echo json_encode(['data' => $data]);
?>
