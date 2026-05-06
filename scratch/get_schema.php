<?php
require 'vendor/autoload.php';
$db = (new App\Config\Database())->getConnection();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    $res = $db->query("SHOW CREATE TABLE `$table`")->fetch();
    echo $res['Create Table'] . ";\n\n";
}
