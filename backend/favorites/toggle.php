<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser  = resolveAuthUser($conn);
$userId    = intval($authUser["id"]);
$productId = intval($_POST["product_id"] ?? 0);

if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}


$productStmt = $conn->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$productStmt->bind_param("i", $productId);
$productStmt->execute();
if ($productStmt->get_result()->num_rows === 0) {
    sendJSON(404, "Proizvod nije pronađen");
}


$checkStmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1");
$checkStmt->bind_param("ii", $userId, $productId);
$checkStmt->execute();
$existing = $checkStmt->get_result()->fetch_assoc();

if ($existing) {
    $deleteStmt = $conn->prepare("DELETE FROM favorites WHERE id = ?");
    $favId      = intval($existing["id"]);
    $deleteStmt->bind_param("i", $favId);
    $deleteStmt->execute();

    sendJSON(200, ["favorite" => false, "message" => "Uklonjeno iz omiljenih"]);
} else {
    $insertStmt = $conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $insertStmt->bind_param("ii", $userId, $productId);

    if (!$insertStmt->execute()) {
        sendJSON(500, "Greška prilikom dodavanja u omiljene");
    }

    sendJSON(200, ["favorite" => true, "message" => "Dodano u omiljene"]);
}
