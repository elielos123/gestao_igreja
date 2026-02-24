<?php
namespace App\Controllers;

use App\Config\Database;
use App\Helpers\TotpHelper;
use App\Helpers\SenhaHelper;
use Exception;
use PDO;

class LoginController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    // ─── VIEWS ─────────────────────────────────────────────────────────────────

    public function index()
    {
        require_once dirname(__DIR__) . '/Views/login.php';
    }

    public function setup2faView()
    {
        static::checkAuth();
        require_once dirname(__DIR__) . '/Views/auth/setup_2fa.php';
    }

    // ─── AUTH ──────────────────────────────────────────────────────────────────

    /**
     * Step 1: verify email + password + reCAPTCHA.
     * If 2FA is active, returns {status:'2fa_required', temp_token}.
     * Otherwise creates full session immediately.
     */
    public function autenticar()
    {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $email = trim($dados['email'] ?? '');
            $senha = $dados['senha'] ?? '';
            $recaptchaToken = $dados['recaptcha_token'] ?? '';

            if (empty($email) || empty($senha)) {
                throw new Exception('Preencha todos os campos.');
            }

            // ── reCAPTCHA v3 ──
            $this->verificarRecaptcha($recaptchaToken);

            // ── Buscar usuário ──
            $stmt = $this->db->prepare(
                'SELECT id, nome, senha, nivel, totp_ativo, totp_secret, forçar_mudança_senha FROM usuarios WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || !password_verify($senha, $usuario['senha'])) {
                throw new Exception('E-mail ou senha incorretos.');
            }

            // ── Forçar mudança de senha? ──
            if ($usuario['forçar_mudança_senha']) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['temp_usuario_id'] = $usuario['id'];
                echo json_encode(['status' => 'password_change_required']);
                return;
            }

            // ── 2FA activo? ──
            if ($usuario['totp_ativo'] && $usuario['totp_secret']) {
                // Criar token temporário de curta duração
                $tempToken = bin2hex(random_bytes(32));
                // Limpar pendentes antigos (> 5 min) e inserir novo
                $this->db->exec("DELETE FROM auth_2fa_pendente WHERE criado_em < NOW() - INTERVAL 5 MINUTE");
                $ins = $this->db->prepare(
                    'INSERT INTO auth_2fa_pendente (token, usuario_id) VALUES (:token, :uid)'
                );
                $ins->execute([':token' => $tempToken, ':uid' => $usuario['id']]);

                echo json_encode(['status' => '2fa_required', 'temp_token' => $tempToken]);
                return;
            }

            // ── Sem 2FA: sessão imediata ──
            $this->criarSessao($usuario);
            echo json_encode(['status' => 'success', 'message' => 'Login realizado com sucesso!']);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Step 2: validate TOTP code with temp_token then create session.
     */
    public function verificar2fa()
    {
        header('Content-Type: application/json');
        try {
            $dados     = json_decode(file_get_contents('php://input'), true);
            $tempToken = $dados['temp_token'] ?? '';
            $codigo    = preg_replace('/\D/', '', $dados['codigo'] ?? '');

            if (empty($tempToken) || strlen($codigo) !== 6) {
                throw new Exception('Dados inválidos.');
            }

            // Buscar pendente (max 5 min)
            $stmt = $this->db->prepare(
                'SELECT p.usuario_id, u.id, u.nome, u.nivel, u.totp_secret
                 FROM auth_2fa_pendente p
                 JOIN usuarios u ON u.id = p.usuario_id
                 WHERE p.token = :token AND p.criado_em >= NOW() - INTERVAL 5 MINUTE
                 LIMIT 1'
            );
            $stmt->execute([':token' => $tempToken]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                throw new Exception('Token expirado. Faça login novamente.');
            }

            if (!TotpHelper::verify($row['totp_secret'], $codigo)) {
                throw new Exception('Código inválido. Verifique o Google Authenticator.');
            }

            // Limpar token temporário
            $this->db->prepare('DELETE FROM auth_2fa_pendente WHERE token = :token')
                     ->execute([':token' => $tempToken]);

            $this->criarSessao($row);
            echo json_encode(['status' => 'success', 'message' => 'Login realizado com sucesso!']);

        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ─── 2FA SETUP ─────────────────────────────────────────────────────────────

    /** Returns a QR URI + secret for the setup page. */
    public function get2faSetup()
    {
        header('Content-Type: application/json');
        static::checkAuth();
        try {
            $uid = $_SESSION['usuario_id'];
            $stmt = $this->db->prepare('SELECT email, totp_ativo FROM usuarios WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $uid]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $secret = TotpHelper::generateSecret();
            // Store temporarily in totp_temp until user confirms
            $this->db->prepare('UPDATE usuarios SET totp_temp = :s WHERE id = :id')
                     ->execute([':s' => $secret, ':id' => $uid]);

            $qrUri = TotpHelper::getQRCodeUri($user['email'], $secret);
            echo json_encode([
                'status'    => 'success',
                'secret'    => $secret,
                'qr_uri'    => $qrUri,
                'totp_ativo'=> (bool)$user['totp_ativo'],
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /** User confirms setup by entering a valid code → mark totp_ativo = 1. */
    public function confirmar2fa()
    {
        header('Content-Type: application/json');
        static::checkAuth();
        try {
            $dados  = json_decode(file_get_contents('php://input'), true);
            $codigo = preg_replace('/\D/', '', $dados['codigo'] ?? '');
            $uid    = $_SESSION['usuario_id'];

            $stmt = $this->db->prepare('SELECT totp_temp FROM usuarios WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $uid]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user['totp_temp']) {
                throw new Exception('Nenhum segredo temporário. Reinicie o processo.');
            }
            if (!TotpHelper::verify($user['totp_temp'], $codigo)) {
                throw new Exception('Código inválido. Tente novamente.');
            }

            $this->db->prepare(
                'UPDATE usuarios SET totp_secret = :s, totp_ativo = 1, totp_temp = NULL WHERE id = :id'
            )->execute([':s' => $user['totp_temp'], ':id' => $uid]);

            echo json_encode(['status' => 'success', 'message' => '2FA ativado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /** Disable 2FA for the current user. */
    public function desativar2fa()
    {
        header('Content-Type: application/json');
        static::checkAuth();
        try {
            $uid = $_SESSION['usuario_id'];
            $this->db->prepare(
                'UPDATE usuarios SET totp_secret = NULL, totp_ativo = 0, totp_temp = NULL WHERE id = :id'
            )->execute([':id' => $uid]);
            echo json_encode(['status' => 'success', 'message' => '2FA desativado.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ─── SENHA FORTE ───────────────────────────────────────────────────────────

    /** API endpoint — client-side pre-validation. */
    public function validarSenha()
    {
        header('Content-Type: application/json');
        $dados  = json_decode(file_get_contents('php://input'), true);
        $senha  = $dados['senha'] ?? '';
        $result = SenhaHelper::validar($senha);
        echo json_encode([
            'valid'      => $result['valid'],
            'erro'       => $result['erro'],
            'pontuacao'  => SenhaHelper::pontuacao($senha),
        ]);
    }

    // ─── LOGOUT ────────────────────────────────────────────────────────────────

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header('Location: index.php?url=login');
        exit;
    }

    public function viewAlterarSenha()
    {
        require_once dirname(__DIR__) . '/Views/auth/alterar_senha.php';
    }

    public function alterarSenhaPrimeiroAcesso()
    {
        header('Content-Type: application/json');
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $uid = $_SESSION['temp_usuario_id'] ?? null;
            if (!$uid) throw new Exception("Sessão expirada.");

            $dados = json_decode(file_get_contents('php://input'), true);
            $novaSenha = $dados['senha'] ?? '';

            if (empty($novaSenha)) throw new Exception("A senha não pode ser vazia.");
            
            $resSenha = SenhaHelper::validar($novaSenha);
            if (!$resSenha['valid']) throw new Exception($resSenha['erro']);

            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE usuarios SET senha = :s, forçar_mudança_senha = 0 WHERE id = :id");
            $stmt->execute([':s' => $senhaHash, ':id' => $uid]);

            // Login automático após alterar
            $stmt = $this->db->prepare("SELECT id, nome, nivel FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $uid]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            unset($_SESSION['temp_usuario_id']);
            $this->criarSessao($usuario);

            echo json_encode(['status' => 'success', 'message' => 'Senha alterada com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ─── HELPERS ───────────────────────────────────────────────────────────────

    private function criarSessao(array $usuario)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['usuario_id']    = $usuario['id'];
        $_SESSION['usuario_nome']  = $usuario['nome'];
        $_SESSION['usuario_nivel'] = $usuario['nivel'];
        \App\Helpers\Acl::loadUserPermissions($usuario['id']);
    }

    /**
     * Validate a reCAPTCHA v3 token.
     * If no keys are configured, skip silently (dev mode).
     */
    private function verificarRecaptcha(string $token): void
    {
        $secret = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
        if (empty($secret) || $secret === 'COLOQUE_SUA_SECRET_KEY_AQUI') {
            return; // Dev mode — skip validation
        }
        if (empty($token)) {
            throw new Exception('Verificação de segurança falhou. Recarregue a página.');
        }
        $resp = @file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?secret=' .
            urlencode($secret) . '&response=' . urlencode($token)
        );
        if (!$resp) {
            throw new Exception('Falha ao verificar reCAPTCHA. Tente novamente.');
        }
        $data = json_decode($resp, true);
        if (empty($data['success']) || ($data['score'] ?? 0) < 0.5) {
            throw new Exception('Verificação de segurança falhou. Tente novamente.');
        }
    }

    // ─── STATIC CHECK ──────────────────────────────────────────────────────────

    public static function checkAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: index.php?url=login');
            exit;
        }
        if (!isset($_SESSION['usuario_permissoes'])) {
            \App\Helpers\Acl::loadUserPermissions($_SESSION['usuario_id']);
        }
    }
}
