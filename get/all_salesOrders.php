<?php
include("../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
$from = $_GET["from"];
$to = $_GET["to"];
$rettype = $_GET["rettype"];
if ($pass) {
    if ($pass == $access_key) {
        // Set the role condition based on `$pre`
        $role_condition = '';
        switch ($pre) {
            case 'ZM':
                $role_condition = "dl.zm = $id";
                break;
            case 'TM':
                $role_condition = "dl.tm = $id";
                break;
            case 'ASM':
                $role_condition = "dl.asm = $id";
                break;
            default:
                $role_condition = '1=1'; // No additional role condition
        }

        $sql_query1 = "SELECT si.*, 
                pp.name AS product_name, 
                dl.name AS dealer_name, 
                CASE
                    WHEN si.status = 0 THEN 'Pending'
                    WHEN si.status = 1 THEN 'Approved'
                    WHEN si.status = 2 THEN 'Complete'
                    WHEN si.status = 3 THEN 'Cancel'
                    WHEN si.status = 4 THEN 'Special Approval'
                    WHEN si.status = 5 THEN 'ASM Approved'
                END AS current_status,
                CASE 
                    WHEN TRIM(dl.rettype_desc) = 'COCO site' THEN si.quantity * si.rate
                    ELSE si.quantity * si.rate
                END AS total_amount,
                dl.rettype_desc,
                si.quantity,
                dp.indent_price,
                dp.nozel_price,
                dp.freight_value,
                CASE 
                    WHEN TRIM(si.buyer_own) = 'PP' THEN dp.indent_price
                    ELSE dp.freight_value
                END AS pp_rate
            FROM order_sales_invoice AS si 
            JOIN all_products AS pp ON pp.sap_no = si.item
            JOIN dealers AS dl ON dl.sap_no = si.customer_id
            JOIN dealers_products AS dp ON dp.dealer_id = dl.id AND dp.name = pp.name
            WHERE $role_condition 
                AND si.status != 3 
                AND si.created_at >= '$from' 
                AND si.created_at <= '$to'
                and dl.rettype='$rettype'
            GROUP BY si.id
            ORDER BY si.id DESC;
        ";

        $result1 = $db->query($sql_query1) or die("Error: " . mysqli_error($db));
        
        // Prepare the result set
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
