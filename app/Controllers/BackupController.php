<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;
use Exception;

class BackupController {

    public function exportar() {
        LoginController::checkAuth();
        \App\Helpers\Acl::check('manage_backup');

        try {
            $db = (new Database())->getConnection();
            
            $tables = [];
            $query = $db->query("SHOW TABLES");
            while ($row = $query->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }
            
            $sql = "-- Backup do Sistema Gestão Igreja\n";
            $sql .= "-- Gerado em: " . date('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                $sql .= "DROP TABLE IF EXISTS `$table`;\n";
                $row2 = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
                $sql .= $row2[1] . ";\n\n";
                
                $query = $db->query("SELECT * FROM `$table`");
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $keys = array_map(function($key) { return "`$key`"; }, array_keys($row));
                        $values = array_map(function($val) use ($db) { 
                            return is_null($val) ? "NULL" : $db->quote($val); 
                        }, array_values($row));
                        
                        $sql .= "INSERT INTO `$table` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $sql .= "\n";
                }
            }
            
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            $filename = "backup_gestao_igreja_" . date('Y-m-d_H-i-s') . ".sql";
            
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1
            header('Pragma: no-cache'); // HTTP 1.0
            header('Expires: 0'); // Proxies
            
            echo $sql;
            exit;

        } catch (Exception $e) {
            die("Erro ao gerar backup: " . $e->getMessage());
        }
    }

    public function importar() {
        LoginController::checkAuth();
        \App\Helpers\Acl::check('manage_backup');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
                if ($_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("Erro no upload do arquivo.");
                }

                $ext = pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'sql') {
                    throw new Exception("Formato inválido. Envie apenas arquivos .sql");
                }

                $sqlContent = file_get_contents($_FILES['backup_file']['tmp_name']);
                if (empty($sqlContent)) {
                    throw new Exception("O arquivo de backup está vazio.");
                }

                $db = (new Database())->getConnection();
                
                // PDO executa múltiplos statements separadas por ponto e vírgula nativamente no MySQL
                $db->exec($sqlContent);
                
                header('Location: index.php?url=ajustes&status=backup_restored');
                exit;
            }
        } catch (Exception $e) {
            header('Location: index.php?url=ajustes&status=backup_error&msg=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
