<?php
//fetch.php  
include ("../../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {
        $id = $_GET["id"];
        $pre = $_GET["pre"];
        $sql_query1 = '';
        $sql = "SELECT * FROM users WHERE id=$id";

        // echo $sql;

        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_array($result);

        $rol = $row['status'];

        if ($rol == '1') {
            if ($pre == 'Admin') {

                $sql_query1 = "SELECT dl.*,usz.name as zm_name,ust.name tm_name,usa.name as asm_name  FROM dealers as dl 
                join users as usz on usz.id=dl.zm
                join users as ust on ust.id=dl.tm
                join users as usa on usa.id=dl.asm
                where dl.zm=$id order by dl.id desc";
            }  else {
                $sql_query1 = "SELECT dl.*,us.name as user_name FROM eng_users_dealers as ud 
                join dealers as dl on dl.id=ud.dealer_id
                join users as us on us.id=ud.user_id
                where ud.user_id=$id";

            }

            $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());
    
            $thread = array();
            while ($user = $result1->fetch_assoc()) {
                $thread[] = $user;
            }
            setAllPhoneNumbersToNull($thread);
            echo json_encode($thread);


        }





    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function setAllPhoneNumbersToNull(&$array) {
    foreach ($array as &$contact) {
        $contact['contact'] = '****'; // Set phone number to null for each contact
    }
}
?>