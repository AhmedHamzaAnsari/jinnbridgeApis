<?php
// fetch.php
include ("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];

if (!empty($pass)) {
    if ($pass === $access_key) {
        
        // Define the SQL query based on the user's role
        switch ($pre) {
            case 'ZM':
                $statusCondition = "dl.zm = $id AND om.status = 0";
                break;
            case 'TM':
                $statusCondition = "dl.tm = $id AND om.status != 0";
                break;
            case 'ASM':
                $statusCondition = "dl.asm = $id AND om.status = 0";
                break;
            default:
                $statusCondition = "om.status = 0";
                break;
        }

        // SQL query to fetch order data
        $sql_query1 = "SELECT om.*, dl.name, dl.zm, dl.tm, dl.asm, dl.region, dl.city, dl.province, dl.district,
                   us.name AS usersnames, dl.credit_limit, dl.rettype_desc, dl.sap_no,od.quantity,od.rate,
                   (SELECT CONCAT(geo.code, '-', geo.consignee_name)
                    FROM puma_sap_data_trips AS tt
                    JOIN puma_sap_data AS sd ON sd.id = tt.main_id
                    JOIN geofenceing AS geo ON geo.code = sd.depo
                    WHERE salesapNo = om.SaleOrder
                    GROUP BY tt.salesapNo) AS consignee_name,
                   (SELECT sd.is_tracker
                    FROM puma_sap_data_trips AS tt
                    JOIN puma_sap_data AS sd ON sd.id = tt.main_id
                    WHERE salesapNo = om.SaleOrder
                    GROUP BY tt.salesapNo) AS is_tracker,
                   CASE
                       WHEN om.status = 0 THEN 'Pending'
                       WHEN om.status = 1 THEN 'Pushed'
                       WHEN om.status = 3 THEN 'Complete'
                       WHEN om.status = 2 THEN 'Cancel'
                       WHEN om.status = 4 THEN 'Special Approval'
                       WHEN om.status = 5 THEN 'Forwarded'
                       WHEN om.status = 6 THEN 'Processed'
                   END AS current_status
            FROM order_main AS om
            join order_detail as od on od.main_id=om.id
            JOIN dealers AS dl ON dl.id = om.created_by
            LEFT JOIN users AS us ON us.id = dl.asm
            WHERE $statusCondition
            ORDER BY om.id DESC";

        // Execute the query and fetch results
        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));

        // Collect data in an array and encode as JSON
        $thread = [];
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
