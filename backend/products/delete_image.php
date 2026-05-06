<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);
$isAdmin  = $authUser["role"] === "admin";
$imageId  = intval($_POST["image_id"] ?? 0);

if (!$imageId) {
    sendJSON(400, "image_id je obavezan");
}


$stmt = $conn->prepare("
    SELECT pi.image_path, p.user_id AS owner_id
    FROM product_images pi
    JOIN products p ON pi.product_id = p.id
    WHERE pi.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $imageId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    sendJSON(404, "Slika nije pronađena");
}

if ($userId !== intval($row["owner_id"]) && !$isAdmin) {
    sendJSON(403, "Niste vlasnik ove slike");
}

$deleteStmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
$deleteStmt->bind_param("i", $imageId);

if (!$deleteStmt->execute()) {
    sendJSON(500, "Greška prilikom brisanja slike");
}


$fullPath = __DIR__ . "/../" . $row["image_path"];
if (file_exists($fullPath)) {
    @unlink($fullPath);
}

sendJSON(200, ["message" => "Slika obrisana"]);
