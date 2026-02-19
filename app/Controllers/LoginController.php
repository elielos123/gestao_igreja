<?php
namespace App\Controllers;

use App\Config\Database;
use Exception;
use PDO;

class LoginController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function index() {
        require_once dirname(__DIR__) . '/Views/login.php';
    }

    public function autenticar() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $email = $dados['email'] ?? '';
            $senha = $dados['senha'] ?? '';

            if (empty($email) || empty($senha)) {
                throw new Exception("Preencha todos os campos.");
            }

            $sql = "SELECT id, nome, senha, nivel FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_nivel'] = $usuario['nivel'];

                // Carrega permissões via ACL
                \App\Helpers\Acl::loadUserPermissions($usuario['id']);

                echo json_encode(['status' => 'success', 'message' => 'Login realizado com sucesso!']);
            } else {
                throw new Exception("E-mail ou senha incorretos.");
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public static function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?url=login");
            exit;
        }

        // Se estiver logado mas sem as permissões carregadas (sessão antiga), carrega agora
        if (!isset($_SESSION['usuario_permissoes'])) {
            \App\Helpers\Acl::loadUserPermissions($_SESSION['usuario_id']);
        }
    }
}
