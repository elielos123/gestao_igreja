<?php
/**
 * Script de Diagnóstico de Redirecionamento e Headers
 */
header('Content-Type: text/plain; charset=utf-8');

echo "--- DIAGNÓSTICO DE REDIRECIONAMENTO ---\n\n";

echo "PROTOCOLO: " . ($_SERVER['HTTPS'] ?? 'OFF') . "\n";
echo "PORTA: " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "\n";
echo "REQUEST URI: " . $_SERVER['REQUEST_URI'] . "\n\n";

echo "--- HEADERS RECEBIDOS ---\n\n";

$headers = getallheaders();
foreach ($headers as $nome => $valor) {
    echo "$nome: $valor\n";
}

echo "\n--- $_SERVER ---\n\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'NÃO DEFINIDO') . "\n";
echo "HTTP_CF_VISITOR: " . ($_SERVER['HTTP_CF_VISITOR'] ?? 'NÃO DEFINIDO') . "\n";
echo "HTTP_X_FORWARDED_SSL: " . ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? 'NÃO DEFINIDO') . "\n";

echo "\n--- POSSÍVEL CAUSA ---\n";
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        echo "Você está atrás de um proxy (Cloudflare?) que já está em HTTPS,\n";
        echo "mas o seu servidor PHP acha que está em HTTP.\n";
        echo "Se o seu código (ou .htaccess) tenta forçar HTTPS, o loop acontece aqui.";
    } else {
        echo "O servidor está operando em HTTP puro.";
    }
} else {
    echo "O servidor detectou HTTPS corretamente.";
}
