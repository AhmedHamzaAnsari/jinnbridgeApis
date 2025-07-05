<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';
$rettype = $_GET["rettype"];

$pass = $_GET["key"];
if ($pass != '') {
    $id = $_GET["id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT od.*,oi.customer_id,oi.customer_name FROM order_shortage as od
        join order_info as oi on oi.order_no=od.order_id 
        join dealers as dl on dl.sap_no=oi.customer_id
        where dl.rettype='$rettype'
        order by od.id desc;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} 
else 
{
    echo 'Key is Required';
}


?>