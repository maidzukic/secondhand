<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$senderId = intval($authUser["id"]);
$chatId   = intval($_POST["chat_id"] ?? 0);
$text     = clean($_POST["message"] ?? "");

if (!$chatId) {
    sendJSON(400, "chat_id je obavezan");
}


$accessStmt = $conn->prepare("SELECT id FROM chats WHERE id = ? AND (user1_id = ? OR user2_id = ?) LIMIT 1");
$accessStmt->bind_param("iii", $chatId, $senderId, $senderId);
$accessStmt->execute();
if ($accessStmt->get_result()->num_rows === 0) {
    sendJSON(403, "Pristup odbijen");
}

$hasFile = isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK;

if (!$text && !$hasFile) {
    sendJSON(400, "Tekst poruke ili priloženi fajl su obavezni");
}

$filePath = null;
$fileType = null;
$fileName = null;


if ($hasFile) {
    $allowedImages    = ["jpg", "jpeg", "png", "gif", "webp"];
    $allowedDocuments = ["pdf", "doc", "docx", "txt", "zip", "rar"];
    $allowedAll       = array_merge($allowedImages, $allowedDocuments);

    $originalName = $_FILES["file"]["name"];
    $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedAll)) {
        sendJSON(400, "Tip fajla nije dozvoljen");
    }

    $uploadDir = __DIR__ . "/../uploads/chat/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFilename = "msg_{$chatId}_" . time() . "_" . rand(1000, 9999) . "." . $ext;
    $destination = $uploadDir . $newFilename;

    if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
        sendJSON(500, "Greška prilikom uploada fajla");
    }

    $filePath = "uploads/chat/" . $newFilename;
    $fileName = $originalName;
    $fileType = in_array($ext, $allowedImages) ? "image" : "file";

  
    if (!$text) {
        $text = $originalName;
    }
}

$stmt = $conn->prepare("
    INSERT INTO messages (chat_id, sender_id, message_text, file_path, file_type, file_name, created_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("iissss", $chatId, $senderId, $text, $filePath, $fileType, $fileName);

if (!$stmt->execute()) {
    sendJSON(500, "Greška prilikom slanja poruke");
}

sendJSON(201, [
    "id"        => $conn->insert_id,
    "message"   => "Poruka poslana",
    "file_path" => $filePath,
    "file_type" => $fileType,
    "file_name" => $fileName,
]);
