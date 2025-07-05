<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
// $id=$_GET["id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $sales_order = $_GET["sales_order"];



        $sql_query1 = "SELECT od.id as sub_id,od.*,om.*,geo.sap_no as dealer_sap ,geo.name,dp.name as product_name ,gg.consignee_name,om.depot,
        CASE
                           WHEN od.status = 0 THEN 'Pending'
                           WHEN od.status = 1 THEN 'Start'
                           WHEN od.status = 2 THEN 'Complete'
                           END AS current_status
        FROM order_detail as od 
       left join order_main as om on om.id=od.main_id
        left join geofenceing as gg on gg.id=om.depot
        left join dealers as geo on geo.id = od.cus_id 
        left join dealers_products as dp on dp.id=od.product_type
      
        where od.main_id = $sales_order  order by od.id desc";

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