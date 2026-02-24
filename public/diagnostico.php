<?php
/**
 * Script de Diagnóstico e Reparo Online
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnóstico do Sistema</h1>";

// 1. Verificar PHP
echo "Versão do PHP: " . PHP_VERSION . "<br>";

// 2. Verificar Vendor
echo "Verificando pasta vendor... ";
if (is_dir(__DIR__ . '/../vendor')) {
    echo "<span style='color:green'>OK (Existe)</span><br>";
} else {
    echo "<span style='color:red'>ERRO (Não encontrada!)</span> - Você precisa rodar 'composer install'<br>";
}

// 3. Verificar .env
echo "Verificando arquivo .env... ";
if (file_exists(__DIR__ . '/../.env')) {
    echo "<span style='color:green'>OK (Existe)</span><br>";
} else {
    echo "<span style='color:orange'>AVISO (.env não encontrado)</span> - Copie o .env.example para .env<br>";
}

// 4. Testar Conexão com Banco
echo "Testando conexão com banco de dados...<br>";
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
try {
    $dotenv->load();
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $db   = $_ENV['DB_NAME'] ?? '';
    $user = $_ENV['DB_USER'] ?? '';
    $pass = $_ENV['DB_PASS'] ?? '';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "<span style='color:green'>Conexão com Banco de Dados OK!</span><br>";
} catch (Exception $e) {
    echo "<span style='color:red'>Erro ao carregar .env ou conectar ao banco: " . $e->getMessage() . "</span><br>";
}

// 5. Verificar Servidor (Nginx vs Apache)
echo "Servidor Web: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
if (strpos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false) {
    echo "<span style='color:blue'>Aviso: Servidor é Nginx. O arquivo .htaccess será ignorado. Você deve configurar as regras de rewrite no nginx.conf.</span><br>";
}
?>
