<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

requireAdmin($conn);

$search = clean($_GET["query"] ?? "");

$sql = "
    SELECT
        c.id, c.comment_text, c.created_at, c.product_id,
        u.id    AS author_id,
        u.name  AS author_name,
        u.email AS author_email,
        p.title AS product_title
    FROM comments c
    JOIN users u    ON c.user_id    = u.id
    JOIN products p ON c.product_id = p.id
";

$params = [];
$types  = "";

if ($search !== "") {
    $sql   .= " WHERE c.comment_text LIKE ? OR u.name LIKE ?";
    $like   = "%$search%";
    $params = [$like, $like];
    $types  = "ss";
}

$sql .= " ORDER BY c.created_at DESC LIMIT 500";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();

$comments = [];
$result   = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

sendJSON(200, $comments);
