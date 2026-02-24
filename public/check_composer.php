<?php
header('Content-Type: text/plain');
echo "Checking Composer status...\n\n";

echo "1. Which composer: ";
system('which composer 2>&1', $ret1);
echo "Exit code: $ret1\n";

echo "2. Composer version: ";
system('composer --version 2>&1', $ret2);
echo "Exit code: $ret2\n";

echo "3. PHP Version: ";
system('php -v | head -n 1', $ret3);

echo "\n4. Directory list:\n";
system('ls -F ..');
?>
