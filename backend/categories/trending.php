<?php
require_once "../config/db.php";
require_once "../config/utils.php";

requireMethod("GET");

$result = $conn->query("
    SELECT
        c.id,
        c.name,
        COUNT(p.id) AS products_count
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id, c.name
    ORDER BY products_count DESC
    LIMIT 8
");

$categories = [];
while ($row = $result->fetch_assoc()) {
    $row["products_count"] = intval($row["products_count"]);
    $categories[] = $row;
}

sendJSON(200, $categories);
