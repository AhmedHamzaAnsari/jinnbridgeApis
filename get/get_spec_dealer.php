<?php
//fetch.php  
include ("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$dealer_id = $_GET["dealer_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dl.*,zm.name as zm_name,tm.name as tm_name,asm.name as asm_name FROM dealers as dl
        left join users as zm on zm.id=dl.zm
        left join users as tm on tm.id=dl.tm
        left join users as asm on asm.id=dl.asm where dl.id=$dealer_id;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $thread[] = $user;

            $id = $user['id'];
            $last_baldate = $user['last_baldate'];
            $sap_no = $user['sap_no'];
            $acount = $user['acount'];


            $check = "SELECT sum(om.total_amount) as order_amount FROM order_main as om 
            join dealers as dl on dl.sap_no=om.dealer_sap
            where om.dealer_sap='$sap_no' and om.created_at>='$last_baldate' and om.status=0;";

            $result = mysqli_query($db, $check);
            $row = mysqli_fetch_array($result);
            $sum_amount = $row['order_amount'];
            if ($sum_amount > 0) {
                $valueToSubtract = $sum_amount;

                // Subtract the value from the ledger element
                if ($acount < 0) {
                    $thread[0]['acount'] += $valueToSubtract;
                } elseif ($acount > 0) {
                    $thread[0]['acount'] -= $valueToSubtract;
                } else {
                    $thread[0]['acount'] -= $valueToSubtract;
                }
                
            }

            $check = "SELECT  sum(CASE 
            WHEN dl.rettype_desc = 'COCO site                     ' THEN om.quantity*dp.nozel_price
            ELSE om.quantity*dp.indent_price
        END) AS order_amount FROM order_sales_invoice as om 
                join dealers as dl on dl.sap_no=om.customer_id
                join all_products as pp on pp.sap_no=om.item
                join dealers_products as dp on dp.dealer_id=dl.id
                where om.customer_id='$sap_no' and om.created_at>='$last_baldate' and om.status=0;";

            $result = mysqli_query($db, $check);
            $row = mysqli_fetch_array($result);
            $sum_amount = $row['order_amount'];
            if ($sum_amount > 0) {
                $valueToSubtract = $sum_amount;

                // Subtract the value from the ledger element
                if ($acount < 0) {
                    $thread[0]['acount'] += $valueToSubtract;
                } elseif ($acount > 0) {
                    $thread[0]['acount'] -= $valueToSubtract;
                } else {
                    $thread[0]['acount'] -= $valueToSubtract;
                }
                
            }



          

           
        }

       
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>