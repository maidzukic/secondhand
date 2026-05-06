<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

requireAdmin($conn);

$search = clean($_GET["query"] ?? "");

$sql = "
    SELECT
        p.id, p.title, p.price, p.location, p.created_at,
        u.id   AS seller_id,
        u.name AS seller_name,
        c.name AS category_name,
        (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS thumbnail
    FROM products p
    JOIN users u      ON u.id = p.user_id
    JOIN categories c ON c.id = p.category_id
";

$params = [];
$types  = "";

if ($search !== "") {
    $sql   .= " WHERE p.title LIKE ? OR u.name LIKE ?";
    $like   = "%$search%";
    $params = [$like, $like];
    $types  = "ss";
}

$sql .= " ORDER BY p.created_at DESC LIMIT 200";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();

$products = [];
$result   = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

sendJSON(200, $products);
