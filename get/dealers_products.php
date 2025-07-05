<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$id = $_GET["dealer_id"];
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT dp.*,ap.sap_no,SUBSTRING(ap.sap_no, LOCATE('1', '000000001000000000')) AS result_sap FROM dealers_products as dp
        left join all_products as ap on ap.name=dp.name
        where dp.dealer_id=$id;";

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