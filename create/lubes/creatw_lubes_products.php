<?php
include("../../config.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $code = mysqli_real_escape_string($db, $_POST["code"]);
    $category = mysqli_real_escape_string($db, $_POST["category"]);
    $sizes = mysqli_real_escape_string($db, $_POST["sizes"]);
    $price = mysqli_real_escape_string($db, $_POST["price"]);
    $date = date('Y-m-d H:i:s');
    $output = '';

    // File upload handling
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Allowed file extensions
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        $file_name = $_FILES['product_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Check if file type is allowed
        if (in_array($file_ext, $allowed_extensions)) {
            $file = rand(1000, 100000) . "-" . $file_name;
            $file_loc = $_FILES['product_image']['tmp_name'];
            $folder = "../../../jinnBridge_files/uploads/";

            // Move the file to the upload folder
            if (move_uploaded_file($file_loc, $folder . $file)) {
                $image_path = $folder . $file;
            } else {
                echo "File upload failed.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
            exit;
        }
    } else {
        $image_path = ''; // Set to empty if no image is uploaded
    }

    // Check if we're updating or inserting a new record
    if (!empty($_POST["row_id"])) {
        $id = mysqli_real_escape_string($db, $_POST["row_id"]);

        // Update query
        $query = "UPDATE `lubes_product`
                  SET
                  `code` = '$code',
                  `name` = '$name',
                  `cat_id` = '$category',
                  `size_id` = '$sizes',
                  `price` = '$price',
                  `image` = '$file'
                  WHERE `id` = '$id';";
    } else {
        // Insert query
        $query = "INSERT INTO `lubes_product`
                  (`code`, `name`, `cat_id`, `size_id`, `price`, `image`, `created_at`, `created_by`)
                  VALUES
                  ('$code', '$name', '$category', '$sizes', '$price', '$file', '$date', '$user_id');";
    }

    // Execute the query
    if (mysqli_query($db, $query)) {
        $output = 1; // Success
    } else {
        $output = 'Error: ' . mysqli_error($db) . '<br>Query: ' . $query;
    }

    // Output the result
    echo $output;
}
?>
