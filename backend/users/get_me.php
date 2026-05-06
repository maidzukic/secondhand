<?php
require_once "../config/db.php";
require_once "../config/utils.php";

requireMethod("GET");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

$stmt = $conn->prepare("
    SELECT id, name, email, role, location, bio, avatar, created_at AS joined_at
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
