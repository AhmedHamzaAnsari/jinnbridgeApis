<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT lp.*,pc.name as category,ps.name as size_name,ps.ctn_size,ps.ctn_qty
        FROM lubes_product as lp 
       join lubes_product_category as pc on pc.id=lp.cat_id
       join lubes_product_sizes as ps on ps.id=lp.size_id order by lp.name asc;";

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