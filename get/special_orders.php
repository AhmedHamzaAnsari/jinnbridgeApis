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

            $sql_query1 = "SELECT od.*,d.name,geo.consignee_name,d.zm,d.tm,d.asm FROM order_main od 
            join  dealers d on d.id = od.created_by
            left join geofenceing as geo on geo.id=od.depot 
            WHERE od.status=1 and d.zm='$id' order by od.id desc;";
        }
        elseif($pre == 'TM'){
            
            $sql_query1 = "SELECT od.*,d.name,geo.consignee_name,d.zm,d.tm,d.asm FROM order_main od 
            join  dealers d on d.id = od.created_by
            left join geofenceing as geo on geo.id=od.depot 
            WHERE od.status=1 and d.tm='$id' order by od.id desc;";
        }
        elseif($pre == 'ASM'){
            $sql_query1 = "SELECT od.*,d.name,geo.consignee_name,d.zm,d.tm,d.asm FROM order_main od 
            join  dealers d on d.id = od.created_by
            left join geofenceing as geo on geo.id=od.depot 
            WHERE od.status=1 and d.asm='$id' order by od.id desc;";

        }else{

            $sql_query1 = "SELECT od.*,d.name,geo.consignee_name,d.zm,d.tm,d.asm FROM order_main od 
            join  dealers d on d.id = od.created_by
            left join geofenceing as geo on geo.id=od.depot 
            WHERE od.status=1 order by od.id desc;";
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