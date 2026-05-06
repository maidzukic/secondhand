<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);
$chatId   = intval($_GET["chat_id"] ?? 0);

if (!$chatId) {
    sendJSON(400, "chat_id is required");
}


$accessStmt = $conn->prepare("SELECT id FROM chats WHERE id = ? AND (user1_id = ? OR user2_id = ?) LIMIT 1");
$accessStmt->bind_param("iii", $chatId, $userId, $userId);
$accessStmt->execute();
if ($accessStmt->get_result()->num_rows === 0) {
    sendJSON(403, "Pristup odbijen");
}

$stmt = $conn->prepare("
    SELECT
        m.id, m.message_text, m.sender_id,
        m.file_path, m.file_type, m.file_name,
        m.is_read, m.created_at,
        u.name   AS sender_name,
        u.avatar AS sender_avatar
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.chat_id = ?
    ORDER BY m.created_at ASC
");
$stmt->bind_param("i", $chatId);
$stmt->execute();

$messages = [];
$result   = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

sendJSON(200, $messages);
