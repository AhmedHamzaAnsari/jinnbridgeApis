<?php
include("../config.php");
session_start();

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate required POST parameters
    if (isset($_GET['id'])) {

        // Sanitize inputs
        $id = intval($_GET['id']); // Ensure $id is an integer
        $user_id = intval($_GET['user_id']); // Ensure $user_id is an integer
        $date = date('Y-m-d H:i:s');

        // SQL Query to fetch data
        $sql_query1 = "SELECT od.*, dl.name AS dealer_name, us.name AS username, dl.`co-ordinates` AS old_co 
                       FROM omcs_dealers AS od
                       JOIN dealers AS dl ON dl.id = od.old_dealer_id
                       JOIN users AS us ON us.id = od.created_by
                       WHERE od.id = $id";

        $result1 = mysqli_query($db, $sql_query1);

        if (!$result1) {
            die("Error executing query: " . mysqli_error($db));
        }

        $output = 0; // Default output

        while ($user = mysqli_fetch_assoc($result1)) {
            $row_id = intval($user['id']);
            $old_co = mysqli_real_escape_string($db, $user['old_co']);
            $new_co = mysqli_real_escape_string($db, $user['coordinates']);
            $dealer_id = intval($user['old_dealer_id']);

            // Update dealer coordinates
            $update_dealer_co = "UPDATE `dealers` SET `co-ordinates` = '$new_co' WHERE id = $dealer_id";
            if (mysqli_query($db, $update_dealer_co)) {

                // Update dealer status
                $query = "UPDATE `omcs_dealers` SET `status` = '1' WHERE id = $row_id";
                if (mysqli_query($db, $query)) {

                    // Insert log entry
                    $log = "INSERT INTO `omcs_dealers_log`
                            (`main_id`, `dealers_id`, `old_co`, `new_co`, `created_at`, `created_by`)
                            VALUES ('$row_id', '$dealer_id', '$old_co', '$new_co', '$date', '$user_id')";

                    if (mysqli_query($db, $log)) {
                        $output = 1; // Success
                    }
                }
            }
        }

        echo $output;

    } else {
        echo 11;
    }
// } else {
//     echo 0;
// }
?>
