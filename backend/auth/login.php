<?php
require_once '../config/db.php';
require_once '../config/utils.php';


$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

if (!$email || !$password) {
    sendJSON(400, "Email i lozinka su obavezni.");
}

$email = mysqli_real_escape_string($conn, $email);


$sql = "SELECT id, NAME, email, PASSWORD, role FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    
    if (password_verify($password, $row['PASSWORD'])) {
        
        
        $token = createToken((int)$row['id']); 

        
        sendJSON(200, [
            "token" => $token,
            "user" => [
                "id" => $row['id'],
                "username" => $row['NAME'],
                "email" => $row['email'],
                "role" => $row['role']
            ]
        ]);
    } else {
        sendJSON(401, "Pogrešna lozinka.");
    }
} else {
    sendJSON(401, "Korisnik nije pronađen.");
}
?>