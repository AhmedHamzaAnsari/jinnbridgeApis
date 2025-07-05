<?php
//fetch.php  
include ("../../../config_indemnifier.php");


$access_key = '12345';

$pass = $_GET["accesskey"];
if ($pass != '') {
    $id = $_GET["id"];
    $from = $_GET['from'];
    $to = $_GET['to'];
    if ($pass == $access_key) {
        $todate = date("Y-m-d H:i:s", time());
        $prev_date = date("Y-m-d H:i:s", strtotime($todate . ' -1 day'));
        $sql_query1 = "SELECT da.*,dc.name,dc.lat as v_lat,dc.lng as v_lng FROM sitara.driving_alerts as da join devicesnew as dc on dc.id=da.device_id where da.type='Night time violations' and da.created_at>='$from' and da.created_at<='$to' and da.created_by='$id';";

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