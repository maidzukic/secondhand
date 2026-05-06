<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$productId = intval($_GET["product_id"] ?? 0);
if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}

$stmt = $conn->prepare("
    SELECT
        c.id, c.comment_text, c.created_at,
        u.id     AS user_id,
        u.name   AS user_name,
        u.avatar AS user_avatar
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.product_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $productId);
$stmt->execute();

$comments = [];
$result   = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

sendJSON(200, $comments);
