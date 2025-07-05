<?php
//fetch.php
include("../../config.php");

// Define the access key
$access_key = '03201232927';

// Retrieve and validate the key
$pass = $_GET["key"] ?? '';

if (!empty($pass)) {
    if ($pass === $access_key) {
        // Get task_id and validate it
        $task_id = $_GET["task_id"] ?? '';

        if (!empty($task_id)) {
            // Use prepared statements for secure querying
            $sql_query = "SELECT tr.*, us.name as rm_name 
                          FROM inspector_task_response as tr
                         left JOIN users as us ON us.id = tr.approved_by 
                          WHERE task_id = ?";
            
            $stmt = $db->prepare($sql_query);
            if ($stmt) {
                $stmt->bind_param("s", $task_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $thread = array();
                while ($user = $result->fetch_assoc()) {
                    $thread[] = $user;
                }

                // Output the result as JSON
                echo json_encode($thread);

                $stmt->close();
            } else {
                // Error in preparing the SQL statement
                echo json_encode(["error" => "Failed to prepare the SQL query."]);
            }
        } else {
            echo json_encode(["error" => "Task ID is required."]);
        }
    } else {
        echo json_encode(["error" => "Invalid access key."]);
    }
} else {
    echo json_encode(["error" => "Access key is required."]);
}

?>
