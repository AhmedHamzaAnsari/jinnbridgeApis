<?php
// fetch.php  
include("../config.php");

$access_key = '03201232927';

$pass = $_GET["key"];
if ($pass != '') {
    if ($pass == $access_key) {

        $tm_id = $_GET["tm_id"];
        $month = $_GET['month']; // Get the selected month in 'YYYY-MM' format

        // Extract the year and month from the selected month
        list($year, $month) = explode('-', $month);

        // Find the number of days in the selected month
        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Initialize an array to store dates and days
        $dates_and_days = [];

        // Query to get the user information
        $sql_u = "SELECT * FROM users WHERE id=$tm_id;";
        $result_u = $db->query($sql_u);

        if ($result_u->num_rows > 0) {
            $row_u = $result_u->fetch_assoc();
            $user_id = $row_u["id"];
            $user_name = $row_u["name"];

            // Loop through each day of the month
            for ($day = 1; $day <= $number_of_days; $day++) {
                // Format the day with leading zeros
                $formatted_day = str_pad($day, 2, '0', STR_PAD_LEFT);
    
                // Create a date string in the format YYYY-MM-DD
                $date = "$year-$month-$formatted_day";
    
                // Get the day of the week (e.g., Monday, Tuesday)
                $day_of_week = date('l', strtotime($date));
    
                // Initialize empty values for inspection and casual dealers
                $inspection_dealers = '---';
                $casual_dealers = '---';
    
                // Query the database for data on this date
                $sql = "SELECT 
                    GROUP_CONCAT(CASE WHEN it.type = 'Inpection' THEN dl.name END ORDER BY it.time SEPARATOR ', ') AS inspection_dealers,
                    GROUP_CONCAT(CASE WHEN it.type = 'Casual' THEN dl.name END ORDER BY it.time SEPARATOR ', ') AS casual_dealers,
                    us.name as user_name
                FROM inspector_task as it
                JOIN dealers as dl ON dl.id = it.dealer_id
                JOIN users as us ON us.id = it.user_id
                WHERE 
                    it.user_id = '$tm_id' 
                    AND DATE(it.time) = '$date'
                GROUP BY DATE_FORMAT(it.time, '%d')";
    
                $result = $db->query($sql);
    
                if ($row = $result->fetch_assoc()) {
                    $inspection_dealers = $row["inspection_dealers"];
                    $casual_dealers = $row["casual_dealers"];
                }
    
                // Store the date, day, and data (even if empty) in the array
                $dates_and_days[] = [
                    'date' => $date,
                    'day' => $day_of_week,
                    'inspection_dealers' => $inspection_dealers,
                    'casual_dealers' => $casual_dealers,
                    'user_name' => $user_name
                ];
            }
        } else {
            echo 'User not found';
            exit;
        }

        // Output the result as JSON
        echo json_encode($dates_and_days);

    } else {
        echo 'Wrong Key...';
    }
} else {
    echo 'Key is Required';
}
?>
