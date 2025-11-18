<?php
header('Content-Type: application/json');

// Questo serve solo per testare se PHP funziona e restituisce JSON
echo json_encode([
    "status" => "ok",
    "message" => "PHP funziona correttamente!"
]);