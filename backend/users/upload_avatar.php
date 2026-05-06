<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

if (!isset($_FILES["avatar"]) || $_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
    sendJSON(400, "Avatar file is required");
}

$allowedExtensions = ["jpg", "jpeg", "png", "webp"];
$allowedMimes      = ["image/jpeg", "image/png", "image/webp"];
$ext               = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
$mime              = mime_content_type($_FILES["avatar"]["tmp_name"]);

if (!in_array($ext, $allowedExtensions) || !in_array($mime, $allowedMimes)) {
    sendJSON(400, "Only JPG, PNG and WEBP images are allowed");
}

$uploadDir = __DIR__ . "/../uploads/avatars/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename   = "avatar_{$userId}_" . time() . "." . $ext;
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetPath)) {
    sendJSON(500, "Upload failed");
}

$relativePath = "uploads/avatars/" . $filename;

$stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
$stmt->bind_param("si", $relativePath, $userId);

if (!$stmt->execute()) {
    sendJSON(500, "Failed to save avatar path");
}

sendJSON(200, ["avatar" => $relativePath]);
