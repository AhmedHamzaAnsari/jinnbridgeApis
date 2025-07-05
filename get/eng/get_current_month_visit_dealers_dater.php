<?php
//fetch.php  
include ("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        // $pre = $_GET["pre"];
        $months = $_GET["months"];
        $years = $_GET["years"];

        $sql = "SELECT * FROM users WHERE id=$id";

        // echo $sql;

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $rol = $row['privilege'];

        if ($rol != 'ASM Disabled') {
           
                $sql_query1 = "SELECT dl.*, us.name as zm_name
                FROM dealers as dl 
                join eng_users_dealers as eg on eg.dealer_id=dl.id
                JOIN users as us ON us.id = eg.user_id
                WHERE dl.id NOT IN (
                SELECT it.dealer_id
                FROM eng_inspector_task as it
                WHERE MONTH(it.time) = '$months' AND YEAR(it.time) = '$years' and it.created_by=$id) and eg.user_id=$id order by dl.name asc";

            


            

            $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

            $thread = array();
            while ($user = $result1->fetch_assoc()) {
                $thread[] = $user;
            }
            echo json_encode($thread);
        }

    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>