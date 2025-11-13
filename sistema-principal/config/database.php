<?php
// Configuración adaptable para diferentes entornos
$host = 'localhost';
$dbname = 'prograp_cliente_api';
$username = 'root';
$password = 'root';

// Detectar entorno (MAMP, XAMPP, hosting)
if (file_exists('/Applications/MAMP')) {
    // MAMP
    $host = 'localhost:8889';
    $password = 'root';
} elseif (file_exists('C:\xampp') || file_exists('/opt/lampp')) {
    // XAMPP
    $password = 'root';
}

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>