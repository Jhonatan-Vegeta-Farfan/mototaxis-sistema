<?php
class ApiConfig {
    // Configuración para la base de datos de tokens
    const DB_HOST = 'localhost';
    const DB_NAME = 'prograp_cliente_api';
    const DB_USER = 'root';
    const DB_PASS = ''; // Para XAMPP
    
    // Configuración para el sistema principal
    const SYSTEM_DB_HOST = 'localhost';
    const SYSTEM_DB_NAME = 'mototaxis_huanta';
    const SYSTEM_DB_USER = 'root';
    const SYSTEM_DB_PASS = ''; // Para XAMPP
    
    const API_KEY_HEADER = 'X-API-Token';
    const ALLOWED_ORIGINS = [
        'https://mototaxis-huanta.dpweb2024.com',
        'https://localhost:8888',
        'https://127.0.0.1:8888',
        'https://localhost',
        'http://localhost',
        'https://127.0.0.1',
        'http://127.0.0.1',
        'https://localhost:3000',
        'http://localhost:3000',
        '*'
    ];
    
    public static function getTokenDB() {
        try {
            $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Error conexión BD tokens: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos de tokens: " . $e->getMessage());
        }
    }
    
    public static function getSystemDB() {
        try {
            $dsn = "mysql:host=" . self::SYSTEM_DB_HOST . ";dbname=" . self::SYSTEM_DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, self::SYSTEM_DB_USER, self::SYSTEM_DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Error conexión BD sistema: " . $e->getMessage());
            throw new Exception("Error de conexión al sistema principal: " . $e->getMessage());
        }
    }
    
    public static function enableCORS() {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        // Si no hay origen específico o está en la lista de permitidos
        if (empty($http_origin) || in_array($http_origin, self::ALLOWED_ORIGINS) || in_array('*', self::ALLOWED_ORIGINS)) {
            header("Access-Control-Allow-Origin: " . ($http_origin ?: '*'));
        } else {
            // Si el origen no está permitido, usar el primero de la lista
            header("Access-Control-Allow-Origin: " . self::ALLOWED_ORIGINS[0]);
        }
        
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
        header("Access-Control-Allow-Headers: X-API-Token, Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 3600");
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        header('Content-Type: application/json; charset=utf-8');
    }
    
    public static function getTokenFromHeaders() {
        // Primero buscar en headers personalizados
        $headers = getallheaders();
        
        if (isset($headers[self::API_KEY_HEADER])) {
            return trim($headers[self::API_KEY_HEADER]);
        }
        
        // Buscar en Authorization header
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return trim($matches[1]);
            }
            return trim($auth);
        }
        
        // Buscar en $_SERVER
        if (isset($_SERVER['HTTP_X_API_TOKEN'])) {
            return trim($_SERVER['HTTP_X_API_TOKEN']);
        }
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return trim($matches[1]);
            }
            return trim($auth);
        }
        
        // Buscar en $_GET como último recurso
        if (isset($_GET['token'])) {
            return trim($_GET['token']);
        }
        
        // Buscar en $_POST
        if (isset($_POST['token'])) {
            return trim($_POST['token']);
        }
        
        return null;
    }
    
    public static function validateTokenFormat($token) {
        if (empty($token)) {
            return false;
        }
        
        // Validar longitud mínima
        if (strlen($token) < 10) {
            return false;
        }
        
        // Validar caracteres (puede contener letras, números, guiones, puntos)
        if (!preg_match('/^[a-zA-Z0-9\-\._]+$/', $token)) {
            return false;
        }
        
        return true;
    }
}
?>