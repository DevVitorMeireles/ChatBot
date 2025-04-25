<?php
phpinfo(); 
ob_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!isset($data["mensagem"])) {
    http_response_code(400);
    echo json_encode(["resposta" => "Erro: mensagem não recebida."]);
    exit;
}

$mensagem = $data["mensagem"];

// Prepara a requisição pra API do Gemini
$apiKey = "AIzaSyAlLrNyI7_XKcXIElTrZs_UHxl1ooDx6QU"; 

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey";

$body = json_encode([
    "contents" => [[
        "parts" => [[ "text" => $mensagem ]]
    ]]
]);

$options = [
    "http" => [
        "method"  => "POST",
        "header"  => "Content-Type: application/json",
        "content" => $body
    ]
];

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === false) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(["resposta" => "Erro ao acessar a API do Gemini"]);
    exit;
}

$responseData = json_decode($result, true);
$resposta = $responseData["candidates"][0]["content"]["parts"][0]["text"] ?? "Erro ao interpretar resposta da IA";

ob_end_clean();
echo json_encode(["resposta" => $resposta]); 
