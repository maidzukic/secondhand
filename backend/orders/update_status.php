<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser  = resolveAuthUser($conn);
$sellerId  = intval($authUser["id"]);
$orderId   = intval($_POST["order_id"] ?? 0);
$newStatus = clean($_POST["status"] ?? "");

if (!$orderId || !$newStatus) {
    sendJSON(400, "order_id i status su obavezni");
}

$allowedStatuses = ["accepted", "rejected"];
if (!in_array($newStatus, $allowedStatuses)) {
    sendJSON(400, "Status must be 'accepted' or 'rejected'");
}

$stmt = $conn->prepare("SELECT id, status FROM orders WHERE id = ? AND seller_id = ? LIMIT 1");
$stmt->bind_param("ii", $orderId, $sellerId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    sendJSON(404, "Narudžba nije pronađena");
}

if ($order["status"] !== "pending") {
    sendJSON(409, "Samo pending narudžbe mogu biti ažurirane");
}

$updateStmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$updateStmt->bind_param("si", $newStatus, $orderId);

if (!$updateStmt->execute()) {
    sendJSON(500, "Greška prilikom ažuriranja statusa narudžbe");
}

sendJSON(200, ["message" => "Status narudžbe ažuriran", "status" => $newStatus]);
