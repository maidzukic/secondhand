<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

requireAdmin($conn);

$search = clean($_GET["query"] ?? "");

$sql = "
    SELECT
        u.id, u.name, u.email, u.role, u.location, u.created_at,
        (SELECT COUNT(*) FROM products p WHERE p.user_id = u.id) AS products_count
    FROM users u
";

$params = [];
$types  = "";

if ($search !== "") {
    $sql   .= " WHERE u.name LIKE ? OR u.email LIKE ?";
    $like   = "%$search%";
    $params = [$like, $like];
    $types  = "ss";
}

$sql .= " ORDER BY u.created_at DESC LIMIT 500";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();

$users  = [];
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row["products_count"] = intval($row["products_count"]);
    $users[] = $row;
}

sendJSON(200, $users);
