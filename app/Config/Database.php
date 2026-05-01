<?php
/**
 * Classe de Conexão com Banco de Dados
 * Padrão: Singleton (Garante apenas uma instância de conexão por requisição)
 * Local: app/Config/Database.php
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para Database.php
 * 2. Altere as configurações abaixo com suas credenciais
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    
    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: "localhost";
        $this->db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: "gestao_igreja";
        $this->username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: "root";
        $this->password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: "";
    }
    
    // Variável estática para segurar a conexão
    public $conn;

    // Método para pegar a conexão
    public function getConnection() {
        $this->conn = null;

        try {
            // String de conexão (DSN)
            // Configurado para UTF-8 para aceitar acentos e caracteres especiais
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Configurações de Erro e Segurança
            // ERRMODE_EXCEPTION: Faz o PHP parar e mostrar o erro se o SQL falhar (bom para dev)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // DEFAULT_FETCH_MODE: Garante que os dados venham como array associativo por padrão
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $exception) {
            // Em produção, nunca mostre o erro exato para o usuário (segurança)
            // Mas em desenvolvimento, precisamos ver o que houve:
            echo "<div style='color:white; background:red; padding:10px; text-align:center;'>";
            echo "<strong>Erro Crítico de Conexão:</strong> " . $exception->getMessage();
            echo "</div>";
            exit; // Para a execução do script
        }

        return $this->conn;
    }
}
