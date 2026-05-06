<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser  = resolveAuthUser($conn);
$userId    = intval($authUser["id"]);
$productId = intval($_POST["product_id"] ?? 0);
$comment   = clean($_POST["comment"] ?? "");

if (!$productId || !$comment) {
    sendJSON(400, "product_id i tekst komentara su obavezni");
}


$productStmt = $conn->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$productStmt->bind_param("i", $productId);
$productStmt->execute();
if ($productStmt->get_result()->num_rows === 0) {
    sendJSON(404, "Proizvod nije pronađen");
}

$stmt = $conn->prepare("INSERT INTO comments (product_id, user_id, comment_text) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $productId, $userId, $comment);

if (!$stmt->execute()) {
    sendJSON(500, "Greška prilikom postavljanja komentara");
}

sendJSON(201, ["message" => "Komentar postavljen", "id" => $conn->insert_id]);
