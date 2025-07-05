<?php
include("../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id    = $_POST['user_id'];
    $name       = mysqli_real_escape_string($db, $_POST['name']);
    $email      = mysqli_real_escape_string($db, $_POST['email']);
    $password   = mysqli_real_escape_string($db, $_POST['confirm_password']);
    $encrypted  = md5($password);
    $number     = mysqli_real_escape_string($db, $_POST['number']);

    // Get depot from dynamic select (single-select array)
    $depot = mysqli_real_escape_string($db, $_POST['depots'][0] ?? '');

    $role        = mysqli_real_escape_string($db, $_POST['role']);
    $sales_role  = mysqli_real_escape_string($db, $_POST['sales_role']); // This is the "privilege"

    // Parent ID logic for BSO
    $parent_id = '';
    if ($sales_role === 'BSO') {
        $parent_id = mysqli_real_escape_string($db, $_POST['tm'] ?? '');
    }

    $query = "INSERT INTO users_depot (
        `name`, `privilege`, `login`, `password`, `usersettings_id`, `status`, `description`, `email`, `depot`, `telephone`, `subacc_id`
    ) VALUES (
        '$name', '$sales_role', '$email', '$encrypted', '1', '1', '$password', '$email', '$depot', '$number', '$parent_id'
    )";

    if (mysqli_query($db, $query)) {
        $main_id = mysqli_insert_id($db);

        // Optional logic based on role — you can uncomment these later
        // if ($role === 'Logistics') logistics($main_id, $db, $user_id);
        // if ($role === 'Sales') sales_role($main_id, $db, $user_id);

        echo 1;
    } else {
        echo 'Error: ' . mysqli_error($db) . '<br>' . $query;
    }
}
?>
