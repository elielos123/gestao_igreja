<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;

    public function __construct() {
        $this->host     = $_ENV['DB_HOST'];
        $this->db_name   = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        $this->charset  = $_ENV['DB_CHARSET'];
    }
    
    // Variável estática para segurar a conexão
    public $conn;

    // Método para pegar a conexão
    public function getConnection() {
        $this->conn = null;

        try {
            // String de conexão (DSN)
            // Configurado para UTF-8 para aceitar acentos e caracteres especiais
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Força o charset para evitar caracteres estranhos
            $this->conn->exec("SET NAMES " . $this->charset);
            
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