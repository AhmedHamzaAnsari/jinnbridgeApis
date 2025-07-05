<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
$from = $_GET["from"];
$to = $_GET["to"];

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
        dl.rettype_desc, 
        us.name AS usersnames, 
        dl.credit_limit, 
        dl.rettype_desc, 
        dl.sap_no,
        pp.name AS product_name,
        CASE
            WHEN om.status = 0 THEN 'Pending'
            WHEN om.status = 1 THEN 'Pushed'
            WHEN om.status = 2 THEN 'Cancel'
            WHEN om.status = 3 THEN 'Cancel'
        END AS current_status,
        (SELECT COUNT(*) FROM order_info WHERE order_no = om.order_no) AS dispatches,
        (SELECT COUNT(*) FROM order_info WHERE is_shortage = 0 and order_no = om.order_no) AS no_not_shortage,
    (SELECT COUNT(*) FROM order_info WHERE is_shortage = 1 and order_no = om.order_no) AS no_shortage
    FROM order_sales_invoice AS om 
    JOIN dealers AS dl ON dl.sap_no = om.customer_id 
    LEFT JOIN users AS us ON us.id = dl.asm 
    JOIN all_products AS pp ON pp.sap_no = om.item
    LEFT JOIN dealers_products AS dp ON dp.dealer_id = dl.id AND dp.name = pp.name
    WHERE $condition AND om.created_at>='$from' AND om.created_at<='$to' and dl.indent_price=1
    group by om.id  ORDER BY om.id DESC;
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