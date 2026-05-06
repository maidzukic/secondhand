<?php
require_once "../config/db.php";
require_once "../config/utils.php";

requireMethod("GET");

$productId = intval($_GET["product_id"] ?? $_GET["id"] ?? 0);
if (!$productId) {
    sendJSON(400, "Product ID je obavezan");
}

$productStmt = $conn->prepare("
    SELECT
        p.id, p.title, p.description, p.price, p.location,
        p.product_condition, p.category_id, p.user_id, p.created_at,
        c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
    LIMIT 1
");
$productStmt->bind_param("i", $productId);
$productStmt->execute();
$product = $productStmt->get_result()->fetch_assoc();

if (!$product) {
    sendJSON(404, "Proizvod nije pronađen");
}


$imgStmt = $conn->prepare("SELECT id, image_path FROM product_images WHERE product_id = ? ORDER BY id ASC");
$imgStmt->bind_param("i", $productId);
$imgStmt->execute();
$images    = [];
$imgResult = $imgStmt->get_result();
while ($row = $imgResult->fetch_assoc()) {
    $images[] = $row;
}
$product["images"] = $images;


$sellerId   = intval($product["user_id"]);
$sellerStmt = $conn->prepare("SELECT id, name, avatar, bio, location, created_at FROM users WHERE id = ? LIMIT 1");
$sellerStmt->bind_param("i", $sellerId);
$sellerStmt->execute();
$seller = $sellerStmt->get_result()->fetch_assoc();

sendJSON(200, [
    "product" => $product,
    "seller"  => $seller,
]);
