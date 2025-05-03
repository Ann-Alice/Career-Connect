<?php
$password = "password";
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "<pre>";
echo "Original password: " . $password . "\n";
echo "Hashed password: " . $hash . "\n";
echo "</pre>";
?> 