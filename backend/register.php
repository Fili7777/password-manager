<?php
header('Content-Type: application/json');
require 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = trim($input['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['message' => 'Compila tutti i campi']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Email non valida']);
    exit;
}

try {
    // Controlla se email esiste
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['message' => 'Email giÃ registrata']);
        exit;
    }

    // Hash della password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Inserisci utente
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $password_hash]);

    echo json_encode(['message' => 'Registrazione completata!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Errore DB: ' . $e->getMessage()]);
}
?>