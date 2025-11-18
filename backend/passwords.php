passwords.php:
<?php
header('Content-Type: application/json');
require 'config.php';

// base64url helper
function base64url_decode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) $data .= str_repeat('=', 4 - $remainder);
    return base64_decode(strtr($data, '-_', '+/'));
}
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// Legge token Bearer
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
if (!$authHeader || stripos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['message' => 'Token mancante']);
    exit;
}
$token = substr($authHeader, 7); // rimuove "Bearer "

$parts = explode('.', $token);
if (count($parts) !== 3) {
    http_response_code(401);
    echo json_encode(['message' => 'Token non valido']);
    exit;
}
list($headerB64, $payloadB64, $sigB64) = $parts;

// verifica signature
$expectedSig = base64url_encode(hash_hmac('sha256', "$headerB64.$payloadB64", JWT_SECRET, true));
if (!hash_equals($expectedSig, $sigB64)) {
    http_response_code(401);
    echo json_encode(['message' => 'Token non valido']);
    exit;
}

$payload = json_decode(base64url_decode($payloadB64), true);
$user_id = $payload['user_id'] ?? null;
$exp = $payload['exp'] ?? 0;

if (!$user_id || time() > $exp) {
    http_response_code(401);
    echo json_encode(['message' => 'Token scaduto o non valido']);
    exit;
}

// GET: ritorna password
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT id, site, username, password FROM passwords WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($passwords);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Errore DB: ' . $e->getMessage()]);
    }
    exit;
}

// POST: aggiunge password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $site = trim($data['site'] ?? '');
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($site === '' || $username === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['message' => 'Compila tutti i campi']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO passwords (user_id, site, username, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $site, $username, $password]);
        echo json_encode(['message' => 'Password salvata con successo']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Errore DB: ' . $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['message' => 'Metodo non permesso']);
?>