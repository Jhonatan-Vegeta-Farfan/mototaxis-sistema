<?php
require_once __DIR__ . '/../config/api_config.php';

class TokenManager {
    private $db;
    
    public function __construct() {
        $this->db = ApiConfig::getTokenDB();
    }
    
    public function validateToken($token) {
        try {
            // Limpiar el token
            $token = trim($token);
            
            if (empty($token)) {
                error_log("TokenManager: Token vacío");
                return false;
            }
            
            $stmt = $this->db->prepare("SELECT id, token, descripcion FROM token_api WHERE token = ?");
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if ($result) {
                error_log("TokenManager: Token válido encontrado - ID: " . $result['id']);
                return true;
            } else {
                error_log("TokenManager: Token no encontrado en BD");
                
                // Log de tokens disponibles para debugging
                $allTokens = $this->getAllTokens();
                error_log("TokenManager: Tokens disponibles: " . count($allTokens));
                
                return false;
            }
        } catch (PDOException $e) {
            error_log("TokenManager Error validando token: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllTokens() {
        try {
            $stmt = $this->db->prepare("SELECT id, token, descripcion FROM token_api ORDER BY id");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TokenManager Error obteniendo tokens: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTokenInfo($token) {
        try {
            $stmt = $this->db->prepare("SELECT id, token, descripcion, fecha_creacion FROM token_api WHERE token = ?");
            $stmt->execute([$token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("TokenManager Error obteniendo info token: " . $e->getMessage());
            return null;
        }
    }
    
    public function logApiRequest($token, $endpoint, $ip, $userAgent) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO api_logs (token_id, endpoint, ip_address, user_agent, fecha_consulta) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $tokenInfo = $this->getTokenInfo($token);
            $tokenId = $tokenInfo ? $tokenInfo['id'] : null;
            
            $stmt->execute([$tokenId, $endpoint, $ip, $userAgent]);
            return true;
        } catch (PDOException $e) {
            error_log("TokenManager Error logueando request: " . $e->getMessage());
            return false;
        }
    }
}
?>