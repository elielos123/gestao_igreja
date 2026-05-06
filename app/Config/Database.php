<?php
/**
 * Classe de Conexão com Banco de Dados (TiDB Cloud + Local)
 * Local: app/Config/Database.php
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    
    public function __construct() {
        // Tenta buscar das variáveis de ambiente (Vercel ou .env)
        // Se não encontrar, usa os padrões do Laragon
        $this->host = getenv('DB_HOST') ?: "localhost";
        $this->db_name = getenv('DB_NAME') ?: "gestao_igreja";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASS') ?: "";
        $this->port = getenv('DB_PORT') ?: "3306";
    }
    
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // String de conexão (DSN) - Incluindo a porta
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";port=" . $this->port . ";charset=utf8";
            
            // Opções padrão de segurança e performance
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            // SE não for localhost, assume que é TiDB/Nuvem e ativa o SSL
            if ($this->host !== "localhost" && $this->host !== "127.0.0.1") {
                // TiDB exige conexão segura (SSL)
                $options[PDO::MYSQL_ATTR_SSL_CA] = true;
                // Alguns ambientes serverless precisam desativar a verificação rigorosa do certificado
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);

        } catch(PDOException $exception) {
            // EXIBIÇÃO TEMPORÁRIA DE ERRO PARA DEBUG NA VERCEL
            echo "<div style='color:white; background:red; padding:10px; text-align:center;'>";
            echo "<strong>Erro de Conexão DB:</strong> " . $exception->getMessage();
            echo "</div>";
            error_log("Erro de Conexão DB: " . $exception->getMessage());
            exit;
        }

        return $this->conn;
    }
}
