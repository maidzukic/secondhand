<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

$stmt = $conn->prepare("
    SELECT
        c.id AS chat_id,
        c.product_id,
        c.user1_id,
        c.user2_id,
        c.created_at,

        p.title,
        p.price,
        (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS thumbnail,

        CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END AS other_user_id,

        (SELECT u.name   FROM users u WHERE u.id = CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END LIMIT 1) AS other_user_name,
        (SELECT u.avatar FROM users u WHERE u.id = CASE WHEN c.user1_id = ? THEN c.user2_id ELSE c.user1_id END LIMIT 1) AS other_user_avatar,

        (SELECT m.message_text FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) AS last_message,
        (SELECT m.created_at   FROM messages m WHERE m.chat_id = c.id ORDER BY m.created_at DESC LIMIT 1) AS last_message_at,
        (SELECT COUNT(*)        FROM messages m WHERE m.chat_id = c.id AND m.sender_id <> ? AND m.is_read = 0) AS unread_count

    FROM chats c
    JOIN products p ON c.product_id = p.id
    WHERE c.user1_id = ? OR c.user2_id = ?
    ORDER BY COALESCE(last_message_at, c.created_at) DESC
");

$stmt->bind_param("iiiiii", $userId, $userId, $userId, $userId, $userId, $userId);
$stmt->execute();

$chats  = [];
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row["unread_count"] = intval($row["unread_count"]);
    $chats[] = $row;
}

sendJSON(200, $chats);
