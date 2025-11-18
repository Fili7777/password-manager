<?php
require_once 'config.php';

function generateJWT($userId) {
    global $secret_key;
    $header = json_encode(['typ'=>'JWT','alg'=>'HS256']);
    $payload = json_encode([
        'user_id'=>$userId,
        'exp'=>time() + 3600 // token valido 1 ora
    ]);

    $base64UrlHeader = str_replace(['+','/','='], ['-','_',''], base64_encode($header));
    $base64UrlPayload = str_replace(['+','/','='], ['-','_',''], base64_encode($payload));
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
    $base64UrlSignature = str_replace(['+','/','='], ['-','_',''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

function verifyJWT($jwt) {
    global $secret_key;
    $parts = explode('.', $jwt);
    if(count($parts) != 3) return false;

    $header = $parts[0];
    $payload = $parts[1];
    $signature = $parts[2];

    $expectedSig = hash_hmac('sha256', $header . "." . $payload, $secret_key, true);
    $expectedSig = str_replace(['+','/','='], ['-','_',''], base64_encode($expectedSig));

    if(!hash_equals($expectedSig, $signature)) return false;

    $payloadArray = json_decode(base64_decode($payload), true);
    if($payloadArray['exp'] < time()) return false;

    return $payloadArray;
}
?>
