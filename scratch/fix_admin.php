<?php
require_once 'vendor/autoload.php';
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = (new Database())->getConnection();
    
    // 1. Garantir que o papel Pastor existe
    $role = $db->query("SELECT id FROM papeis WHERE nome = 'Pastor'")->fetch(PDO::FETCH_ASSOC);
    if (!$role) {
        $db->exec("INSERT INTO papeis (nome, descricao) VALUES ('Pastor', 'Acesso total ao sistema')");
        $roleId = $db->lastInsertId();
    } else {
        $roleId = $role['id'];
    }

    // 2. Dar todas as permissões para o papel Pastor
    $perms = $db->query("SELECT id FROM permissoes")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($perms as $p) {
        $db->exec("INSERT IGNORE INTO papel_permissao (papel_id, permissao_id) VALUES ($roleId, {$p['id']})");
    }

    // 3. Vincular o usuário admin ao papel Pastor
    $admin = $db->query("SELECT id FROM usuarios WHERE usuario = 'admin'")->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $db->exec("INSERT IGNORE INTO usuario_papel (usuario_id, papel_id) VALUES ({$admin['id']}, $roleId)");
        echo "Sucesso: Admin vinculado ao papel Pastor com todas as permissões.";
    } else {
        echo "Erro: Usuário admin não encontrado.";
    }

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
