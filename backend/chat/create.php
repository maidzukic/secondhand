<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser   = resolveAuthUser($conn);
$userId     = intval($authUser["id"]);
$otherUserId = intval($_POST["other_user_id"] ?? $_POST["seller_id"] ?? 0);
$productId  = intval($_POST["product_id"] ?? 0);

if (!$otherUserId || !$productId) {
    sendJSON(400, "other_user_id i product_id su obavezni");
}

if ($userId === $otherUserId) {
    sendJSON(400, "Ne možete započeti chat sa samim sobom");
}


$productStmt = $conn->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$productStmt->bind_param("i", $productId);
$productStmt->execute();
if ($productStmt->get_result()->num_rows === 0) {
    sendJSON(404, "Proizvod nije pronađen");
}


$existingStmt = $conn->prepare("
    SELECT id FROM chats
    WHERE product_id = ?
      AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))
    LIMIT 1
");
$existingStmt->bind_param("iiiii", $productId, $userId, $otherUserId, $otherUserId, $userId);
$existingStmt->execute();
$existing = $existingStmt->get_result()->fetch_assoc();

if ($existing) {
    sendJSON(200, ["chat_id" => intval($existing["id"]), "existing" => true]);
}

// Create new chat
$insertStmt = $conn->prepare("INSERT INTO chats (product_id, user1_id, user2_id, created_at) VALUES (?, ?, ?, NOW())");
$insertStmt->bind_param("iii", $productId, $userId, $otherUserId);

if (!$insertStmt->execute()) {
    sendJSON(500, "Greška prilikom kreiranja chata");
}

sendJSON(201, ["chat_id" => $conn->insert_id, "existing" => false]);
