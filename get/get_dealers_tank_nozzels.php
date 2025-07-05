<?php
//fetch.php  
include("../config.php");


$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    $dealer_id = $_GET["dealer_id"];
    if ($pass == $access_key) {
        $sql_query1 = "SELECT tn.*,ll.lorry_no,ll.product,ll.min_limit,ll.max_limit,nn.name,nn.no_of_nozel FROM dealers_tanks_nozels as tn 
        join dealers_lorries as ll on ll.id=tn.tank_id
        join dealers_nozzel as nn on nn.id=tn.nozel_id where tn.dealer_id='$dealer_id';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error($db));

        $thread = array();
        while ($user = $result1->fetch_assoc()) {
            $thread[] = $user;
        }
        echo json_encode($thread);

    } else {
        echo 'Wrong Key...';
    }

} 
else 
{
    echo 'Key is Required';
}


?>