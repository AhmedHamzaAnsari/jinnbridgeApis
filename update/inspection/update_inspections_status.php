<?php
include("../../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : null;
    $table_name = isset($_POST['table_name']) ? mysqli_real_escape_string($db, $_POST['table_name']) : null;

    if (!$task_id || !$table_name) {
        echo "Invalid input. Please provide valid task ID and table name.";
        exit;
    }

    $datetime = date('Y-m-d H:i:s');

    // Update query
    $query = "UPDATE `inspector_task` 
              SET `$table_name` = '1' 
              WHERE id = $task_id";

    // Execute the query and handle the result
    if (mysqli_query($db, $query)) {
        // Call emailers function if the query is successful
        emailers($task_id, $db, $table_name);
        echo 1; // Success
    } else {
        echo 'Error: ' . mysqli_error($db) . '<br>' . $query;
    }
}

function emailers($task_id, $db, $table_name)
{
    // Initialize variables
    $report_name = '';
    $date = date('Y-m-d H:i:s');

    // Determine report name based on table name
    switch ($table_name) {
        case 'stock_recon':
            $report_name = 'Stock Reconciliation';
            break;
        case 'inspection':
            $report_name = 'Inspection';
            break;
        default:
            $report_name = '';
            break;
    }

    // Fetch task details
    $sql_query1 = "SELECT * FROM inspector_task WHERE id = $task_id";
    $result1 = $db->query($sql_query1);

    if (!$result1) {
        die("Error fetching task details: " . $db->error);
    }

    // Process the task details
    if ($user = $result1->fetch_assoc()) {
        $dealer_id = $user['dealer_id'];
        $tm_id = $user['user_id'];

        // Insert into reports_emailers table
        $query = "INSERT INTO `reports_emailers` 
                  (`task_id`, `dealer_id`, `tm_id`, `report_name`, `created_at`) 
                  VALUES 
                  ('$task_id', '$dealer_id', '$tm_id', '$report_name', '$date')";

        if (!mysqli_query($db, $query)) {
            // Log error if insertion fails
            // error_log('Error inserting report emailer: ' . mysqli_error($db));
        }
    } else {
        // error_log("No task found with ID: $task_id");
    }
}
?>
