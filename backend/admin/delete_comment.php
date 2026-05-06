<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

requireAdmin($conn);

$commentId = intval($_POST["comment_id"] ?? 0);
if (!$commentId) {
    sendJSON(400, "comment_id je obavezan");
}

$stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
$stmt->bind_param("i", $commentId);

if (!$stmt->execute() || $stmt->affected_rows === 0) {
    sendJSON(404, "Komentar nije pronađen");
}

sendJSON(200, "Komentar obrisan");
