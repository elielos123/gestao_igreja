<?php
/**
 * Script de Instalação do Banco de Dados
 * Cria as tabelas e popula os dados iniciais.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/Database.php';

use App\Config\Database;

try {
    $db = (new Database())->getConnection();
    
    echo "<h1>Iniciando Instalação do Banco de Dados...</h1>";

    $sql = "
    CREATE TABLE IF NOT EXISTS `auth_2fa_pendente` (
      `id` int NOT NULL AUTO_INCREMENT,
      `token` varchar(255) NOT NULL,
      `usuario_id` int NOT NULL,
      `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `cargo` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `cargo_congregacional` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `congregacao` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `entradas` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      `data` date DEFAULT NULL,
      `valor` decimal(10,2) DEFAULT NULL,
      `congregacao` varchar(255) DEFAULT NULL,
      `tipo` varchar(100) DEFAULT NULL,
      `incongruencia_aceita` tinyint(1) DEFAULT '0',
      `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `funcao_eclesiastica` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `membros` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) NOT NULL,
      `data_nascimento` date DEFAULT NULL,
      `sexo` varchar(50) DEFAULT NULL,
      `cpf` varchar(50) DEFAULT NULL,
      `telefone` varchar(50) DEFAULT NULL,
      `email` varchar(255) DEFAULT NULL,
      `endereco` text,
      `data_batismo` date DEFAULT NULL,
      `funcao_eclesiastica` varchar(255) DEFAULT NULL,
      `cargo_congregacional` varchar(255) DEFAULT NULL,
      `cargo` varchar(255) DEFAULT NULL,
      `congregacao` varchar(255) DEFAULT NULL,
      `status` varchar(50) DEFAULT 'Ativo',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `membros_conflitos` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) DEFAULT NULL,
      `resolvido` tinyint(1) DEFAULT '0',
      `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `papeis` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) NOT NULL,
      `descricao` text,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `papel_permissao` (
      `papel_id` int NOT NULL,
      `permissao_id` int NOT NULL,
      PRIMARY KEY (`papel_id`,`permissao_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `permissoes` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) NOT NULL,
      `descricao` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `nome` (`nome`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `saidas` (
      `id` int NOT NULL AUTO_INCREMENT,
      `recebedor` varchar(255) DEFAULT NULL,
      `data` date DEFAULT NULL,
      `valor` decimal(10,2) DEFAULT NULL,
      `descricao` text,
      `dados_cadastrais` text,
      `tipo_saida` varchar(100) DEFAULT NULL,
      `parcela` varchar(50) DEFAULT NULL,
      `incongruencia_aceita` tinyint(1) DEFAULT '0',
      `data_criacao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `usuario_papel` (
      `usuario_id` int NOT NULL,
      `papel_id` int NOT NULL,
      PRIMARY KEY (`usuario_id`,`papel_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `usuarios` (
      `id` int NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) NOT NULL,
      `usuario` varchar(50) DEFAULT NULL,
      `email` varchar(255) NOT NULL,
      `senha` varchar(255) NOT NULL,
      `nivel` varchar(50) DEFAULT 'secretario',
      `totp_ativo` tinyint(1) DEFAULT '0',
      `totp_secret` varchar(255) DEFAULT NULL,
      `totp_temp` varchar(255) DEFAULT NULL,
      `forçar_mudança_senha` tinyint(1) DEFAULT '0',
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`),
      UNIQUE KEY `usuario` (`usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    $db->exec($sql);
    echo "<p>Tabelas criadas com sucesso.</p>";

    // --- POPULAR DADOS INICIAIS ---
    
    // 1. Permissões
    $permissions = [
        ['manage_users', 'Gerenciar usuários e permissões'],
        ['manage_roles', 'Gerenciar papéis e permissões'],
        ['view_membros', 'Visualizar membros'],
        ['manage_membros', 'Criar e editar membros'],
        ['view_financeiro', 'Visualizar financeiro'],
        ['manage_financeiro', 'Realizar lançamentos financeiros'],
        ['manage_settings', 'Gerenciar configurações'],
        ['view_reports', 'Visualizar relatórios'],
        ['manage_backup', 'Realizar backup do sistema']
    ];
    $stmt = $db->prepare("INSERT IGNORE INTO permissoes (nome, descricao) VALUES (?, ?)");
    foreach ($permissions as $p) { $stmt->execute($p); }
    echo "<p>Permissões populadas.</p>";

    // 2. Papéis
    $roles = [
        ['Tesouraria', 'Responsável pelas finanças da igreja'],
        ['Secretaria', 'Responsável pelo cadastro de membros'],
        ['Pastor', 'Acesso administrativo e visualização geral']
    ];
    $stmt = $db->prepare("INSERT IGNORE INTO papeis (nome, descricao) VALUES (?, ?)");
    foreach ($roles as $r) { $stmt->execute($r); }
    echo "<p>Papéis populados.</p>";

    // 3. Mapeamento
    $mapping = [
        'Tesouraria' => ['view_financeiro', 'manage_financeiro', 'view_membros', 'view_reports'],
        'Secretaria' => ['view_membros', 'manage_membros'],
        'Pastor' => ['view_membros', 'view_financeiro', 'view_reports']
    ];
    foreach ($mapping as $roleName => $perms) {
        $roleId = $db->query("SELECT id FROM papeis WHERE nome = '$roleName'")->fetchColumn();
        foreach ($perms as $pName) {
            $pId = $db->query("SELECT id FROM permissoes WHERE nome = '$pName'")->fetchColumn();
            if ($roleId && $pId) {
                $db->prepare("INSERT IGNORE INTO papel_permissao (papel_id, permissao_id) VALUES (?, ?)")->execute([$roleId, $pId]);
            }
        }
    }
    echo "<p>Mapeamento de papéis concluído.</p>";

    // 4. Admin User
    $nome  = "Administrador";
    $usuario = "admin";
    $email = "admin@igreja.com";
    $senha = "Igreja@2026";
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE usuario = :u OR email = :e");
    $stmt->execute([':u' => $usuario, ':e' => $email]);
    if (!$stmt->fetch()) {
        $db->prepare("INSERT INTO usuarios (nome, usuario, email, senha, nivel, forçar_mudança_senha) VALUES (?, ?, ?, ?, 'admin', 1)")
           ->execute([$nome, $usuario, $email, $senhaHash]);
        echo "<p>Usuário administrador criado: <b>$usuario</b> / Senha: <b>$senha</b></p>";
    }

    echo "<h2>Instalação Concluída com Sucesso!</h2>";
    echo "<a href='index.php?url=login'>Ir para o Login</a>";

} catch (Exception $e) {
    echo "<h1>Erro na Instalação</h1>";
    echo $e->getMessage();
}
