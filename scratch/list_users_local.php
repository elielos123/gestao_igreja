<?php
require_once 'vendor/autoload.php';
require_once 'app/Config/Database.php';

use App\Config\Database;

try {
    $db = (new Database())->getConnection();
    $sql = "SELECT u.usuario, p.nome as papel FROM usuarios u 
            LEFT JOIN usuario_papel up ON u.id = up.usuario_id 
            LEFT JOIN papeis p ON p.id = up.papel_id 
            WHERE u.usuario = 'admin'";
    $res = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    print_r($res);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
