<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $id = $_GET["id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT od.*,dl.name as dealer_name,us.name as username,ll.created_at as approved_time FROM omcs_dealers AS od 
        join omcs_dealers_log as ll on ll.main_id=od.id
                join dealers as dl on dl.id=od.old_dealer_id
                join users as us on us.id=od.created_by
                where od.status=1 order by od.id desc;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        $thread = utf8ize($thread);
$json = json_encode($thread, JSON_PRETTY_PRINT);

if ($json === false) {
    echo json_encode(["error" => "JSON encoding failed", "details" => json_last_error_msg()]);
} else {
    echo $json;
}


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


function utf8ize($data) {
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>