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

            $sql_query1 = "SELECT om.*,dl.name ,dl.zm,dl.tm,dl.asm,dl.region,dl.city,dl.province,dl.district,us.name as usersnames,dl.credit_limit,
            (SELECT CONCAT(geo.code, '-', geo.consignee_name) FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            join geofenceing as geo on geo.code=sd.depo
            where salesapNo=om.SaleOrder group by tt.salesapNo) as consignee_name,
            (SELECT sd.is_tracker FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            where salesapNo=om.SaleOrder group by tt.salesapNo) as is_tracker,
                        CASE
                                WHEN om.status = 0 THEN 'Pending'
                                WHEN om.status = 1 THEN 'Pushed'
                                WHEN om.status = 3 THEN 'Complete'
                                WHEN om.status = 2 THEN 'Cancel'
                                WHEN om.status = 4 THEN 'Special Approval'
                                WHEN om.status = 5 THEN 'ASM Approved'
                            END AS current_status
                        FROM order_main as om 
                        join dealers as dl on dl.id=om.created_by 
                        LEFT join users as us on us.id=dl.asm
            where om.status IN(4) and dl.zm=$id order by om.id desc";
        } elseif ($pre == 'TM') {

            $sql_query1 = "SELECT om.*,dl.name ,dl.zm,dl.tm,dl.asm,dl.region,dl.city,dl.province,dl.district,us.name as usersnames,dl.credit_limit,
            (SELECT CONCAT(geo.code, '-', geo.consignee_name) FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            join geofenceing as geo on geo.code=sd.depo
            where salesapNo=om.SaleOrder group by tt.salesapNo) as consignee_name,
            (SELECT sd.is_tracker FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            where salesapNo=om.SaleOrder group by tt.salesapNo) as is_tracker,
                        CASE
                                WHEN om.status = 0 THEN 'Pending'
                                WHEN om.status = 1 THEN 'Pushed'
                                WHEN om.status = 3 THEN 'Complete'
                                WHEN om.status = 2 THEN 'Cancel'
                                WHEN om.status = 4 THEN 'Special Approval'
                                WHEN om.status = 5 THEN 'ASM Approved'
                            END AS current_status
                        FROM order_main as om 
                        join dealers as dl on dl.id=om.created_by 
                        LEFT join users as us on us.id=dl.asm
            where om.status IN(4) and dl.tm=$id order by om.id desc";
        } elseif ($pre == 'ASM') {
            $sql_query1 = "SELECT om.*,dl.name ,dl.zm,dl.tm,dl.asm,dl.region,dl.city,dl.province,dl.district,us.name as usersnames,dl.credit_limit,
            (SELECT CONCAT(geo.code, '-', geo.consignee_name) FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            join geofenceing as geo on geo.code=sd.depo
            where salesapNo=om.SaleOrder group by tt.salesapNo) as consignee_name,
            (SELECT sd.is_tracker FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            where salesapNo=om.SaleOrder group by tt.salesapNo) as is_tracker,
                        CASE
                                WHEN om.status = 0 THEN 'Pending'
                                WHEN om.status = 1 THEN 'Pushed'
                                WHEN om.status = 3 THEN 'Complete'
                                WHEN om.status = 2 THEN 'Cancel'
                                WHEN om.status = 4 THEN 'Special Approval'
                                WHEN om.status = 5 THEN 'ASM Approved'
                            END AS current_status
                        FROM order_main as om 
                        join dealers as dl on dl.id=om.created_by 
                        LEFT join users as us on us.id=dl.asm
            where om.status IN(4) and dl.asm=$id order by om.id desc";

        } else {

            $sql_query1 = "SELECT om.*,dl.name ,dl.zm,dl.tm,dl.asm,dl.region,dl.city,dl.province,dl.district,us.name as usersnames,dl.credit_limit,
            (SELECT CONCAT(geo.code, '-', geo.consignee_name) FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            join geofenceing as geo on geo.code=sd.depo
            where salesapNo=om.SaleOrder group by tt.salesapNo) as consignee_name,
            (SELECT sd.is_tracker FROM puma_sap_data_trips  as tt
            join puma_sap_data as sd on sd.id=tt.main_id
            where salesapNo=om.SaleOrder group by tt.salesapNo) as is_tracker,
                        CASE
                                WHEN om.status = 0 THEN 'Pending'
                                WHEN om.status = 1 THEN 'Pushed'
                                WHEN om.status = 3 THEN 'Complete'
                                WHEN om.status = 2 THEN 'Cancel'
                                WHEN om.status = 4 THEN 'Special Approval'
                                WHEN om.status = 5 THEN 'ASM Approved'
                            END AS current_status
                        FROM order_main as om 
                        join dealers as dl on dl.id=om.created_by 
                        LEFT join users as us on us.id=dl.asm
            where om.status IN(4) order by om.id desc;";
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