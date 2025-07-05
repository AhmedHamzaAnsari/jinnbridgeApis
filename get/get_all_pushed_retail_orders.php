<?php
//fetch.php
include("../config.php");

$access_key = '03201232927';

// Sanitize inputs
$pass = isset($_GET["key"]) ? mysqli_real_escape_string($db, $_GET["key"]) : '';
$pre = isset($_GET["pre"]) ? mysqli_real_escape_string($db, $_GET["pre"]) : '';
$id = isset($_GET["user_id"]) ? mysqli_real_escape_string($db, $_GET["user_id"]) : '';
$from = isset($_GET["from"]) ? mysqli_real_escape_string($db, $_GET["from"]) : '';
$to = isset($_GET["to"]) ? mysqli_real_escape_string($db, $_GET["to"]) : '';

if ($pass != '') {
    if ($pass === $access_key) {
        // Validate 'from' and 'to' dates
       

        // Base SQL query for different user types (ZM, TM, ASM)
        $base_query = "SELECT om.*, dl.name, dl.zm, dl.tm, dl.asm, dl.region, dl.city, dl.province, dl.district, us.name as usersnames, 
                              dl.credit_limit, dl.rettype_desc, dl.sap_no,
                              (SELECT CONCAT(geo.code, '-', geo.consignee_name) 
                               FROM puma_sap_data_trips AS tt
                               JOIN puma_sap_data AS sd ON sd.id = tt.main_id
                               JOIN geofenceing AS geo ON geo.code = sd.depo
                               WHERE tt.salesapNo = om.SaleOrder 
                               GROUP BY tt.salesapNo) AS consignee_name,
                              (SELECT sd.is_tracker 
                               FROM puma_sap_data_trips AS tt
                               JOIN puma_sap_data AS sd ON sd.id = tt.main_id
                               WHERE tt.salesapNo = om.SaleOrder 
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
                        JOIN dealers AS dl ON dl.id = om.created_by 
                        LEFT JOIN users AS us ON us.id = dl.asm 
                        WHERE om.status IN (1)
                        AND dl.rettype = 'RT'
                        AND om.created_at >= '$from'
                        AND om.created_at <= '$to'
                        AND om.web_order = 0";

        // Modify the query based on the user type in $pre
        if ($pre == 'ZM') {
            $sql_query1 = $base_query . " AND dl.zm = '$id' ORDER BY om.id DESC";
        } elseif ($pre == 'TM') {
            $sql_query1 = $base_query . " AND dl.tm = '$id' ORDER BY om.id DESC";
        } elseif ($pre == 'ASM') {
            $sql_query1 = $base_query . " AND dl.asm = '$id' ORDER BY om.id DESC";
        } else {
            $sql_query1 = $base_query . " ORDER BY om.id DESC";
        }

        // Execute the query
        $result1 = $db->query($sql_query1);

        // Check for query errors
        if (!$result1) {
            die(json_encode(['error' => 'Query Error: ' . mysqli_error($db)]));
        }

        // Fetch the results and encode them into JSON format
        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }

        // Return data as JSON
        echo json_encode($thread);

    } else {
        echo json_encode(['error' => 'Wrong Key']);
    }
} else {
    echo json_encode(['error' => 'Key is Required']);
}
?>
