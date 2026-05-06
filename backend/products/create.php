<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

$title       = clean($_POST["title"]       ?? "");
$description = clean($_POST["description"] ?? "");
$price       = floatval($_POST["price"]    ?? 0);
$categoryId  = intval($_POST["category_id"] ?? 0);
$location    = clean($_POST["location"]    ?? "");
$condition   = clean($_POST["condition"]   ?? "used");

if (!$title || !$description || !$price || !$categoryId) {
    sendJSON(400, "Naslov, opis, cijena i kategorija su obavezni");
}

if (!in_array($condition, ["new", "used"])) {
    $condition = "used";
}

$stmt = $conn->prepare("
    INSERT INTO products (user_id, category_id, title, description, price, location, product_condition)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("iissdss", $userId, $categoryId, $title, $description, $price, $location, $condition);

if (!$stmt->execute()) {
    sendJSON(500, "Greška prilikom kreiranja proizvoda");
}

$productId = $stmt->insert_id;


if (!empty($_FILES["images"]["name"][0])) {
    $fileCount = count($_FILES["images"]["name"]);

    if ($fileCount > 10) {
        sendJSON(400, "Dozvoljeno je maksimalno 10 slika");
    }

    $uploadFolder = "../uploads/products/$productId/";
    if (!is_dir($uploadFolder)) {
        mkdir($uploadFolder, 0755, true);
    }

    $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

    for ($i = 0; $i < $fileCount; $i++) {
        $tmpPath  = $_FILES["images"]["tmp_name"][$i];
        $origName = $_FILES["images"]["name"][$i];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            continue;
        }

        
        $mime = mime_content_type($tmpPath);
        $allowedMimes = ["image/jpeg", "image/png", "image/webp"];
        if (!in_array($mime, $allowedMimes)) {
            continue;
        }

        $filename   = time() . "_" . rand(1000, 9999) . "." . $ext;
        $targetPath = $uploadFolder . $filename;

        if (move_uploaded_file($tmpPath, $targetPath)) {
            $dbPath   = "uploads/products/$productId/$filename";
            $imgStmt  = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
            $imgStmt->bind_param("is", $productId, $dbPath);
            $imgStmt->execute();
        }
    }
}

sendJSON(201, [
    "message"    => "Proizvod kreiran",
    "product_id" => $productId,
]);
