<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser = resolveAuthUser($conn);
$userId   = intval($authUser["id"]);

$name     = clean($_POST["name"]     ?? "");
$location = clean($_POST["location"] ?? "");
$bio      = clean($_POST["bio"]      ?? "");

if (!$name) {
    sendJSON(400, "Ime je obavezno");
}

$stmt = $conn->prepare("UPDATE users SET name = ?, location = ?, bio = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $location, $bio, $userId);

if (!$stmt->execute()) {
    sendJSON(500, "Greška prilikom ažuriranja profila");
}

sendJSON(200, ["message" => "Profil ažuriran"]);
