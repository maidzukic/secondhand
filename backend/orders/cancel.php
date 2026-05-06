<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$buyerId  = intval($authUser["id"]);
$orderId  = intval($_POST["order_id"] ?? 0);

if (!$orderId) {
    sendJSON(400, "order_id je obavezan");
}

$stmt = $conn->prepare("SELECT id, status FROM orders WHERE id = ? AND buyer_id = ? LIMIT 1");
$stmt->bind_param("ii", $orderId, $buyerId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    sendJSON(404, "Narudžba nije pronađena");
}

if ($order["status"] !== "pending") {
    sendJSON(409, "Samo pending narudžbe mogu biti otkazane");
}

$updateStmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
$updateStmt->bind_param("i", $orderId);

if (!$updateStmt->execute()) {
    sendJSON(500, "Greška prilikom otkazivanja narudžbe");
}

sendJSON(200, "Narudžba otkazana");
