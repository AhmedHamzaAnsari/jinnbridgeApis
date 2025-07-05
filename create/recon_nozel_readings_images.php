<?php
include("../config.php");
session_start();

// Adjust PHP configuration to allow larger file uploads
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_execution_time', '300'); // Increase max execution time if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data securely
    $user_id = mysqli_real_escape_string($db, $_POST['user_id']);
    $task_id = mysqli_real_escape_string($db, $_POST["task_id"]);
    $nozel_id = mysqli_real_escape_string($db, $_POST["nozel_id"]);
    $dealer_id = mysqli_real_escape_string($db, $_POST["dealer_id"]);
    $tdate = date('Y-m-d H:i:s');

    // Check if file was uploaded without errors
    if (isset($_FILES['files']) && $_FILES['files']['error'] == UPLOAD_ERR_OK) {
        // Sanitize file name and get file properties
        $file_name = basename($_FILES['files']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'); // Allowed file types

        // Check if the file extension is valid
        if (in_array($file_ext, $allowed_ext)) {
            $file_size = $_FILES['files']['size'];
            
            // Check if file size is acceptable (max 128 MB)
            if ($file_size <= 134217728) { // 128 MB in bytes
                $new_file_name = rand(1000, 100000) . "-" . $file_name;
                $file_loc = $_FILES['files']['tmp_name'];
                $upload_dir = "../../jinnBridge_files/uploads/";
                $file_path = $upload_dir . $new_file_name;

                // Move the uploaded file to the destination folder
                if (move_uploaded_file($file_loc, $file_path)) {
                    // Insert into the database
                    $query_main = "INSERT INTO `dealer_stock_recon_new_files`
                    (`task_id`,
                    `nozel_id`,
                    `dealer_id`,
                    `file`,
                    `created_at`,
                    `created_by`)
                    VALUES
                    ('$task_id',
                    '$nozel_id',
                    '$dealer_id',
                    '$new_file_name',
                    '$tdate',
                    '$user_id');";

                    if (mysqli_query($db, $query_main)) {
                        echo 1; // Success
                    } else {
                        echo 'Error: ' . mysqli_error($db); // Database insertion error
                    }
                } else {
                    echo 'Error: Failed to move uploaded file.'; // File move error
                }
            } else {
                echo 'Error: File size exceeds the 128 MB limit.';
            }
        } else {
            echo 'Error: Invalid file type. Allowed types: ' . implode(', ', $allowed_ext);
        }
    } else {
        echo 'Error: No file uploaded or file upload error occurred.';
    }
} else {
    echo 'Error: Invalid request method.';
}
?>