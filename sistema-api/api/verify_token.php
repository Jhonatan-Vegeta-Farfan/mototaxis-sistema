<?php
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

// Habilitar CORS primero
ApiConfig::enableCORS();

// Log para debugging
error_log("=== VERIFY TOKEN REQUEST ===");
error_log("Headers: " . json_encode(getallheaders()));
error_log("GET: " . json_encode($_GET));
error_log("POST: " . json_encode($_POST));

try {
    $auth = new AuthMiddleware();
    $token = $auth->authenticate();
    
    // Obtener información del token
    $tokenManager = new TokenManager();
    $tokenInfo = $tokenManager->getTokenInfo($token);
    
    $response = [
        'success' => true, 
        'message' => '✅ Token válido',
        'token_info' => [
            'token_preview' => substr($token, 0, 10) . '...',
            'description' => $tokenInfo['descripcion'] ?? 'Sin descripción',
            'created_date' => $tokenInfo['fecha_creacion'] ?? 'Desconocida',
            'token_id' => $tokenInfo['id'] ?? 'N/A'
        ],
        'validation' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'expires' => 'No expira', // Los tokens no expiran en este sistema
            'permissions' => ['read', 'search']
        ],
        'api_info' => [
            'version' => '1.0',
            'endpoints_available' => [
                '/api/buscar.php?numero=TERMINO',
                '/api/debug_estructura.php',
                '/api/verify_token.php'
            ]
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>