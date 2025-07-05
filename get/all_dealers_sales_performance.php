<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];
if ($pass != '') {
    if ($pass == $access_key) {



        if($pre == 'ZM'){

            $sql_query1 = "SELECT dt.*,pp.name,dl.name as dealer_name FROM dealers_monthly_targets as dt 
            join dealers as dl on dl.id=dt.dealer_id
            join dealers_products as pp on pp.id=dt.product_id where dl.zm='$id' order by dt.id desc";
        }
        elseif($pre == 'TM'){
            
            $sql_query1 = "SELECT dt.*,pp.name,dl.name as dealer_name FROM dealers_monthly_targets as dt 
            join dealers as dl on dl.id=dt.dealer_id
            join dealers_products as pp on pp.id=dt.product_id where dl.tm='$id' order by dt.id desc";
        }
        elseif($pre == 'ASM'){
            $sql_query1 = "SELECT dt.*,pp.name,dl.name as dealer_name FROM dealers_monthly_targets as dt 
            join dealers as dl on dl.id=dt.dealer_id
            join dealers_products as pp on pp.id=dt.product_id where dl.asm='$id' order by dt.id desc";

        }else{

            $sql_query1 = "SELECT dt.*,pp.name,dl.name as dealer_name FROM dealers_monthly_targets as dt 
            join dealers as dl on dl.id=dt.dealer_id
            join dealers_products as pp on pp.id=dt.product_id order by dt.id desc;";
        }

        



        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

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