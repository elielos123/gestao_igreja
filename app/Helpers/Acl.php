<?php
namespace App\Helpers;

use App\Config\Database;
use PDO;

class Acl {
    private static $db;
    private static $permissions = null;

    private static function init() {
        if (self::$db === null) {
            self::$db = (new Database())->getConnection();
        }
    }

    /**
     * Carrega as permissões do usuário logado para a sessão
     */
    public static function loadUserPermissions($usuario_id) {
        self::init();
        
        $sql = "SELECT DISTINCT p.nome 
                FROM permissoes p
                JOIN papel_permissao pp ON p.id = pp.permissao_id
                JOIN usuario_papel up ON pp.papel_id = up.papel_id
                WHERE up.usuario_id = :usuario_id";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        $perms = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['usuario_permissoes'] = $perms;
        self::$permissions = $perms;
    }

    /**
     * Verifica se o usuário tem uma determinada permissão
     */
    public static function hasPermission($permissao) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Se for admin (nível 1), as vezes pode ter bypass, mas vamos seguir ACL pura
        // se preferir bypass por nível:
        /*
        if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] == 1) {
            return true;
        }
        */

        $perms = $_SESSION['usuario_permissoes'] ?? [];
        
        return in_array($permissao, $perms);
    }

    /**
     * Verifica se o usuário tem qualquer uma das permissões informadas
     */
    public static function hasAnyPermission(array $permissoes) {
        foreach ($permissoes as $p) {
            if (self::hasPermission($p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Aborta a execução se o usuário não tiver a permissão
     */
    public static function check($permissao) {
        if (!self::hasPermission($permissao)) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Acesso negado. Você não tem permissão para realizar esta ação.']);
            exit;
        }
    }

    /**
     * Verifica permissão para renderização de elementos na View
     */
    public static function canView($permissao) {
        return self::hasPermission($permissao);
    }
}
