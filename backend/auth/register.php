<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';



$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : null;
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Sva polja (name, email, password) su obavezna."]);
    exit();
}


$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (NAME, email, PASSWORD, role) VALUES ('$name', '$email', '$hashed_password', 'user')";

if (mysqli_query($conn, $sql)) {
    http_response_code(201);
    echo json_encode(["message" => "Uspješna registracija!"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Greška u bazi: " . mysqli_error($conn)]);
}
?>