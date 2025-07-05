<?php
//fetch.php  
include ("../config.php");
set_time_limit(500); // 
$url1 = $_SERVER['REQUEST_URI'];
header("Refresh: 120; URL=$url1");

$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT distinct(sap_no) FROM dealers where indent_price=1;";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $sap_no = $user['sap_no'];


            $sql_query2 = "SELECT * FROM customer_bal where customer_id='$sap_no' order by created_at desc limit 1";

            $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error());

            $first_id = '';
            $pre = 'Dealer';
            $i = 0;

            while ($user2 = $result2->fetch_assoc()) {

                $balance = $user2['balance'];
                $lastbalance = $user2['lastbalance'];

                update_legder($sap_no, $db, $balance);




                // echo $output;

            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}
echo 'Last Run ' . date('Y-m-d H:i:s');

function update_legder($sap_id, $db, $new_ledger)
{

    $sql = "SELECT * FROM dealers where sap_no='$sap_id' and privilege='Dealer'";

    // echo $sql;

    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);

    $dealer_id = $row['id'];
    $acount = $row['acount'];
    $last_baldate = $row['last_baldate'];
    $datetime = date('Y-m-d H:i:s');
    $output = '';
    if ($acount != $new_ledger) {

       echo $query = "UPDATE `dealers` 
        SET `acount` = '$new_ledger',
        `last_baldate` = '$datetime'
        WHERE `sap_no` = '$sap_id';";


        if (mysqli_query($db, $query)) {

            $log = "INSERT INTO `dealer_ledger_log`
            (`dealer_id`,
            `type`,
            `old_ledger`,
            `new_ledger`,
            `datetime`,
            `description`,
            `created_at`,
            `created_by`)
            VALUES
            ('$dealer_id',
            'Primary',
            '$acount',
            '$new_ledger',
            '$datetime',
            '',
            '$datetime',
            '1');";
            if (mysqli_query($db, $log)) {
                $output = 'Ledger Updated <br>';

            } else {
                $output = 'Error' . mysqli_error($db) . '<br>' . $log;

            }

        } else {
            $output = 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    } else {
        $output = 'Ledger Not Updated <br>';

    }
    echo $output;

}


?>