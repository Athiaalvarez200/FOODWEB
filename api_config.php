<?php
// REST API base URL. Keep APIFOOD folder inside htdocs: C:\xampp\htdocs\APIFOOD\FoodAPI
$API_BASE = "http://127.0.0.1:8000/";

function api_get($endpoint) {
    global $API_BASE;
    $response = @file_get_contents($API_BASE . $endpoint);
    if ($response === false) return null;
    return json_decode($response, true);
}

function api_post($endpoint, $payload) {
    global $API_BASE;
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => json_encode($payload),
            'timeout' => 5
        ]
    ];
    $response = @file_get_contents($API_BASE . $endpoint, false, stream_context_create($opts));
    if ($response === false) return null;
    return json_decode($response, true);
}
?>
