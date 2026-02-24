<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Config\Database;

// Forçar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste de Conexão com Banco de Dados</h2>";

try {
    // Carregar .env
    if (file_exists(dirname(__DIR__) . '/.env')) {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();
        echo "✅ Arquivo .env carregado.<br>";
    } else {
        echo "❌ Arquivo .env não encontrado!<br>";
    }

    $db = new Database();
    $conn = $db->getConnection();

    if ($conn) {
        echo "✅ Conexão estabelecida com sucesso!<br>";
        
        $stmt = $conn->query("SELECT VERSION() as version");
        $row = $stmt->fetch();
        echo "Versão do MySQL: " . $row['version'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "<br>";
}
?>
