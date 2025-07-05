<?php
include("../config.php");
session_start();

if (isset($_POST)) {

    $name = $_POST['name'];
    $user_id = $_POST['user_id'];
    $id = $_POST['row_id'];
    $email = $_POST['email'];
    $confirm_password = $_POST['confirm_password'];
    $encriped = md5($confirm_password);
    $desc = $_POST['confirm_password'];
    $number = $_POST['number'];
    $sales_role = $_POST['sales_role'];
    $sales_role_hide = $_POST['sales_role_hide'];
    $tm_hide = $_POST['tm_hide'];
    $zm_hide = $_POST['zm_hide'];

    $output = "";
    
    // Existing code...
    $date = date("Y-m-d H:i:s");

    $query = "UPDATE users SET name='$name',
     privilege='$sales_role',
     password='$encriped',
     description='$desc',
     telephone='$number',
    email='$email'
     WHERE id='$id'";


    if (mysqli_query($db, $query)) {

        if ($sales_role_hide == 'TM') {


            $delete_tm = "DELETE FROM `users_zm_tm` WHERE `tm_id`='$id' AND zm_id='$zm_hide' ";
            if (mysqli_query($db, $delete_tm)) {
            } else {
                echo 'Error' . mysqli_error($db) . '<br>' . $delete_tm;
            }
        } elseif ($sales_role_hide == 'ASM') {

            $delete_asm = "DELETE FROM `users_asm_tm` WHERE `asm_id`='$id' AND tm_id='$tm_hide'";
            if (mysqli_query($db, $delete_asm)) {
            }
        }
        if ($sales_role == 'ZM') {
            $privilege = "UPDATE `users` SET `privilege`='$sales_role' WHERE id='$id'";
            if (mysqli_query($db, $privilege)) {
                $output = 1;

                // echo $output;
            } else {
                echo 'Error' . mysqli_error($db) . '<br>' . $privilege;
            }
        
        } 
        else if ($sales_role == 'TM') 
        {
            $zm = $_POST['zm'];
            $query1 = "INSERT INTO `users_zm_tm`
        (`zm_id`,
        `tm_id`,
        `created_by`,
        `created_at`)
        VALUES
        ('$zm',
        '$id',
        '$date',
        '$user_id');";

            if (mysqli_query($db, $query1)) {
                $output = 1;

                // echo $output;
            } else {
                echo 'Error' . mysqli_error($db) . '<br>' . $query1;
            }
        }
         else if ($sales_role == 'ASM')
          {
            $tm = $_POST['tm'];
            $query2 = "INSERT INTO `users_asm_tm`
                        (`tm_id`,
                        `asm_id`,
                        `created_by`,
                        `created_at`)
                        VALUES
                        ('$tm',
                        '$id',
                        '$date',
                        '$user_id');";

            if (mysqli_query($db, $query2)) 
            {
                $output = 1;

                // echo $output;
            }
             else 
            {
                echo 'Error' . mysqli_error($db) . '<br>' . $query2;
            }
        }
    }





    echo $output;
}
