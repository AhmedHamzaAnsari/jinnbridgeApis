<?php
//fetch.php  
include ("../../config.php");

$access_key = '03201232927';
$pass = $_GET["key"];

if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];
        $month = $_GET["month"];  // Added 'month' as input parameter

        $sql_query1 = '';
        $sql = "SELECT * FROM users WHERE id=$id";

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);
        $rol = $row['privilege'];

        if ($rol != 'ASM Disabled') {
            if ($pre == 'ZM') {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE zm=$id";
            } elseif ($pre == 'TM') {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE tm=$id";
            } else {
                $sql_query1 = "SELECT GROUP_CONCAT(id ORDER BY id ASC) AS id FROM dealers WHERE asm=$id";
            }

            $result = mysqli_query($db, $sql_query1);
            $row = mysqli_fetch_array($result);
            $count = mysqli_num_rows($result);

            if ($count > 0) {
                $dealer_id = $row['id'];

                // Modify the query to filter by the specified month and year
                // $sql_query2 = 'SELECT om.*, dl.name, dl.location 
                //                FROM order_main AS om 
                //                JOIN dealers AS dl ON dl.id = om.created_by 
                //                WHERE om.created_by IN (' . $dealer_id . ') 
                //                AND MONTH(om.created_at) = MONTH("' . $month . '-01") 
                //                AND YEAR(om.created_at) = YEAR("' . $month . '-01")
                //                ORDER BY om.id DESC';

                $sql_query2 = "SELECT dl.asm,
                si.*, 
                pp.name AS product_name, 
                dl.name AS dealer_name, 
                dl.rettype_desc,
                CASE
                    WHEN si.status = 0 THEN 'Pending'
                    WHEN si.status = 1 THEN 'Approved'
                    WHEN si.status = 2 THEN 'Complete'
                    WHEN si.status = 3 THEN 'Cancel'
                    WHEN si.status = 4 THEN 'Special Approval'
                    WHEN si.status = 5 THEN 'ASM Approved'
                END AS current_status,
                CASE 
                    WHEN dl.rettype_desc = 'COCO site                     ' THEN si.quantity * si.rate
                    ELSE si.quantity * si.rate
                END AS total_amount,
                dl.rettype_desc,
                si.quantity,
                dp.indent_price,
                dp.nozel_price,
                dp.freight_value,CASE 
                WHEN si.buyer_own = 'PP ' THEN dp.indent_price
                ELSE  dp.freight_value
            END AS rate
                FROM order_sales_invoice AS si 
                JOIN all_products AS pp ON pp.sap_no = si.item
                JOIN dealers AS dl ON dl.sap_no = si.customer_id
                JOIN dealers_products AS dp ON dp.dealer_id = dl.id and dp.name=pp.name
                where  si.status!=3 and dl.id IN ($dealer_id)  AND MONTH(si.created_at) = MONTH('" . $month . "-01') AND YEAR(si.created_at) = YEAR('" . $month . "-01') group by si.order_no
                ORDER BY STR_TO_DATE(CONCAT(si.order_date, ' ', si.datetime), '%d-%b-%y %H:%i:%s') DESC";
                
                $result1 = $db->query($sql_query2) or die("Error :" . mysqli_error($db));

                $thread = array();
                while ($user = $result1->fetch_assoc()) {
                    $thread[] = $user;
                }
                echo json_encode($thread);
            }
        }
    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
