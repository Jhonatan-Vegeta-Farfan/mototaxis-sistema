<?php
require_once __DIR__ . '/../config/api_config.php';
require_once __DIR__ . '/../models/TokenManager.php';

class AuthMiddleware {
    private $tokenManager;
    
    public function __construct() {
        $this->tokenManager = new TokenManager();
    }
    
    public function authenticate() {
        $token = ApiConfig::getTokenFromHeaders();
        
        error_log("AuthMiddleware: Token recibido - " . ($token ? substr($token, 0, 10) . '...' : 'NULL'));
        
        if (!$token) {
            http_response_code(401);
            echo json_encode([
                'success' => false, 
                'message' => 'Token de API no proporcionado. Use el header: X-API-Token',
                'help' => 'Incluya el token en el header: X-API-Token: su_token_aqui',
                'provided_headers' => array_keys(getallheaders())
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Validar formato del token
        if (!ApiConfig::validateTokenFormat($token)) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Formato de token inválido',
                'token_received' => $token
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Validar token en la base de datos
        if (!$this->tokenManager->validateToken($token)) {
            http_response_code(403);
            echo json_encode([
                'success' => false, 
                'message' => 'Token inválido o no autorizado',
                'token_received' => substr($token, 0, 10) . '...',
                'suggestion' => 'Verifique que el token exista en el sistema de gestión'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        error_log("AuthMiddleware: Token válido - " . substr($token, 0, 10) . '...');
        return $token;
    }
    
    public function optionalAuth() {
        $token = ApiConfig::getTokenFromHeaders();
        
        if ($token && ApiConfig::validateTokenFormat($token)) {
            return $this->tokenManager->validateToken($token) ? $token : null;
        }
        
        return null;
    }
}
?>