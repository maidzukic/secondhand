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

$title       = clean($_POST["title"]             ?? "");
$description = clean($_POST["description"]       ?? "");
$price       = floatval($_POST["price"]          ?? 0);
$categoryId  = intval($_POST["category_id"]      ?? 0);
$location    = clean($_POST["location"]          ?? "");
$condition   = clean($_POST["product_condition"] ?? "used");

if (!$title || !$description || !$price || !$categoryId || !$location) {
    sendJSON(400, "Naslov, opis, cijena, kategorija i lokacija su obavezni");
}

if (!in_array($condition, ["new", "used"])) {
    $condition = "used";
}


$ownerStmt = $conn->prepare("SELECT user_id FROM products WHERE id = ? LIMIT 1");
$ownerStmt->bind_param("i", $productId);
$ownerStmt->execute();
$ownerRow = $ownerStmt->get_result()->fetch_assoc();

if (!$ownerRow) {
    sendJSON(404, "Proizvod nije pronađen");
}

if ($userId !== intval($ownerRow["user_id"]) && !$isAdmin) {
    sendJSON(403, "Nije vam dozvoljeno da uređujete ovaj proizvod");
}

$updateStmt = $conn->prepare("
    UPDATE products
    SET title = ?, description = ?, price = ?, category_id = ?, location = ?, product_condition = ?
    WHERE id = ?
");
$updateStmt->bind_param("ssdissi", $title, $description, $price, $categoryId, $location, $condition, $productId);

if (!$updateStmt->execute()) {
    sendJSON(500, "Ažuriranje nije uspjelo");
}

sendJSON(200, ["message" => "Proizvod ažuriran"]);
