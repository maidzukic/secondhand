<?php
require_once "../config/db.php";
require_once "../config/utils.php";


requireMethod("GET");

$search     = clean($_GET["query"]       ?? "");
$categoryId = intval($_GET["category_id"] ?? 0);
$location   = clean($_GET["location"]    ?? "");
$minPrice   = isset($_GET["minPrice"]) ? floatval($_GET["minPrice"]) : null;
$maxPrice   = isset($_GET["maxPrice"]) ? floatval($_GET["maxPrice"]) : null;
$sort       = clean($_GET["sort"]        ?? "newest");
$randomise  = intval($_GET["random"]     ?? 0);
$limit      = intval($_GET["limit"]      ?? 50);

if ($limit <= 0 || $limit > 200) {
    $limit = 50;
}

$conditions = [];
$params     = [];
$types      = "";

if ($search !== "") {
    $conditions[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= "ss";
}

if ($categoryId > 0) {
    $conditions[] = "p.category_id = ?";
    $params[] = $categoryId;
    $types   .= "i";
}

if ($location !== "") {
    $conditions[] = "p.location LIKE ?";
    $params[] = "%$location%";
    $types   .= "s";
}

if ($minPrice !== null) {
    $conditions[] = "p.price >= ?";
    $params[] = $minPrice;
    $types   .= "d";
}

if ($maxPrice !== null) {
    $conditions[] = "p.price <= ?";
    $params[] = $maxPrice;
    $types   .= "d";
}

$whereClause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$orderClause = "p.created_at DESC";
if ($sort === "price_asc")  $orderClause = "p.price ASC";
if ($sort === "price_desc") $orderClause = "p.price DESC";
if ($randomise === 1)       $orderClause = "RAND()";

$sql = "
    SELECT
        p.id,
        p.title,
        p.price,
        p.location,
        p.product_condition,
        p.created_at,
        c.name  AS category_name,
        u.id    AS seller_id,
        u.name  AS seller_name,
        (
            SELECT image_path
            FROM product_images
            WHERE product_id = p.id
            ORDER BY id ASC
            LIMIT 1
        ) AS thumbnail
    FROM products p
    JOIN users u      ON p.user_id     = u.id
    JOIN categories c ON p.category_id = c.id
    $whereClause
    ORDER BY $orderClause
    LIMIT ?
";

$params[] = $limit;
$types   .= "i";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result();

$products = [];
while ($row = $rows->fetch_assoc()) {
    $products[] = $row;
}

sendJSON(200, $products);
