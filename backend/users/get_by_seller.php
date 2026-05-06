<?php
require_once "../config/db.php";
require_once "../config/utils.php";

requireMethod("GET");

$userId = intval($_GET["user_id"] ?? 0);
if (!$userId) {
    sendJSON(400, "user_id je obavezan");
}

$stmt = $conn->prepare("
    SELECT
        p.id, p.title, p.price, p.location, p.created_at,
        c.name AS category_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS thumbnail
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();

$products = [];
$result   = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

sendJSON(200, $products);
