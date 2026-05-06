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

if (!$ownerRow) {
    sendJSON(404, "Proizvod nije pronađen");
}

if ($userId !== intval($ownerRow["user_id"]) && !$isAdmin) {
    sendJSON(403, "Niste vlasnik ovog proizvoda");
}


$countStmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM product_images WHERE product_id = ?");
$countStmt->bind_param("i", $productId);
$countStmt->execute();
$currentCount = intval($countStmt->get_result()->fetch_assoc()["cnt"]);
$remaining    = 10 - $currentCount;

if ($remaining <= 0) {
    sendJSON(400, "Maksimalan broj slika je već dostignut");
}

if (empty($_FILES["images"]["name"][0])) {
    sendJSON(400, "Nisu dostavljene slike");
}

$uploadDir        = __DIR__ . "/../uploads/products/$productId/";
$allowedExtensions = ["jpg", "jpeg", "png", "webp"];
$allowedMimes      = ["image/jpeg", "image/png", "image/webp"];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$files  = $_FILES["images"];
$total  = count($files["name"]);
$added  = 0;

for ($i = 0; $i < $total && $added < $remaining; $i++) {
    if ($files["error"][$i] !== UPLOAD_ERR_OK) {
        continue;
    }

    $ext  = strtolower(pathinfo($files["name"][$i], PATHINFO_EXTENSION));
    $mime = mime_content_type($files["tmp_name"][$i]);

    if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimes)) {
        continue;
    }

    $filename   = time() . "_" . rand(1000, 9999) . "." . $ext;
    $targetPath = $uploadDir . $filename;

    if (!move_uploaded_file($files["tmp_name"][$i], $targetPath)) {
        continue;
    }

    $relativePath = "uploads/products/$productId/$filename";
    $imgStmt      = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
    $imgStmt->bind_param("is", $productId, $relativePath);
    if ($imgStmt->execute()) {
        $added++;
    }
}

sendJSON(200, [
    "added"   => $added,
    "message" => "$added image(s) uploaded successfully",
]);
