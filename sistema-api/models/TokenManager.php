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
            
            // Consulta mejorada para manejar tokens existentes
            $stmt = $this->db->prepare("
                SELECT id, token, descripcion, 
                       COALESCE(activo, 1) as activo 
                FROM token_api 
                WHERE token = ?
            ");
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if ($result) {
                // Verificar si el token está activo
                $activo = $result['activo'];
                
                if (!$activo) {
                    error_log("TokenManager: Token inactivo - ID: " . $result['id']);
                    return false;
                }
                
                error_log("TokenManager: Token válido encontrado - ID: " . $result['id']);
                return true;
            } else {
                error_log("TokenManager: Token no encontrado en BD: " . substr($token, 0, 10) . '...');
                
                // Debug: Mostrar tokens disponibles
                $allTokens = $this->getAllTokens();
                error_log("TokenManager: Tokens disponibles: " . count($allTokens));
                foreach ($allTokens as $t) {
                    error_log(" - Token: " . substr($t['token'], 0, 20) . '...');
                }
                
                return false;
            }
        } catch (PDOException $e) {
            error_log("TokenManager Error validando token: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllTokens() {
        try {
            $stmt = $this->db->prepare("
                SELECT id, token, descripcion, 
                       COALESCE(activo, 1) as activo,
                       fecha_creacion 
                FROM token_api 
                ORDER BY id
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("TokenManager Error obteniendo tokens: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTokenInfo($token) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, token, descripcion, 
                       COALESCE(activo, 1) as activo,
                       fecha_creacion 
                FROM token_api 
                WHERE token = ?
            ");
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            return $result;
        } catch (PDOException $e) {
            error_log("TokenManager Error obteniendo info token: " . $e->getMessage());
            return null;
        }
    }
    
    public function logApiRequest($token, $endpoint, $ip, $userAgent) {
        try {
            // Intentar insertar en logs, pero si falla no interrumpir
            $stmt = $this->db->prepare("
                INSERT INTO api_logs (token_id, endpoint, ip_address, user_agent, fecha_consulta) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $tokenInfo = $this->getTokenInfo($token);
            $tokenId = $tokenInfo ? $tokenInfo['id'] : null;
            
            $stmt->execute([$tokenId, $endpoint, $ip, $userAgent]);
            return true;
        } catch (PDOException $e) {
            // Solo loguear el error pero no fallar
            error_log("TokenManager Error logueando request: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para generar token automático
    public function generateAutoToken($descripcion = '') {
        try {
            // Obtener el último ID para determinar el siguiente número
            $stmt = $this->db->query("SELECT MAX(id) as max_id FROM token_api");
            $result = $stmt->fetch();
            $siguiente_numero = ($result['max_id'] ?? 0) + 1;
            
            // Generar parte aleatoria del token (32 caracteres hexadecimales)
            $parte_aleatoria = bin2hex(random_bytes(16));
            
            // Generar el token con el formato: [random]-[prefijo]-[número]
            $token = $parte_aleatoria . '-MOT-' . $siguiente_numero;
            
            // Insertar el token (activo por defecto)
            $stmt = $this->db->prepare("INSERT INTO token_api (token, descripcion, activo) VALUES (?, ?, 1)");
            $stmt->execute([$token, $descripcion]);
            
            return $token;
        } catch (PDOException $e) {
            error_log("TokenManager Error generando token: " . $e->getMessage());
            throw new Exception("Error al generar token automático: " . $e->getMessage());
        }
    }
}
?>