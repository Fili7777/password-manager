<?php
header('Content-Type: application/json');
require 'config.php';

$input = json_decode(file_get_contents("php://input"), true);

$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Email e password richieste']);
    exit;
}

try {
    // Cerca utente
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['message' => 'Email o password non corretti']);
        exit;
    }

    // Funzioni base64url
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Crea JWT semplice (header.payload.signature) con base64url
    $header = base64url_encode(json_encode(['alg'=>'HS256','typ'=>'JWT']));
    $payload = base64url_encode(json_encode([
        'user_id' => $user['id'],
        'exp' => time() + 3600 // scadenza 1h
    ]));

    $signature = base64url_encode(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    $jwt = "$header.$payload.$signature";

    echo json_encode(['token' => $jwt]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Errore DB: ' . $e->getMessage()]);
}
?>