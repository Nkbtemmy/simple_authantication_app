<?php
// Get password from command line argument or use default
$password = $argv[1] ?? 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password\n";
echo "Hash: $hashed_password\n";
?> 