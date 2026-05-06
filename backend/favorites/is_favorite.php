<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser  = resolveAuthUser($conn);
$userId    = intval($authUser["id"]);
$productId = intval($_GET["product_id"] ?? 0);

if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}

$stmt = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1");
$stmt->bind_param("ii", $userId, $productId);
$stmt->execute();

sendJSON(200, ["favorite" => $stmt->get_result()->num_rows > 0]);
