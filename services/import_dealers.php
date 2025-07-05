<?php
//fetch.php  
include("../config.php");
set_time_limit(500); // 


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT distinct(erp_id) FROM customer_details order by id asc";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $dealers_id = $user['erp_id'];

           
            $sql_query2 = "SELECT * FROM customer_details where erp_id='$dealers_id' order by id asc";

            $result2 = $db->query($sql_query2) or die("Error :" . mysqli_error());
    
            $first_id = '';
            $pre = 'Dealer';
            $i=0;

            while ($user2 = $result2->fetch_assoc()) {
    
                $id = $user2['erp_id'];
                $site_name = $user2['site_name'];
                $location = $user2['location'];
                $area_code = $user2['area_code'];
                $contact = $user2['contact'];
                $full_contact = $area_code."".$contact;
                $email = $full_contact.'@gmail.com';
                $address_line1 = $user2['address_line1'];
                $address_line2 = $user2['address_line2'];
                $address_line3 = $user2['address_line3'];
                $add = $address_line1.' '.$address_line2 .' '.$address_line2;
                $city = $user2['city'];
                $postal = $user2['postal'];
                $province = $user2['province'];
                $depotcode = $user2['depotcode'];
                $depotdesc = $user2['depotdesc'];
                $regional_manager = $user2['regional_manager'];
                $territory_manager = $user2['territory_manager'];
                $rettype = $user2['rettype'];
                $retype_desc = $user2['retype_desc'];

                $date = date('Y-m-d H:i:s');

                $insert = "INSERT INTO `dealers`
                (`name`,
                `privilege`,
                `sap_no`,
                `contact`,
                `email`,
                `password`,
                `location`,
                `co-ordinates`,
                `no_lorries`,
                `zm`,
                `tm`,
                `asm`,
                `banner`,
                `logo`,
                `parent_id`,
                `city`,
                `district`,
                `province`,
                `region`,
                `created_by`,
                `created_at`,
                `owner_name`,
                `rettype`,
                `rettype_desc`,
                `depo`)
                VALUES
                ('$site_name',
                '$pre',
                '$id',
                '$full_contact',
                '$email',
                '1234567890',
                '$add',
                '24.8433193, 66.954432',
                '0',
                '2',
                '$regional_manager',
                '$territory_manager',
                '18459-2.png',
                'system_logo.png',
                '$first_id',
                '$city',
                '',
                '$province',
                '',
                '1',
                '$date',
                '$site_name',
                '$rettype',
                '$retype_desc',
                '$depotdesc');";

                if(mysqli_query($db, $insert)){
                    if($i==0){
                        $pre = 'Manager';
                        $first_id = mysqli_insert_id($db);
                        $i++;
                    }

                }
                else{
                    echo 'Error' . mysqli_error($db) . '<br>' . $insert;
                }
                
    
    
                // echo $output;
    
            }

        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}


?>