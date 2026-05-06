<?php
require_once 'vendor/autoload.php';
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = (new Database())->getConnection();
    $res = $db->query("SELECT nome, usuario, email, nivel FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    echo "--- USUÁRIOS CADASTRADOS ---\n";
    foreach ($res as $u) {
        echo "Nome: {$u['nome']} | Usuário: {$u['usuario']} | E-mail: {$u['email']} | Nível: {$u['nivel']}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
