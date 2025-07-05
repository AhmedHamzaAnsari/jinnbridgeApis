<?php
//fetch.php  
ini_set('max_execution_time', '0');
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 60; URL=$url1");
include("../../../config.php");
set_time_limit(5000); // 
// file_put_contents('reload_log.txt', 'Page reloaded at ' . date('Y-m-d H:i:s') . PHP_EOL, FILE_APPEND);


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {

        $sql_query1 = "SELECT * FROM order_main where delivered_status=0 and dealer_sap!='' order by id desc";
    

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $id = $user['id'];
            $sap = $user['dealer_sap'];
            $SaleOrder = $user['SaleOrder'];

            $selected = "SELECT * FROM puma_sap_data_trips where SUBSTRING(dealer_sap, 3)='$sap' and salesapNo='$SaleOrder';";
            $result_selected = mysqli_query($db, $selected);
            $selected_row = mysqli_fetch_array($result_selected);

            $count = mysqli_num_rows($result_selected);

            $up = date('Y-m-d H:i:s');
            if ($count > 0) {
                $update = "UPDATE `order_main`
                        SET
                        `delivered_status` = '1',
                        `approved_time` = '$up'
                        WHERE `id` = '$id';";

                        if (mysqli_query($db, $update)) {
                            $output = 1;

                            echo 'Updated .';

                        } else {
                            $output = 'Error' . mysqli_error($db) . '<br>';

                        }
            } else {
                echo 'Order Not Founds in SAP Table'. '<br>';
            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

echo date('Y-m-d H:i:s');
?>