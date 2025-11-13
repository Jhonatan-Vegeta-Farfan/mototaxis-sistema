<?php
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/MototaxiManager.php';

// Habilitar CORS
ApiConfig::enableCORS();

// Log para debugging
error_log("=== BUSCAR MOTOTAXI REQUEST ===");

try {
    // Autenticar con token
    $auth = new AuthMiddleware();
    $token = $auth->authenticate();
    
    // Log de la solicitud
    $tokenManager = new TokenManager();
    $tokenManager->logApiRequest(
        $token, 
        '/api/buscar.php', 
        $_SERVER['REMOTE_ADDR'] ?? 'unknown', 
        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    );
    
    // Obtener parámetros
    $numeroAsignado = $_GET['numero'] ?? '';
    
    if (empty($numeroAsignado)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Parámetro "numero" requerido. Ej: ?numero=MT-001',
            'example' => '/api/buscar.php?numero=MT-001',
            'tip' => 'Puede buscar por número asignado, placa, DNI, etc.'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Validar longitud del término de búsqueda
    if (strlen($numeroAsignado) < 2) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'El término de búsqueda debe tener al menos 2 caracteres',
            'search_term' => $numeroAsignado
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Buscar mototaxi en el sistema principal
    $mototaxiManager = new MototaxiManager();
    $result = $mototaxiManager->buscarPorNumero($numeroAsignado);
    
    // Agregar información de la solicitud
    $result['request_info'] = [
        'numero_buscado' => $numeroAsignado,
        'timestamp' => date('Y-m-d H:i:s'),
        'token_used' => substr($token, 0, 10) . '...',
        'api_version' => '1.0'
    ];
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
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