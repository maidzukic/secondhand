<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$me        = resolveAuthUser($conn);
$userId    = intval($me["id"]);
$isAdmin   = $me["role"] === "admin";
$commentId = intval($_POST["comment_id"] ?? 0);

if (!$commentId) sendJSON(400, "comment_id je obavezan");


$q = $conn->prepare("
    SELECT c.user_id AS author_id, p.user_id AS product_owner_id
    FROM comments c
    JOIN products p ON c.product_id = p.id
    WHERE c.id = ?
    LIMIT 1
");
$q->bind_param("i", $commentId);
$q->execute();
$row = $q->get_result()->fetch_assoc();

if (!$row) sendJSON(404, "Komentar nije pronađen");

$canDelete = $userId === intval($row["author_id"])
          || $userId === intval($row["product_owner_id"])
          || $isAdmin;

if (!$canDelete) sendJSON(403, "Pristup odbijen");

$del = $conn->prepare("DELETE FROM comments WHERE id = ?");
$del->bind_param("i", $commentId);
if (!$del->execute()) sendJSON(500, "Brisanje komentara nije uspelo");

sendJSON(200, "Komentar obrisan");
