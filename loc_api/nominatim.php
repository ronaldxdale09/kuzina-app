<?php
// nominatim-proxy.php - Place this file in your functions directory

// Set headers to allow access from your domain
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Ensure we have the required parameters
if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters (lat and lon)']);
    exit;
}

// Get parameters
$lat = floatval($_GET['lat']);
$lon = floatval($_GET['lon']);

// Validate coordinates
if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid coordinates']);
    exit;
}

// Construct the Nominatim URL
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}&addressdetails=1";

// Set up cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'KuzinaFoodDelivery/1.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Execute the request
$response = curl_exec($ch);
$error = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle errors
if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to connect to geocoding service: ' . $error]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Geocoding service returned error code: ' . $httpCode]);
    exit;
}

// Return the response from Nominatim
echo $response;
