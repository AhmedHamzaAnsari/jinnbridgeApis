<?php
$url = "https://goo.gl/maps/QDxKJGFNJtNEszWSA";

// Regular expression pattern to match latitude and longitude
$pattern = '/@(-?\d+\.\d+),(-?\d+\.\d+)/';

// Perform the regular expression match
if (preg_match($pattern, $url, $matches)) {
    // Extract latitude and longitude from the matched groups
    $latitude = $matches[1];
    $longitude = $matches[2];

    echo "Latitude: $latitude<br>";
    echo "Longitude: $longitude<br>";
} else {
    echo "Latitude and longitude not found in the URL.";
}
?><?php
$url = "https://goo.gl/maps/QDxKJGFNJtNEszWSA";

// Regular expression pattern to match latitude and longitude
$pattern = '/@(-?\d+\.\d+),(-?\d+\.\d+)/';

// Perform the regular expression match
if (preg_match($pattern, $url, $matches)) {
    // Extract latitude and longitude from the matched groups
    $latitude = $matches[1];
    $longitude = $matches[2];

    echo "Latitude: $latitude<br>";
    echo "Longitude: $longitude<br>";
} else {
    echo "Latitude and longitude not found in the URL.";
}
?><?php
$url = "https://goo.gl/maps/QDxKJGFNJtNEszWSA";

// Regular expression pattern to match latitude and longitude
$pattern = '/@(-?\d+\.\d+),(-?\d+\.\d+)/';

// Perform the regular expression match
if (preg_match($pattern, $url, $matches)) {
    // Extract latitude and longitude from the matched groups
    $latitude = $matches[1];
    $longitude = $matches[2];

    echo "Latitude: $latitude<br>";
    echo "Longitude: $longitude<br>";
} else {
    echo "Latitude and longitude not found in the URL.";
}
?>