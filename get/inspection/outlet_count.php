<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];

        if($pre == 'ZM'){

            $sql_query1 = "SELECT dl.*,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            left join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.zm=$id and dl.privilege='Dealer' order by dl.id desc";
        }
        elseif($pre == 'TM'){
            
            $sql_query1 = "SELECT dl.*,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
            left join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.tm=$id and dl.privilege='Dealer' order by dl.id desc";
        }
        else{
            $sql_query1 = "SELECT dl.*,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
           left join users as usz on usz.id=dl.zm
            join users as ust on ust.id=dl.tm
            join users as usa on usa.id=dl.asm
            where dl.asm=$id and dl.privilege='Dealer' order by dl.id desc";

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