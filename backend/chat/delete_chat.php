<?php

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);
$chatId   = intval($_POST["chat_id"] ?? 0);

if (!$chatId) {
    sendJSON(400, "chat_id je obavezan");
}


$accessStmt = $conn->prepare("SELECT id FROM chats WHERE id = ? AND (user1_id = ? OR user2_id = ?) LIMIT 1");
$accessStmt->bind_param("iii", $chatId, $userId, $userId);
$accessStmt->execute();

if ($accessStmt->get_result()->num_rows === 0) {
    sendJSON(403, "Nemate dozvolu za brisanje ovog razgovora");
}

$stmt = $conn->prepare("DELETE FROM chats WHERE id = ?");
$stmt->bind_param("i", $chatId);

if ($stmt->execute()) {
    sendJSON(200, "Razgovor je uspješno obrisan");
} else {
    sendJSON(500, "Greška pri brisanju razgovora");
}