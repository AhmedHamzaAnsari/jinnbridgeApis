<?php
//fetch.php  
include ("../../config.php");


$access_key = '12345';

$pass = $_GET["accesskey"];
if ($pass != '') {
    $id = $_GET["id"];
    $from = $_GET['from'];
    $to = $_GET['to'];
    if ($pass == $access_key) {
        $todate = date("Y-m-d H:i:s", time());
        $prev_date = date("Y-m-d H:i:s", strtotime($todate . ' -1 day'));
        // $sql_query1 = "SELECT da.*,dc.name FROM driving_alerts as da join devicesnew as dc on dc.id=da.device_id 
        // where da.type='Un-Authorized Stop' and da.created_at>='$from' and da.created_at<='$to' and da.created_by='$id';";



        $sql_query1 = "SELECT dc.id as device_id,dc.name as device_name,geo.consignee_name,geo.location,ck.in_time,ck.out_time,ck.in_duration FROM geo_check as ck
        join geofenceing as geo on geo.id=ck.geo_id
        join users_devices_new as ud on ud.devices_id=ck.veh_id
        join devicesnew as dc on dc.id=ud.devices_id
        where geo.geotype='Black Spote'
        and date(ck.in_time)>='$from'
        and date(ck.in_time)<='$to'
        and ud.users_id='$id'
        and ck.in_duration>=60
        order by ck.id desc;";


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