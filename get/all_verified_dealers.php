<?php
//fetch.php  
include ("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$id = $_GET["user_id"];

if ($pass != '') {
    if ($pass == $access_key) {

        if ($pre == 'ZM') {

            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            left join users as usz on usz.id=dl.zm
           left join users as ust on ust.id=dl.tm
           left join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.zm=$id and dl.privilege='Dealer' and dl.indent_price=1 order by dl.id desc";
        } elseif ($pre == 'TM') {

            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
           left join users as usz on usz.id=dl.zm
           left join users as ust on ust.id=dl.tm
           left join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.tm=$id and dl.privilege='Dealer' and dl.indent_price=1 order by dl.id desc";
        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
           left join users as usz on usz.id=dl.zm
           left join users as ust on ust.id=dl.tm
           left join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.asm=$id and dl.privilege='Dealer' and dl.indent_price=1 order by dl.id desc";

        } else {

            $sql_query1 = "SELECT dl.*,dl.`co-ordinates` as co_ordinates,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
           left join users as usz on usz.id=dl.zm
           left join users as ust on ust.id=dl.tm
           left join users as usa on usa.id=dl.asm
            where dl.privilege='Dealer' and dl.privilege='Dealer' and dl.indent_price=1 order by dl.id desc ;";
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