<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");
requireAdmin($conn);

$productId = intval($_POST["product_id"] ?? 0);
if (!$productId) {
    sendJSON(400, "product_id je obavezan");
}


$imgStmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$imgStmt->bind_param("i", $productId);
$imgStmt->execute();
$imagePaths = [];
$imgResult  = $imgStmt->get_result();
while ($row = $imgResult->fetch_assoc()) {
    $imagePaths[] = $row["image_path"];
}

$conn->begin_transaction();

try {
    $tables = ['favorites', 'comments', 'orders', 'product_images'];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $productId);
            $stmt->execute();
        }
    }

    $chatStmt = $conn->prepare("SELECT id FROM chats WHERE product_id = ?");
    $chatStmt->bind_param("i", $productId);
    $chatStmt->execute();
    $chatResult = $chatStmt->get_result();
    while ($chat = $chatResult->fetch_assoc()) {
        $chatId = $chat['id'];
        $conn->query("DELETE FROM messages WHERE chat_id = $chatId");
    }
    
  
    $conn->query("DELETE FROM chats WHERE product_id = $productId");


    $finalStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $finalStmt->bind_param("i", $productId);
    $finalStmt->execute();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    sendJSON(500, "Brisanje nije uspjelo: " . $e->getMessage());
}

foreach ($imagePaths as $relativePath) {
    $full = __DIR__ . "/../" . $relativePath;
    if (file_exists($full)) {
        @unlink($full);
    }
}

sendJSON(200, "Proizvod obrisan uspješno");