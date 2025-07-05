<?php
include("../config.php");
session_start();

if (isset($_POST)) {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = mysqli_real_escape_string($db, $_POST['confirm_password']);
    $password_enc = md5($password);
    $number = mysqli_real_escape_string($db, $_POST['number']);
    $role = mysqli_real_escape_string($db, $_POST['role']);
    $sales_role = mysqli_real_escape_string($db, $_POST['sales_role']);

    if (in_array($role, ['Order', 'Logistics', 'Forward_order', 'App_order', 'Back_orders', 'Reporting', 'Eng', 'Depot'])) {
        $sales_role = $role;
    }

    function sales_role($main_id, $db, $user_id) {
        $date = date('Y-m-d H:i:s');
        $sales_role = mysqli_real_escape_string($db, $_POST['sales_role']);

        if ($sales_role == 'TM') {
            $zm = mysqli_real_escape_string($db, $_POST['zm']);
            $query = "INSERT INTO `users_zm_tm` (`zm_id`, `tm_id`, `created_by`, `created_at`) 
                      VALUES ('$zm', '$main_id', '$user_id', '$date')";
            mysqli_query($db, $query);
        } elseif ($sales_role == 'ASM') {
            $tm = mysqli_real_escape_string($db, $_POST['tm']);
            $query = "INSERT INTO `users_asm_tm` (`tm_id`, `asm_id`, `created_by`, `created_at`) 
                      VALUES ('$tm', '$main_id', '$user_id', '$date')";
            mysqli_query($db, $query);
        }
    }

    function logistics($main_id, $db, $user_id) {
        $role = mysqli_real_escape_string($db, $_POST['logistics_role']);
        $date = date('Y-m-d H:i:s');
        $query = "INSERT INTO `users_logistics` (`role`, `logistics_id`, `created_by`, `created_at`) 
                  VALUES ('$role', '$main_id', '$user_id', '$date')";
        mysqli_query($db, $query);
    }

    function insertDepotEmails($main_id, $db, $user_id) {
        if (!empty($_POST['emails']) && is_array($_POST['emails'])) {
            $date = date('Y-m-d H:i:s');

            // Sanitize and join all emails into a comma-separated string
            $cleanedEmails = array_map(function($email) use ($db) {
                return mysqli_real_escape_string($db, $email);
            }, $_POST['emails']);

            $email_string = implode(',', $cleanedEmails);

            $query = "INSERT INTO users_depott (user_email, created_at, created_by) 
                      VALUES ('$email_string', '$date', '$user_id')";
            mysqli_query($db, $query);
        }
    }

    if ($_POST['row_id'] == '') {
        $parent_id = '';
        if ($sales_role == 'BSO') {
            $parent_id = mysqli_real_escape_string($db, $_POST['tm']);
        }

        $query = "INSERT INTO users (`name`, `privilege`, `login`, `password`, `usersettings_id`, `status`, `description`, `email`, `telephone`, `subacc_id`) 
                  VALUES ('$name', '$sales_role', '$email', '$password_enc', '1', '1', '$password', '$email', '$number', '$parent_id')";

        if (mysqli_query($db, $query)) {
            $main_id = mysqli_insert_id($db);

            // Attach roles
            if ($role == 'Logistics') {
                logistics($main_id, $db, $user_id);
            } elseif ($role == 'Sales') {
                sales_role($main_id, $db, $user_id);
            } elseif ($role == 'Depot') {
                insertDepotEmails($main_id, $db, $user_id);
            }

            echo 1;
        } else {
            echo 'Error: ' . mysqli_error($db) . '<br>' . $query;
        }
    }
}
?>
