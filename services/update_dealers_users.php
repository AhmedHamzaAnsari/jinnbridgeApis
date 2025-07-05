<?php
//fetch.php  
include ("../config.php");
set_time_limit(500); //

$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT us.id as asm_id,us.name as asm_name,uasm.name as tm_name,uasm.id as tm_id,cd.erp_id FROM users as us 
        join customer_details as cd on cd.territory_manager_decs=us.name
        join users as uasm on uasm.name=cd.regional_manager_desc group by cd.erp_id ";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $erp_id = $user['erp_id'];
            $tm_id = $user['tm_id'];
            $asm_id = $user['asm_id'];


            $query = "UPDATE `dealers`
            SET `tm` = '$tm_id',
            `asm` = '$asm_id'
            WHERE `sap_no` = '$erp_id';";
    
    
            if (mysqli_query($db, $query)) {
                echo 'Dealers RM TM Updated.';

            }else{
                echo 'Dealers RM TM Not Updated.';

            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}
echo 'Last Run ' . date('Y-m-d H:i:s');


?>