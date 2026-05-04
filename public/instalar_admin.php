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
    
    $nome  = "Administrador Novo";
    $email = "admin@admin.com";
    $senha = "Mudar123!"; // Senha temporária
    $nivel = "admin"; // Nível de acesso
    
    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Verifica se o e-mail já existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    if ($stmt->fetch()) {
        // Se já existe, apenas atualiza a senha e força a mudança
        $sql = "UPDATE usuarios SET senha = :senha, nome = :nome, nivel = :nivel, forçar_mudança_senha = 1 WHERE email = :email";
        $msg = "Usuário '$email' já existia. A senha foi resetada para '$senha'.";
    } else {
        // Se não existe, cria um novo
        $sql = "INSERT INTO usuarios (nome, email, senha, nivel, forçar_mudança_senha) VALUES (:nome, :email, :senha, :nivel, 1)";
        $msg = "Novo administrador criado com sucesso!<br>E-mail: $email<br>Senha: $senha";
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nome'  => $nome,
        ':email' => $email,
        ':senha' => $senhaHash,
        ':nivel' => $nivel
    ]);
    
    echo "<h1>Sucesso!</h1>";
    echo "<p>$msg</p>";
    echo "<p><strong>Atenção:</strong> Ao fazer login, o sistema pedirá para você criar uma nova senha definitiva.</p>";
    echo "<a href='index.php?url=login'>Ir para a página de Login</a>";

} catch (Exception $e) {
    echo "<h1>Erro ao criar usuário</h1>";
    echo $e->getMessage();
}
