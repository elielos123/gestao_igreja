<?php
require 'vendor/autoload.php';
$db = (new App\Config\Database())->getConnection();
$users = $db->query('SELECT id, nome, usuario, email FROM usuarios')->fetchAll();
print_r($users);
