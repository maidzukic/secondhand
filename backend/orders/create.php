<?php
require_once "../config/db.php";
require_once "../config/utils.php";
require_once '../auth/check_auth.php';

requireMethod("POST");

$authUser  = resolveAuthUser($conn);
$buyerId   = intval($authUser["id"]);
$productId = intval($_POST["product_id"] ?? 0);

if (!$productId) sendJSON(400, "product_id je obavezan");

$stmt = $conn->prepare("SELECT user_id AS seller_id FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $productId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) sendJSON(404, "Proizvod nije pronađen");

$sellerId = intval($row["seller_id"]);

if ($sellerId === $buyerId) sendJSON(400, "Ne možete kupiti svoj proizvod");

$dup = $conn->prepare("SELECT id FROM orders WHERE product_id=? AND buyer_id=? AND status='pending' LIMIT 1");
$dup->bind_param("ii", $productId, $buyerId);
$dup->execute();
if ($dup->get_result()->num_rows > 0) sendJSON(409, "Već imate pending zahtev za ovaj proizvod");

$ins = $conn->prepare("INSERT INTO orders (product_id, buyer_id, seller_id, status) VALUES (?,?,?,'pending')");
$ins->bind_param("iii", $productId, $buyerId, $sellerId);
if (!$ins->execute()) sendJSON(500, "Greška prilikom kreiranja narudžbe");

sendJSON(201, ["order_id" => $conn->insert_id, "status" => "pending"]);
