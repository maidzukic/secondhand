<?php
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


$accessStmt = $conn->prepare("SELECT user1_id, user2_id FROM chats WHERE id = ? LIMIT 1");
$accessStmt->bind_param("i", $chatId);
$accessStmt->execute();
$chat = $accessStmt->get_result()->fetch_assoc();

if (!$chat) {
    sendJSON(404, "Chat nije pronađen");
}

if ($userId !== intval($chat["user1_id"]) && $userId !== intval($chat["user2_id"])) {
    sendJSON(403, "Pristup odbijen");
}


$updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE chat_id = ? AND sender_id <> ?");
$updateStmt->bind_param("ii", $chatId, $userId);
$updateStmt->execute();

sendJSON(200, ["marked" => $updateStmt->affected_rows]);
