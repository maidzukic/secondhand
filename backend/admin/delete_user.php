<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");
$admin = requireAdmin($conn);
$adminId = intval($admin["id"]);


$targetId = intval($_POST["target_user_id"] ?? $_POST["delete_id"] ?? 0);

if (!$targetId) {
    sendJSON(400, "target_user_id je obavezan");
}

if ($adminId === $targetId) {
    sendJSON(400, "Ne možete obrisati sami sebe");
}


$imgStmt = $conn->prepare("
    SELECT pi.image_path 
    FROM product_images pi 
    JOIN products p ON pi.product_id = p.id 
    WHERE p.user_id = ?
");
$imgStmt->bind_param("i", $targetId);
$imgStmt->execute();
$imagePaths = [];
$imgResult = $imgStmt->get_result();
while ($row = $imgResult->fetch_assoc()) {
    $imagePaths[] = $row["image_path"];
}


$userRes = $conn->query("SELECT avatar FROM users WHERE id = $targetId");
$userData = $userRes->fetch_assoc();

$conn->begin_transaction();

try {
   
    $conn->query("DELETE FROM messages WHERE chat_id IN (SELECT id FROM chats WHERE user1_id = $targetId OR user2_id = $targetId)");
    
    
    $conn->query("DELETE FROM messages WHERE chat_id IN (SELECT id FROM chats WHERE product_id IN (SELECT id FROM products WHERE user_id = $targetId))");

    
    $conn->query("DELETE FROM chats WHERE user1_id = $targetId OR user2_id = $targetId OR product_id IN (SELECT id FROM products WHERE user_id = $targetId)");
    $conn->query("DELETE FROM favorites WHERE user_id = $targetId OR product_id IN (SELECT id FROM products WHERE user_id = $targetId)");
    $conn->query("DELETE FROM comments WHERE user_id = $targetId OR product_id IN (SELECT id FROM products WHERE user_id = $targetId)");
    $conn->query("DELETE FROM orders WHERE buyer_id = $targetId OR seller_id = $targetId");
    $conn->query("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE user_id = $targetId)");
    $conn->query("DELETE FROM products WHERE user_id = $targetId");
    
    
    $conn->query("DELETE FROM users WHERE id = $targetId");

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    sendJSON(500, "Brisanje nije uspjelo: " . $e->getMessage());
}


foreach ($imagePaths as $path) {
    $fullPath = __DIR__ . "/../" . $path;
    if (file_exists($fullPath)) @unlink($fullPath);
}


if (!empty($userData['avatar'])) {
    $avatarPath = __DIR__ . "/../" . $userData['avatar'];
    if (file_exists($avatarPath)) @unlink($avatarPath);
}

sendJSON(200, "Korisnik obrisan uspješno");