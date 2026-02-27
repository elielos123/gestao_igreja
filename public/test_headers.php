<?php
header('Content-Type: text/plain');
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'off') . "\n";
echo "HTTP_X_FORWARDED_PROTO: " . ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'none') . "\n";
echo "SERVER_PORT: " . $_SERVER['SERVER_PORT'] . "\n";
echo "REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'] . "\n";
print_r(getallheaders());
?>
