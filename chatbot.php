<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// Trata requisições OPTIONS (preflight do CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!isset($data["mensagem"])) {
    http_response_code(400);
    echo json_encode(["resposta" => "Erro: mensagem não recebida."]);
    exit;
}

$mensagem = $data["mensagem"];
$apiKey = "AIzaSyAlLrNyI7_XKcXIElTrZs_UHxl1ooDx6QU"; // <--- substitui pela sua chave da API do Gemini

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

$payload = json_encode([
    "contents" => [[
        "parts" => [[ "text" => $mensagem ]]
    ]]
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    http_response_code(500);
    echo json_encode(["resposta" => "Erro ao acessar a API do Gemini: $curlError"]);
    exit;
}

$responseData = json_decode($response, true);
$resposta = $responseData["candidates"][0]["content"]["parts"][0]["text"] ?? "Erro ao interpretar resposta da IA";

echo json_encode(["resposta" => $resposta]);
