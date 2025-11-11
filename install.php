<?php
// Script de instalación del sistema
echo "<h1>Instalador del Sistema de MotoTaxis</h1>";

$host = 'localhost:8889';
$username = 'root';
$password = 'root';

try {
    // Conectar sin seleccionar base de datos
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos cliente_api
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cliente_api");
    $pdo->exec("USE cliente_api");
    
    // Crear tabla de usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(50) NOT NULL UNIQUE,
            contrasena VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            activo TINYINT DEFAULT 1,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Crear tabla de tokens API
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS token_api (
            id INT PRIMARY KEY AUTO_INCREMENT,
            token TEXT NOT NULL,
            descripcion VARCHAR(255),
            activo TINYINT DEFAULT 1,
            fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_token_active (activo)
        )
    ");
    
    // Insertar usuario por defecto
    $stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (nombre, contrasena, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin', 'admin123', 'admin@mototaxishuanta.com']);
    
    // Insertar tokens de ejemplo
    $stmt = $pdo->prepare("INSERT IGNORE INTO token_api (token, descripcion) VALUES (?, ?), (?, ?)");
    $stmt->execute([
        '716532101831aa4b61b6816e40e398bd-MOT-2', 'Token principal para API de producción',
        '8ed9873d99e3ab18c922eaf4af3ee20f-STI-1', 'Token secundario para desarrollo'
    ]);
    
    echo "<div class='alert alert-success'>✅ Base de datos cliente_api creada y configurada correctamente</div>";
    
    // Crear base de datos del sistema principal (ejemplo)
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mototaxis_huanta");
    $pdo->exec("USE mototaxis_huanta");
    
    // Crear tabla de mototaxis de ejemplo
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS mototaxis (
            id INT PRIMARY KEY AUTO_INCREMENT,
            numero_asignado VARCHAR(20) UNIQUE NOT NULL,
            nombre_completo VARCHAR(100) NOT NULL,
            dni VARCHAR(8) NOT NULL,
            direccion TEXT,
            placa_rodaje VARCHAR(10),
            anio_fabricacion YEAR,
            marca VARCHAR(50),
            color VARCHAR(30),
            numero_motor VARCHAR(50),
            tipo_motor VARCHAR(50),
            serie VARCHAR(50),
            estado_registro ENUM('ACTIVO', 'INACTIVO') DEFAULT 'ACTIVO',
            fecha_registro DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Insertar datos de ejemplo
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO mototaxis 
        (numero_asignado, nombre_completo, dni, direccion, placa_rodaje, anio_fabricacion, marca, color) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?),
        (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'MT-001', 'Juan Pérez Rodríguez', '12345678', 'Av. Libertad 123', 'ABC-123', 2020, 'Honda', 'Rojo',
        'MT-002', 'María García López', '87654321', 'Jr. Unión 456', 'DEF-456', 2021, 'Yamaha', 'Azul'
    ]);
    
    echo "<div class='alert alert-success'>✅ Base de datos mototaxis_huanta creada y poblada con datos de ejemplo</div>";
    echo "<div class='alert alert-info'>📝 Credenciales por defecto:<br>Usuario: admin<br>Contraseña: admin123</div>";
    echo "<div class='alert alert-warning'>⚠️ Recuerda eliminar este archivo (install.php) después de la instalación</div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Error durante la instalación: " . $e->getMessage() . "</div>";
}
?>

<style>
.alert { padding: 15px; margin: 10px 0; border-radius: 5px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
</style>