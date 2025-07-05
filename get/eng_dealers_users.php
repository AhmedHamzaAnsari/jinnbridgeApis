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

            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.region!='TEST' order by dl.name ASC";
        }
        elseif($pre == 'TM'){
            
            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.region!='TEST' and dl.tm=$id order by dl.name ASC";
        }
        elseif($pre == 'ASM'){
            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.region!='TEST' and dl.asm=$id order by dl.name ASC";

        }
        elseif ($pre == 'Eng') {
            $sql_query1 = "SELECT dl.*,
            dl.`co-ordinates` as co_ordinates,
            '' as zm_name,
            '' as tm_name,
            us.name as asm_name,
            dl.name as dealer_name,
            us.name as username,
            us.id as eng_user_id,
            eu.id as row_id,
            CASE 
                WHEN 
                    EXISTS (SELECT 1 FROM dealer_dispenser_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_tank_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_general_eq_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_signage_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                THEN 'Verified'
                ELSE 'Not Verified'
            END AS asset_verification_status
     FROM dealers AS dl
     JOIN eng_users_dealers AS eu ON eu.dealer_id = dl.id
     JOIN users AS us ON us.id = eu.user_id
     WHERE dl.privilege = 'Dealer'
     AND us.id = $id
     ORDER BY dl.name ASC;";

        }else{

            $sql_query1 = "SELECT dl.*,
            dl.`co-ordinates` as co_ordinates,
            dl.name as dealer_name,
            us.name as username,
            '' as zm_name,
            '' as tm_name,
            us.name as asm_name,
            us.id as eng_user_id,
            eu.id as row_id,
            CASE 
                WHEN 
                    EXISTS (SELECT 1 FROM dealer_dispenser_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_tank_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_general_eq_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                    AND EXISTS (SELECT 1 FROM dealer_signage_setup WHERE dealer_id = dl.id AND created_by = us.id order by id desc limit 1)
                THEN 'Verified'
                ELSE 'Not Verified'
            END AS asset_verification_status
     FROM dealers AS dl
     JOIN eng_users_dealers AS eu ON eu.dealer_id = dl.id
     JOIN users AS us ON us.id = eu.user_id
     WHERE dl.privilege = 'Dealer'
     ORDER BY dl.name ASC";
        }


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