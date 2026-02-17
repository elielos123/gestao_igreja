<?php
require_once dirname(__DIR__) . '/app/Config/Database.php';
try {
    $db = new Database();
    $conn = $db->getConnection();
    if($conn) {
        echo "✅ Conexão com o Banco de Dados: OK!<br>";
        $query = $conn->query("SELECT COUNT(*) as total FROM entradas");
        $res = $query->fetch();
        echo "📊 Total de entradas no banco: " . $res['total'];
    }
} catch (Exception $e) {
    echo "❌ ERRO DE CONEXÃO: " . $e->getMessage();
}