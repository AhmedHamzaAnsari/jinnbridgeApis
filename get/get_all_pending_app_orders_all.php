<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
$rettype = $_GET["rettype"];
if (!empty($pass)) {
    if ($pass === $access_key) {

        $condition = '';
        if ($pre === 'ZM') {
            $condition = "dl.zm = $id";
        } elseif ($pre === 'TM') {
            $condition = "dl.tm = $id";
        } elseif ($pre === 'ASM') {
            $condition = "dl.asm = $id";
        } else {
            $condition = "1 = 1"; // Fallback condition if no matching privilege is found
        }
        // $rettype_condition = ($rettype !== 'RT') ? "AND dl.rettype = '$rettype'" : '';

        $sql_query1 = "SELECT 
                om.*, 
                dl.name, 
                dl.zm, 
                dl.tm, 
                dl.asm, 
                dl.region, 
                dl.city, 
                dl.province, 
                dl.district, 
                us.name as usersnames, 
                dl.credit_limit, 
                dl.rettype_desc, 
                dl.sap_no,
                (SELECT CONCAT(geo.code, '-', geo.consignee_name) 
                 FROM puma_sap_data_trips as tt
                 JOIN puma_sap_data as sd on sd.id = tt.main_id
                 JOIN geofenceing as geo on geo.code = sd.depo
                 WHERE tt.salesapNo = om.SaleOrder 
                 GROUP BY tt.salesapNo) as consignee_name,
                (SELECT sd.is_tracker 
                 FROM puma_sap_data_trips as tt
                 JOIN puma_sap_data as sd on sd.id = tt.main_id
                 WHERE tt.salesapNo = om.SaleOrder 
                 GROUP BY tt.salesapNo) as is_tracker,
                CASE
                    WHEN om.status = 0 THEN 'Pending'
                    WHEN om.status = 1 THEN 'Pushed'
                    WHEN om.status = 3 THEN 'Complete'
                    WHEN om.status = 2 THEN 'Cancel'
                    WHEN om.status = 4 THEN 'Special Approval'
                    WHEN om.status = 5 THEN 'Forwarded'
                    WHEN om.status = 6 THEN 'Processed'
                END AS current_status,
                (SELECT GROUP_CONCAT(geo.consignee_name SEPARATOR ', ') 
                 FROM dealers_depots AS dd
                 JOIN geofenceing AS geo ON geo.id = dd.depot_id
                 WHERE dd.dealers_id = dl.id) as dealers_depots
            FROM order_main as om 
            JOIN dealers as dl ON dl.id = om.created_by 
            LEFT JOIN users as us ON us.id = dl.asm 
            WHERE $condition AND om.status IN (0) AND dl.rettype = '$rettype'
            ORDER BY om.id DESC;
        ";

        $result1 = $db->query($sql_query1);

        if (!$result1) {
            die("Error: " . mysqli_error($db));
        }

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }

        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
