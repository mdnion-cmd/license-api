<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');
header('Access-Control-Allow-Methods: GET, POST');

// Your actual license API URL
$TARGET_URL = 'https://license.infinityfreeapp.com/license.php';

// Get all parameters from the request
$query_string = $_SERVER['QUERY_STRING'];

// Build the full URL
$full_url = $TARGET_URL . '?' . $query_string;

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $full_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Minecraft-License-Proxy/1.0');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Execute the request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_error($ch)) {
    echo "ERROR:" . curl_error($ch);
} else {
    // Return the actual response from your license system
    echo $response;
}

curl_close($ch);
?>
