<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser  = resolveAuthUser($conn);
$userId    = intval($authUser["id"]);
$isAdmin   = $authUser["role"] === "admin";
$productId = intval($_POST["product_id"] ?? 0);

if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}

$ownerStmt = $conn->prepare("SELECT user_id FROM products WHERE id = ? LIMIT 1");
$ownerStmt->bind_param("i", $productId);
$ownerStmt->execute();
$ownerRow = $ownerStmt->get_result()->fetch_assoc();

if (!$ownerRow) sendJSON(404, "Proizvod nije pronađen");

if ($userId !== intval($ownerRow["user_id"]) && !$isAdmin) {
    sendJSON(403, "Nije dozvoljeno");
}


$imgStmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$imgStmt->bind_param("i", $productId);
$imgStmt->execute();
$imagePaths = array_column($imgStmt->get_result()->fetch_all(MYSQLI_ASSOC), "image_path");

$chatStmt = $conn->prepare("SELECT id FROM chats WHERE product_id = ?");
$chatStmt->bind_param("i", $productId);
$chatStmt->execute();
$chatIds = array_column($chatStmt->get_result()->fetch_all(MYSQLI_ASSOC), "id");

$conn->begin_transaction();

try {
    $conn->prepare("DELETE FROM favorites WHERE product_id = ?")->bind_param("i", $productId)->execute();
    $conn->prepare("DELETE FROM comments  WHERE product_id = ?")->bind_param("i", $productId)->execute();
    $conn->prepare("DELETE FROM orders    WHERE product_id = ?")->bind_param("i", $productId)->execute();

    if ($chatIds) {
        $ph  = implode(",", array_fill(0, count($chatIds), "?"));
        $t   = str_repeat("i", count($chatIds));
        $conn->prepare("DELETE FROM messages WHERE chat_id IN ($ph)")->bind_param($t, ...$chatIds)->execute();
    }

    $conn->prepare("DELETE FROM chats          WHERE product_id = ?")->bind_param("i", $productId)->execute();
    $conn->prepare("DELETE FROM product_images WHERE product_id = ?")->bind_param("i", $productId)->execute();
    $conn->prepare("DELETE FROM products       WHERE id = ?")->bind_param("i", $productId)->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    sendJSON(500, "Brisanje nije uspjelo");
}

foreach ($imagePaths as $rel) {
    $abs = __DIR__ . "/../" . $rel;
    if (file_exists($abs)) @unlink($abs);
}

$folder = __DIR__ . "/../uploads/products/$productId/";
if (is_dir($folder)) {
    array_map('unlink', glob($folder . "*"));
    @rmdir($folder);
}

sendJSON(200, "Proizvod obrisan");
