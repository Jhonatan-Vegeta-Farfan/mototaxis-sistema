<?php
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/MototaxiManager.php';

// Habilitar CORS
ApiConfig::enableCORS();

// Log para debugging
error_log("=== DEBUG ESTRUCTURA REQUEST ===");

try {
    // Autenticar con token
    $auth = new AuthMiddleware();
    $token = $auth->authenticate();
    
    // Log de la solicitud
    $tokenManager = new TokenManager();
    $tokenManager->logApiRequest(
        $token, 
        '/api/debug_estructura.php', 
        $_SERVER['REMOTE_ADDR'] ?? 'unknown', 
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    );
    
    $mototaxiManager = new MototaxiManager();
    
    // Obtener información de estructura
    $estructura = $mototaxiManager->obtenerInfoEstructura();
    
    // Obtener estadísticas
    $estadisticas = $mototaxiManager->obtenerEstadisticas();
    
    // Combinar resultados
    $result = [
        'success' => true,
        'timestamp' => date('Y-m-d H:i:s'),
        'database_info' => [
            'system_database' => ApiConfig::SYSTEM_DB_NAME,
            'token_database' => ApiConfig::DB_NAME
        ],
        'estructura' => $estructura,
        'estadisticas' => $estadisticas,
        'token_info' => [
            'token_used' => substr($token, 0, 10) . '...',
            'request_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>