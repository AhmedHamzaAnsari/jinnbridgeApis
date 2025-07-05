<?php
header("Content-Type: application/json");

// Connect to your MySQL database
include("../../hacol_conif_post.php");


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract data from the raw POST body
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $request_id = $data['request_id'];
        $ledger_data = $data['ledger_data'];

        // Convert the ledger_data array to a JSON string
        $ledger_data_json = json_encode($ledger_data);
        $datetime = date('Y-m-d H:i:s');
        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE `request_for_dealers_ledger` SET `ledger_json` = ?, `status` = '1',`update_at`='$datetime' WHERE `id` = ?");
        $stmt->bind_param('si', $ledger_data_json, $request_id);

        if ($stmt->execute()) {
            $output = json_encode(['message' => 'Update successful', 'request_id' => $request_id]);
        } else {
            $output = json_encode(['error' => 'Error updating record: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        $output = json_encode(['error' => 'Invalid JSON: ' . json_last_error_msg()]);
    }
} else {
    $output = json_encode(['error' => 'Invalid request method']);
}

// Close the connection
$conn->close();

// Return the JSON response
echo $output;
?>
