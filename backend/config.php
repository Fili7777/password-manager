<?php
// Configurazione database
$db_host = "localhost";
$db_name = "password_manager";
$db_user = "root"; // cambia se necessario
$db_pass = "";     // cambia se necessario

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Errore DB: ' . $e->getMessage()]);
    exit;
}

// Chiave segreta per JWT (usa una chiave lunga e sicura in produzione)
define('JWT_SECRET', 'questaÃ¨_una_chiave_segretissima_per_il_tuo_progetto');
?>