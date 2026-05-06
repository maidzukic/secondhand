<?php

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["message" => "Pristup odbijen. Niste prjavljeno."]);
    exit();
}

$token = $matches[1]; 

?>