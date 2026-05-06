<?php
require_once "../config/db.php";
require_once "../config/utils.php";

requireMethod("GET");

$userId = intval($_GET["user_id"] ?? 0);
if (!$userId) {
    sendJSON(400, "user_id je obavezan");
}

$stmt = $conn->prepare("
    SELECT id, name, location, bio, avatar, created_at AS joined_at
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    sendJSON(404, "Korisnik nije pronađen");
}

sendJSON(200, $user);
