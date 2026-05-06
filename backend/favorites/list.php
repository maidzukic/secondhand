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
        (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS thumbnail
    FROM favorites f
    JOIN products p ON f.product_id = p.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();

$favorites = [];
$result    = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $favorites[] = $row;
}

sendJSON(200, $favorites);
