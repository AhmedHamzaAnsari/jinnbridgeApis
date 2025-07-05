<?php
include("../../config.php");
session_start();
set_time_limit(0); // Set execution time limit to unlimited

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $datetime = date('Y-m-d H:i:s'); // Ensure the datetime variable is defined here

    // Check if the CSV file is uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {

        // Get the uploaded file's temporary path
        $fileName = $_FILES['csv_file']['tmp_name'];

        // Check if the uploaded file is a CSV file
        if ($_FILES['csv_file']['type'] === 'text/csv' || mime_content_type($fileName) === 'text/plain') {

            // Open the CSV file
            if (($handle = fopen($fileName, "r")) !== FALSE) {

                // Skip the header row
                fgetcsv($handle);

                $success = true; // Initialize success flag
                $errors = []; // Initialize errors array

                // Loop through each row in the CSV file
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Extract data from CSV row
                    $dealer_name = $data[1];
                    $dealerr_code = $data[2];
                    $ledger = $data[3];

                    // SQL query to fetch dealer based on dealer code
                    $sql = "SELECT * FROM dealers WHERE sap_no='$dealerr_code'";

                    // Execute the query
                    $result = mysqli_query($db, $sql);
                    if ($result) {
                        $row = mysqli_fetch_array($result);
                        $count = mysqli_num_rows($result);

                        if ($count > 0) {
                            $dealer_id = $row['id'];
                            $old_ledger = $row['acount'];

                            // Update query for dealers
                            $query = "UPDATE `dealers` SET 
                                      `acount`='$ledger' WHERE id=$dealer_id";

                            // Execute the update query
                            if (!mysqli_query($db, $query)) {
                                $errors[] = 'Error updating dealer ID ' . $dealer_name . ': ' . mysqli_error($db);
                                $success = false; // Set success to false
                            } else {
                                // Insert query for dealer ledger log
                                $backlog = "INSERT INTO `dealer_ledger_log`
                                            (`dealer_id`,
                                             `old_ledger`,
                                             `new_ledger`,
                                             `datetime`,
                                             `description`,
                                             `created_at`,
                                             `created_by`)
                                            VALUES
                                            ('$dealer_id',
                                             '$old_ledger',
                                             '$ledger',
                                             '$datetime',
                                             'Update through importer',
                                             '$datetime',
                                             '$user_id');";

                                // Execute the insert query
                                if (!mysqli_query($db, $backlog)) {
                                    $errors[] = 'Error logging update for dealer ID ' . $dealer_name . ': ' . mysqli_error($db);
                                    $success = false; // Set success to false
                                }
                            }
                        } else {
                            $errors[] = "Dealer with code '$dealerr_code' not found.";
                        }
                    } else {
                        $errors[] = 'Error executing query: ' . mysqli_error($db);
                        $success = false; // Set success to false
                    }
                }

                // Close the file handle
                fclose($handle);

                // Prepare response
                if ($success) {
                    echo json_encode(["status" => "success", "message" => "File uploaded and data inserted successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "errors" => $errors]);
                }

            } else {
                echo json_encode(["status" => "error", "message" => "Error opening the file."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Please upload a valid CSV file."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded or there was an error uploading the file."]);
    }

    // Close the database connection
    $db->close();
}
?>
