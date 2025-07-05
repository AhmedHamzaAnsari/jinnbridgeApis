<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $id = $_GET["id"];
    $from = $_GET["from"];
    $to = $_GET["to"];

    $pre = $_GET["pre"];


    if ($pass == $access_key) {

        if ($pre == 'ZM') {

            $sql_query1 = "SELECT it.*,dl.name as site_name,dl.sap_no as dealer_sap,us.name as tm_name,us.title,us.region FROM dealer_casual_visits as it
            join dealers as dl on dl.id=it.dealer_id
            join users as us on us.id=it.users_id
            where dl.zm='$id' and date(it.created_at)>='$from' and date(it.created_at)<='$to' group by it.dealer_id,DATE(it.created_at);";

        } elseif ($pre == 'TM') {

            $sql_query1 = "SELECT it.*,dl.name as site_name,dl.sap_no as dealer_sap,us.name as tm_name,us.title,us.region FROM dealer_casual_visits as it
            join dealers as dl on dl.id=it.dealer_id
            join users as us on us.id=it.users_id
            where dl.tm='$id' and date(it.created_at)>='$from' and date(it.created_at)<='$to' group by it.dealer_id,DATE(it.created_at);";

        } elseif ($pre == 'ASM') {

            $sql_query1 = "SELECT it.*,dl.name as site_name,dl.sap_no as dealer_sap,us.name as tm_name,us.title,us.region FROM dealer_casual_visits as it
            join dealers as dl on dl.id=it.dealer_id
            join users as us on us.id=it.users_id
            where dl.asm='$id' and date(it.created_at)>='$from' and date(it.created_at)<='$to' group by it.dealer_id,DATE(it.created_at);";

        } else {

            $sql_query1 = "SELECT it.*,dl.name as site_name,dl.sap_no as dealer_sap,us.name as tm_name,'' as title,us.region,us.privilege FROM dealer_casual_visits as it
            join dealers as dl on dl.id=it.dealer_id
            join eng_users_dealers as du on du.dealer_id=dl.id
            join users as us on us.id=du.user_id
            where date(it.created_at)>='$from' and date(it.created_at)<='$to' group by it.dealer_id,DATE(it.created_at)";
        }


        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        // echo json_encode($thread);

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

function utf8ize($data)
{
    if (is_array($data)) {
        return array_map('utf8ize', $data);
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}
?>