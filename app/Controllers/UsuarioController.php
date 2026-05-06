<?php
namespace App\Controllers;

use App\Config\Database;
use App\Helpers\Acl;
use PDO;
use Exception;

class UsuarioController {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        LoginController::checkAuth();
    }

    public function index() {
        Acl::check('manage_users');
        
        $sql = "SELECT u.id, u.nome, u.usuario, u.email, u.nivel, 
                (SELECT GROUP_CONCAT(p.nome SEPARATOR ', ') 
                 FROM papeis p 
                 JOIN usuario_papel up ON p.id = up.papel_id 
                 WHERE up.usuario_id = u.id) as papeis_nomes
                FROM usuarios u";
        $stmt = $this->db->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $papeis = $this->db->query("SELECT * FROM papeis")->fetchAll(PDO::FETCH_ASSOC);

        require_once dirname(__DIR__) . '/Views/usuarios/index.php';
    }

    public function papeis() {
        Acl::check('manage_roles');
        
        $papeis = $this->db->query("SELECT * FROM papeis")->fetchAll(PDO::FETCH_ASSOC);
        $permissoes = $this->db->query("SELECT * FROM permissoes")->fetchAll(PDO::FETCH_ASSOC);
        
        // Carrega permissões de cada papel
        foreach ($papeis as &$papel) {
            $sql = "SELECT permissao_id FROM papel_permissao WHERE papel_id = :papel_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':papel_id' => $papel['id']]);
            $papel['permissoes'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        unset($papel);

        require_once dirname(__DIR__) . '/Views/usuarios/papeis.php';
    }

    public function salvarUsuarioPapeis() {
        Acl::check('manage_users');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $usuario_id = $dados['usuario_id'];
            $papeis_ids = $dados['papeis'] ?? [];

            $this->db->beginTransaction();

            // Remove papéis atuais
            $stmt = $this->db->prepare("DELETE FROM usuario_papel WHERE usuario_id = :usuario_id");
            $stmt->execute([':usuario_id' => $usuario_id]);

            // Adiciona novos papéis
            $stmt = $this->db->prepare("INSERT INTO usuario_papel (usuario_id, papel_id) VALUES (:usuario_id, :papel_id)");
            foreach ($papeis_ids as $papel_id) {
                $stmt->execute([':usuario_id' => $usuario_id, ':papel_id' => $papel_id]);
            }

            $this->db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Permissões atualizadas com sucesso!']);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function salvarPapelPermissoes() {
        Acl::check('manage_roles');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $papel_id = $dados['papel_id'];
            $permissoes_ids = $dados['permissoes'] ?? [];

            $this->db->beginTransaction();

            // Remove permissões atuais
            $stmt = $this->db->prepare("DELETE FROM papel_permissao WHERE papel_id = :papel_id");
            $stmt->execute([':papel_id' => $papel_id]);

            // Adiciona novas permissões
            $stmt = $this->db->prepare("INSERT INTO papel_permissao (papel_id, permissao_id) VALUES (:papel_id, :permissao_id)");
            foreach ($permissoes_ids as $perm_id) {
                $stmt->execute([':papel_id' => $papel_id, ':permissao_id' => $perm_id]);
            }

            $this->db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Permissões do papel atualizadas com sucesso!']);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function criarPapel() {
        Acl::check('manage_roles');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $nome = trim($dados['nome'] ?? '');
            $descricao = $dados['descricao'] ?? '';

            if (empty($nome)) throw new Exception("O nome do papel é obrigatório.");

            // Verifica se já existe
            $stmt = $this->db->prepare("SELECT id FROM papeis WHERE nome = :nome");
            $stmt->execute([':nome' => $nome]);
            if ($stmt->fetch()) {
                throw new Exception("Já existe um papel com o nome '$nome'.");
            }

            $stmt = $this->db->prepare("INSERT INTO papeis (nome, descricao) VALUES (:nome, :descricao)");
            $stmt->execute([':nome' => $nome, ':descricao' => $descricao]);

            echo json_encode(['status' => 'success', 'message' => "Papel '$nome' criado com sucesso!"]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function atualizarPapel() {
        Acl::check('manage_roles');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $id = $dados['id'] ?? null;
            $nome = trim($dados['nome'] ?? '');
            $descricao = $dados['descricao'] ?? '';

            if (!$id) throw new Exception("ID do papel não fornecido.");
            if (empty($nome)) throw new Exception("O nome do papel é obrigatório.");

            // Verifica se o novo nome já existe em outro ID
            $stmt = $this->db->prepare("SELECT id FROM papeis WHERE nome = :nome AND id != :id");
            $stmt->execute([':nome' => $nome, ':id' => $id]);
            if ($stmt->fetch()) {
                throw new Exception("Já existe outro papel com o nome '$nome'.");
            }

            $stmt = $this->db->prepare("UPDATE papeis SET nome = :nome, descricao = :descricao WHERE id = :id");
            $stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':id' => $id]);

            echo json_encode(['status' => 'success', 'message' => 'Papel atualizado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function excluirPapel() {
        Acl::check('manage_roles');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $id = $dados['id'] ?? null;

            if (!$id) throw new Exception("ID do papel não fornecido.");

            // Não permite excluir papéis básicos se necessário, ou apenas deleta
            // Primeiro remove as associações para evitar erro de FK
            $this->db->beginTransaction();
            
            $this->db->prepare("DELETE FROM papel_permissao WHERE papel_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM usuario_papel WHERE papel_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM papeis WHERE id = :id")->execute([':id' => $id]);
            
            $this->db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Papel excluído com sucesso!']);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function criarUsuario() {
        Acl::check('manage_users');
        header('Content-Type: application/json');
        
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $nome = trim($dados['nome'] ?? '');
            $usuario = trim($dados['usuario'] ?? '');
            $email = trim($dados['email'] ?? '');
            $senha = $dados['senha'] ?? '';
            $nivel = $dados['nivel'] ?? 'secretario';

            if (empty($nome) || empty($usuario) || empty($email) || empty($senha)) {
                throw new Exception("Preencha todos os campos obrigatórios.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("E-mail inválido.");
            }

            // Verifica se e-mail ou usuário já existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email OR usuario = :usuario");
            $stmt->execute([':email' => $email, ':usuario' => $usuario]);
            if ($stmt->fetch()) {
                throw new Exception("Este e-mail ou usuário já está cadastrado.");
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, usuario, email, senha, nivel, forçar_mudança_senha) 
                    VALUES (:nome, :usuario, :email, :senha, :nivel, 1)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome'    => $nome,
                ':usuario' => $usuario,
                ':email'   => $email,
                ':senha'   => $senhaHash,
                ':nivel'   => $nivel
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Usuário criado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getUsuario() {
        Acl::check('manage_users');
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new Exception("ID não fornecido.");

            $stmt = $this->db->prepare("SELECT id, nome, usuario, email, nivel FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) throw new Exception("Usuário não encontrado.");
            echo json_encode(['status' => 'success', 'data' => $user]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function atualizarUsuario() {
        Acl::check('manage_users');
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $id = $dados['id'] ?? null;
            $nome = trim($dados['nome'] ?? '');
            $usuario = trim($dados['usuario'] ?? '');
            $email = trim($dados['email'] ?? '');
            $nivel = $dados['nivel'] ?? '';
            $senha = $dados['senha'] ?? '';

            if (!$id || empty($nome) || empty($usuario) || empty($email) || empty($nivel)) {
                throw new Exception("Preencha todos os campos obrigatórios.");
            }

            // Verifica se e-mail ou usuário já existe em outro ID
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE (email = :email OR usuario = :usuario) AND id != :id");
            $stmt->execute([':email' => $email, ':usuario' => $usuario, ':id' => $id]);
            if ($stmt->fetch()) {
                throw new Exception("Este e-mail ou usuário já está em uso por outro usuário.");
            }

            $sql = "UPDATE usuarios SET nome = :nome, usuario = :usuario, email = :email, nivel = :nivel";
            $params = [
                ':nome'    => $nome,
                ':usuario' => $usuario,
                ':email'   => $email,
                ':nivel'   => $nivel,
                ':id'      => $id
            ];

            if (!empty($senha)) {
                $sql .= ", senha = :senha, forçar_mudança_senha = 1";
                $params[':senha'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            echo json_encode(['status' => 'success', 'message' => 'Usuário atualizado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function excluirUsuario() {
        Acl::check('manage_users');
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $id = $dados['id'] ?? null;

            if (!$id) throw new Exception("ID não fornecido.");
            if ($id == $_SESSION['usuario_id']) throw new Exception("Você não pode excluir a si mesmo.");

            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM usuario_papel WHERE usuario_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM auth_2fa_pendente WHERE usuario_id = :id")->execute([':id' => $id]);
            $this->db->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);
            $this->db->commit();

            echo json_encode(['status' => 'success', 'message' => 'Usuário excluído com sucesso!']);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
