<?php

// CORS
$allowedOrigins = ['http://localhost:4200'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: *");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function sendJSON(int $status, $data): void
{
    http_response_code($status);
    header("Content-Type: application/json; charset=utf-8");
    $payload = is_array($data) ? $data : ['message' => $data];
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function requireMethod(string $method): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $method) {
        sendJSON(405, "Method not allowed");
    }
}

function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}


define('TOKEN_SECRET', 'secondhand2025xK9mP');

function createToken(int $userId): string
{
    $payload = base64_encode(json_encode(['id' => $userId, 'ts' => time()]));
    $sig     = hash_hmac('sha256', $payload, TOKEN_SECRET);
    return "$payload.$sig";
}

function resolveAuthUser($conn): array
{
    $authHeader = '';

   
    if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } elseif (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                $authHeader = $value;
                break;
            }
        }
    }

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        sendJSON(401, "Unauthorized - Missing or malformed header");
    }

    $token = substr($authHeader, 7);
    $parts = explode('.', $token);
    
    if (count($parts) !== 2) {
        sendJSON(401, "Invalid token format");
    }

    [$payload, $sig] = $parts;

    
    if (hash_hmac('sha256', $payload, TOKEN_SECRET) !== $sig) {
        sendJSON(401, "Invalid token signature");
    }

    $decodedPayload = json_decode(base64_decode($payload), true);
    $uid = intval($decodedPayload['id'] ?? 0);

    if (!$uid) {
        sendJSON(401, "Bad token payload");
    }

    
    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        sendJSON(401, "User not found");
    }

    return $user;
}

function requireAdmin($conn): array
{
    $user = resolveAuthUser($conn);
    if (($user['role'] ?? '') !== 'admin') {
        sendJSON(403, "Admin only");
    }
    return $user;
}