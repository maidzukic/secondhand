<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("GET");

requireAdmin($conn);

$userCount    = intval($conn->query("SELECT COUNT(*) AS cnt FROM users")->fetch_assoc()["cnt"]);
$productCount = intval($conn->query("SELECT COUNT(*) AS cnt FROM products")->fetch_assoc()["cnt"]);
$commentCount = intval($conn->query("SELECT COUNT(*) AS cnt FROM comments")->fetch_assoc()["cnt"]);
$orderCount   = intval($conn->query("SELECT COUNT(*) AS cnt FROM orders")->fetch_assoc()["cnt"]);

sendJSON(200, [
    "users"    => $userCount,
    "products" => $productCount,
    "comments" => $commentCount,
    "orders"   => $orderCount,
]);
