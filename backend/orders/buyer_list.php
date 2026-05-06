<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser = resolveAuthUser($conn);
$buyerId  = intval($authUser["id"]);

$stmt = $conn->prepare("
    SELECT
        o.id, o.product_id, o.buyer_id, o.seller_id, o.status, o.created_at,
        p.title, p.price, p.location,
        su.name AS seller_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS thumbnail,
        (SELECT c.id FROM chats c
            WHERE c.product_id = p.id
              AND ((c.user1_id = o.buyer_id AND c.user2_id = o.seller_id)
                OR (c.user1_id = o.seller_id AND c.user2_id = o.buyer_id))
            LIMIT 1
        ) AS chat_id
    FROM orders o
    JOIN products p  ON o.product_id = p.id
    JOIN users su    ON o.seller_id  = su.id
    WHERE o.buyer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $buyerId);
$stmt->execute();

$orders = [];
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

sendJSON(200, $orders);
