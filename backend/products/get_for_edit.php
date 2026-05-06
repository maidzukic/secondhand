<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser  = resolveAuthUser($conn);
$userId    = intval($authUser["id"]);
$isAdmin   = $authUser["role"] === "admin";
$productId = intval($_GET["product_id"] ?? 0);

if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}

$productStmt = $conn->prepare("
    SELECT id, user_id, category_id, title, description, price, location, product_condition, created_at
    FROM products
    WHERE id = ?
    LIMIT 1
");
$productStmt->bind_param("i", $productId);
$productStmt->execute();
$product = $productStmt->get_result()->fetch_assoc();

if (!$product) {
    sendJSON(404, "Proizvod nije pronađen");
}

if ($userId !== intval($product["user_id"]) && !$isAdmin) {
    sendJSON(403, "Nije vam dozvoljeno da uređujete ovaj proizvod");
}

$imgStmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY id ASC");
$imgStmt->bind_param("i", $productId);
$imgStmt->execute();
$images    = [];
$imgResult = $imgStmt->get_result();
while ($row = $imgResult->fetch_assoc()) {
    $images[] = $row;
}

sendJSON(200, [
    "product" => $product,
    "images"  => $images,
]);
