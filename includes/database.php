<?php

$db = mysqli_connect(
    $_ENV['DB_HOST'], 
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'] ?? '',
    $_ENV['DB_BD']
);

// $db = mysqli_connect('ralphtechnology.com', 'u347393537_root', 'Admin1001', 'u347393537_uptask_mvc');

$db->set_charset('utf8');

// debuguear($_ENV);

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
} /* else {
    echo "Conectado";
} */
