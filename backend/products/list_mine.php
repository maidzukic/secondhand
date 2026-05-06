<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

$stmt = $conn->prepare("
    SELECT
        p.id, p.title, p.price, p.location, p.created_at,
        c.name AS category_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id LIMIT 1) AS thumbnail,
        (SELECT COUNT(*) FROM product_images WHERE product_id = p.id) AS images_count
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();

$products  = [];
$result    = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

sendJSON(200, $products);
