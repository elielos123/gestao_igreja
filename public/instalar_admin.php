<?php
/**
 * Script de Emergência para Criar Administrador
 * Acesse: seu-site.vercel.app/instalar_admin.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/Database.php';

use App\Config\Database;

try {
    $db = (new Database())->getConnection();
    
    $nome  = "Administrador";
    $usuario = "admin";
    $email = "admin@igreja.com";
    $senha = "Igreja@2026"; // Senha temporária
    $nivel = "admin";
    
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Verifica se já existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE usuario = :u OR email = :e");
    $stmt->execute([':u' => $usuario, ':e' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $sql = "UPDATE usuarios SET senha = :senha, usuario = :u, email = :e, nome = :nome, nivel = :nivel, forçar_mudança_senha = 1 WHERE id = :id";
        $params = [':senha' => $senhaHash, ':u' => $usuario, ':e' => $email, ':nome' => $nome, ':nivel' => $nivel, ':id' => $user['id']];
        $msg = "Usuário '$usuario' atualizado. Senha resetada para '$senha'.";
    } else {
        $sql = "INSERT INTO usuarios (nome, usuario, email, senha, nivel, forçar_mudança_senha) VALUES (:nome, :u, :e, :senha, :nivel, 1)";
        $params = [':nome' => $nome, ':u' => $usuario, ':e' => $email, ':senha' => $senhaHash, ':nivel' => $nivel];
        $msg = "Novo administrador '$usuario' criado com sucesso! Senha: $senha";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    echo "<h1>Sucesso!</h1>";
    echo "<p>$msg</p>";
    echo "<p><strong>Atenção:</strong> Ao fazer login, o sistema pedirá para você criar uma nova senha definitiva.</p>";
    echo "<a href='index.php?url=login'>Ir para a página de Login</a>";

} catch (Exception $e) {
    echo "<h1>Erro ao criar usuário</h1>";
    echo $e->getMessage();
}
