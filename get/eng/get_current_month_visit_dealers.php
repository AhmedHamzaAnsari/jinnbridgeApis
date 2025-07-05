<?php
//fetch.php  
include("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];
        $sql = "SELECT * FROM users WHERE id=$id";

        // echo $sql;

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $rol = $row['privilege'];



        $sql_query1 = "SELECT dl.*, usz.name as user_name
        FROM dealers as dl 
        join eng_users_dealers as ud on ud.dealer_id=dl.id
        JOIN users as usz ON usz.id = ud.user_id
        WHERE dl.id NOT IN (
        SELECT it.dealer_id
        FROM eng_inspector_task as it
        WHERE MONTH(it.time) = MONTH(CURDATE()) AND YEAR(it.time) = YEAR(CURDATE()) and it.user_id='$id'
    ) and ud.user_id='$id' order by dl.name asc";


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