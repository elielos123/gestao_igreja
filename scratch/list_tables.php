<?php
require 'vendor/autoload.php';
try {
    $db = (new App\Config\Database())->getConnection();
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas encontradas:\n";
    print_r($tables);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
