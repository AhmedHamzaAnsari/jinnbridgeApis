<?php
//fetch.php  
include("../config.php");
ini_set('max_execution_time', 1000);


$access_key = '03201232927';

$pass = $_GET["key"];
$date = date('Y-m-d H:i:s');
if ($pass != '') {
    if ($pass == $access_key) {
        $sql_query1 = "SELECT * FROM dealers where `housekeeping`!='';";

        $result1 = $db->query($sql_query1) or die("Error :" . mysqli_error());

        $thread = array();
        while ($user = $result1->fetch_assoc()) {

            $id = $user['id'];
            $co_ordinates = $user['housekeeping'];

            $coordinate = latlng($co_ordinates);
            echo "Co - : " .$coordinate ."<br>";
            $sql_query = "UPDATE `dealers`
            SET
            `co-ordinates` = '$coordinate'
            WHERE `id` = '$id';";

            if (mysqli_query($db, $sql_query)) {
                echo "Updated....";
            }else{
                echo "Not Updated....";
            }

            $result = $db->query($sql_query) or die("Error :" . mysqli_error());



        }


    } else {
        echo 'Wrong Key...';
    }

} else {
    echo 'Key is Required';
}

function latlng($co)
{
    $googleMapsLink = $co;

    // Get the final URL after following redirection
    $finalUrl = get_final_url($googleMapsLink);

    // Regular expression pattern to extract latitude and longitude from the final URL
    $pattern = '/@(-?\d+(\.\d+)?),(-?\d+(\.\d+)?),/';

    // Perform the regular expression match on the final URL
    if (preg_match($pattern, $finalUrl, $matches)) {
        // Extracted latitude and longitude
        $latitude = $matches[1];
        $longitude = $matches[3];

        // Output the latitude and longitude
        // echo "Latitude: $latitude<br>";
        // echo "Longitude: $longitude";
        $lt_lg = $latitude . ', ' . $longitude;
        return $lt_lg;
    } else {
        // No latitude and longitude found in the link
        echo "Latitude and longitude not found in the link.";
    }

    // Function to follow redirection and get the final URL

}

function get_final_url($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return $finalUrl;
}

?>