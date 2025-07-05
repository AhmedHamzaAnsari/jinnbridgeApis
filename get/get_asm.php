<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
$pre = $_GET["pre"];
$user_id = $_GET["user_id"];
if ($pass != '') {
    if ($pass == $access_key) {


        if($pre == 'TM'){
            $sql_query1 = "SELECT us.name,us.id FROM users_asm_tm as at
            join users as us on us.id=at.asm_id
            where at.tm_id=$user_id";
        }else{

            $sql_query1 = "SELECT * FROM users where privilege='ASM' order by id desc;";
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