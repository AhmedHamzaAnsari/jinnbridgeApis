<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {


        if ($pre == 'ZM') {
        } elseif ($pre == 'TM') {
            $sql_query1 = "SELECT dl.asm,
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
        where dl.tm=$id and si.status!=3 group by si.order_no
        ORDER BY STR_TO_DATE(CONCAT(si.order_date, ' ', si.datetime), '%d-%b-%y %H:%i:%s') DESC;";
        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT dl.asm,
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
            where dl.asm=$id and si.status!=3 group by si.order_no
            ORDER BY STR_TO_DATE(CONCAT(si.order_date, ' ', si.datetime), '%d-%b-%y %H:%i:%s') DESC;";
        } else {
            $sql_query1 = "SELECT dl.asm,
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
            where  si.status!=3 group by si.order_no
            ORDER BY STR_TO_DATE(CONCAT(si.order_date, ' ', si.datetime), '%d-%b-%y %H:%i:%s') DESC;";
        }

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

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