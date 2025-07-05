<?php
include("../config.php");
session_start();
if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['confirm_password']);
    $password_enc = mysqli_real_escape_string($db, $_POST['confirm_password']);
    $encriped = md5($password_enc);
    $number = mysqli_real_escape_string($db, $_POST['number']);
    // $privilege = mysqli_real_escape_string($db,$_POST['privilege']); 
    $role = mysqli_real_escape_string($db, $_POST['role']);
    $sales_role = mysqli_real_escape_string($db, $_POST['sales_role']);

    if($role=='Order' || $role=='Logistics' || $role=='Forward_order' || $role=='App_order' || $role=='Back_orders' || $role=='Reporting'|| $role=='Eng'){
        $sales_role = $role;
    }


    function sales_role($main_id, $db, $user_id)
    {
        $date = date('Y-m-d H:i:s');
        $sales_role = mysqli_real_escape_string($db, $_POST['sales_role']);

        if ($sales_role == 'TM') {
            $zm = mysqli_real_escape_string($db, $_POST['zm']);


            $query = "INSERT INTO `users_zm_tm`
            (`zm_id`,
            `tm_id`,
            `created_by`,
            `created_at`)
            VALUES
            ('$zm',
            '$main_id',
            '$date',
            '$user_id');";

            if (mysqli_query($db, $query)) {


                echo 1;

            } else {
                echo 'Error' . mysqli_error($db) . '<br>' . $query;

            }
        }else{
            if ($sales_role == 'ASM') {
                $tm = mysqli_real_escape_string($db, $_POST['tm']);
    
    
                $query = "INSERT INTO `users_asm_tm`
                (`tm_id`,
                `asm_id`,
                `created_by`,
                `created_at`)
                VALUES
                ('$tm',
                '$main_id',
                '$date',
                '$user_id');";
    
                if (mysqli_query($db, $query)) {
    
    
                    echo 1;
    
                } else {
                    echo 'Error' . mysqli_error($db) . '<br>' . $query;
    
                }
            }
        }


    }

    function logistics($main_id, $db, $user_id)
    {
        $role = mysqli_real_escape_string($db, $_POST['logistics_role']);
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `users_logistics`
        (`role`,
        `logistics_id`,
        `created_by`,
        `created_at`)
        VALUES
        ('$role',
        '$main_id',
        '$date',
        '$user_id');";

        if (mysqli_query($db, $query)) {


            echo 1;

        } else {
            echo 'Error' . mysqli_error($db) . '<br>' . $query;

        }

    }
    // echo 'HAmza';
    if ($_POST["row_id"] != '') {


    } else {

        $parent_id = '';
        $check = mysqli_real_escape_string($db, $_POST['sales_role']);

        if($check == 'BSO'){
            $parent_id = mysqli_real_escape_string($db, $_POST['tm']);;
        }

        $query = "INSERT INTO  users (`name`,`privilege`,`login`, `password`,`usersettings_id`,`status`,`description`,`email`,`telephone`,`subacc_id`)
        VALUES ('$name', '$sales_role', '$email', '$encriped','1','1','$password','$email','$number','$parent_id')";



        if (mysqli_query($db, $query)) {
            $main_id = mysqli_insert_id($db);

            if ($role == 'Logistics') {

                logistics($main_id, $db, $user_id);
            } elseif ($role == 'Sales') {
                sales_role($main_id, $db, $user_id);
            }
            if($sales_role == 'ZM'){
                echo 1;
            }elseif($sales_role == 'Order'){
                echo 1;
            }
            elseif($sales_role == 'Forward_order'){
                echo 1;
            }
            elseif($sales_role == 'App_order'){
                echo 1;
            }
            elseif($sales_role == 'Back_orders'){
                echo 1;
            }
            elseif($sales_role == 'BSO'){
                echo 1;
            }
            elseif($sales_role == 'Reporting'){
                echo 1;
            }elseif($sales_role == 'Eng'){
                echo 1;
            }
            // elseif($sales_role == 'Logistics'){
            //     echo 1;
            // }

            // $output = 1;

        } else {
            echo 'Error' . mysqli_error($db) . '<br>' . $query;

        }
    }




    // echo $output;
}
?>