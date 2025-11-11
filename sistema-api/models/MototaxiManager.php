<?php
require_once __DIR__ . '/../config/api_config.php';

class MototaxiManager {
    private $db;
    
    public function __construct() {
        $this->db = ApiConfig::getSystemDB();
    }
    
    public function buscarPorNumero($numeroAsignado) {
        try {
            // Primero, descubrir la estructura de la base de datos
            $estructura = $this->descubrirEstructura();
            
            // Buscar usando la estructura descubierta
            return $this->buscarConEstructura($numeroAsignado, $estructura);
            
        } catch (PDOException $e) {
            error_log("MototaxiManager Error buscando '{$numeroAsignado}': " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage(),
                'search_term' => $numeroAsignado
            ];
        } catch (Exception $e) {
            error_log("MototaxiManager Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'search_term' => $numeroAsignado
            ];
        }
    }
    
    private function descubrirEstructura() {
        $estructura = [
            'tabla_principal' => '',
            'campos' => [],
            'tablas_disponibles' => []
        ];
        
        // Obtener todas las tablas
        $tablas = $this->obtenerTablas();
        $estructura['tablas_disponibles'] = $tablas;
        
        // Lista de posibles tablas principales (en orden de prioridad)
        $posiblesTablas = ['mototaxis', 'conductores', 'vehiculos', 'motorizados', 'registros', 'usuarios'];
        
        foreach ($posiblesTablas as $tabla) {
            if (in_array($tabla, $tablas)) {
                $estructura['tabla_principal'] = $tabla;
                $estructura['campos'] = $this->obtenerCamposTabla($tabla);
                break;
            }
        }
        
        // Si no encontramos tabla principal, usar la primera tabla disponible
        if (empty($estructura['tabla_principal']) && count($tablas) > 0) {
            $estructura['tabla_principal'] = $tablas[0];
            $estructura['campos'] = $this->obtenerCamposTabla($tablas[0]);
        }
        
        return $estructura;
    }
    
    private function obtenerTablas() {
        try {
            $stmt = $this->db->query("SHOW TABLES");
            $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $resultados;
        } catch (Exception $e) {
            error_log("Error obteniendo tablas: " . $e->getMessage());
            return [];
        }
    }
    
    private function obtenerCamposTabla($tabla) {
        try {
            $stmt = $this->db->prepare("DESCRIBE $tabla");
            $stmt->execute();
            $campos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Campo 'Field'
            return $campos;
        } catch (Exception $e) {
            error_log("Error obteniendo campos de $tabla: " . $e->getMessage());
            return [];
        }
    }
    
    private function buscarConEstructura($numeroAsignado, $estructura) {
        $tabla = $estructura['tabla_principal'];
        $campos = $estructura['campos'];
        
        if (empty($tabla)) {
            return [
                'success' => false,
                'message' => 'No se encontró ninguna tabla en la base de datos',
                'available_tables' => $estructura['tablas_disponibles']
            ];
        }
        
        // Construir consulta dinámica basada en los campos disponibles
        $condiciones = [];
        $parametros = [];
        
        // Campos potenciales para búsqueda (en orden de prioridad)
        $camposBusqueda = [
            'numero_asignado', 'numero', 'codigo', 'placa', 'matricula',
            'dni', 'documento', 'cedula', 'doc_identidad',
            'nombre', 'nombre_completo', 'conductor', 'chofer',
            'id', 'codigo_asignado'
        ];
        
        foreach ($camposBusqueda as $campo) {
            if (in_array($campo, $campos)) {
                $condiciones[] = "$campo LIKE ?";
                $parametros[] = "%$numeroAsignado%";
            }
        }
        
        if (empty($condiciones)) {
            // Si no hay campos específicos, buscar en todos los campos de texto
            foreach ($campos as $campo) {
                // Excluir campos que probablemente no sean de búsqueda
                if (!in_array($campo, ['id', 'fecha_creacion', 'fecha_actualizacion', 'created_at', 'updated_at'])) {
                    $condiciones[] = "$campo LIKE ?";
                    $parametros[] = "%$numeroAsignado%";
                }
            }
        }
        
        if (empty($condiciones)) {
            return [
                'success' => false,
                'message' => 'No se encontraron campos de búsqueda adecuados en la tabla: ' . $tabla,
                'available_fields' => $campos
            ];
        }
        
        $whereClause = implode(' OR ', $condiciones);
        $sql = "SELECT * FROM $tabla WHERE $whereClause LIMIT 10";
        
        error_log("Ejecutando consulta: $sql");
        error_log("Parámetros: " . implode(', ', $parametros));
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($parametros);
        $resultados = $stmt->fetchAll();
        
        if ($resultados) {
            // Formatear todos los resultados
            $datosFormateados = array_map([$this, 'formatearDatos'], $resultados);
            
            return [
                'success' => true,
                'count' => count($resultados),
                'data' => $datosFormateados,
                'search_info' => [
                    'table_used' => $tabla,
                    'search_term' => $numeroAsignado,
                    'results_count' => count($resultados)
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'No se encontró mototaxi con el término: ' . $numeroAsignado,
            'search_info' => [
                'table_used' => $tabla,
                'search_term' => $numeroAsignado,
                'available_fields' => $campos
            ]
        ];
    }
    
    private function formatearDatos($datos) {
        // Mapear nombres de campos posibles a campos estandarizados
        $mapeoCampos = [
            'numero_asignado' => $this->buscarCampo($datos, ['numero_asignado', 'numero', 'codigo', 'id', 'codigo_asignado']),
            'nombre_completo' => $this->buscarCampo($datos, ['nombre_completo', 'nombre', 'conductor', 'propietario', 'chofer', 'apellidos']),
            'dni' => $this->buscarCampo($datos, ['dni', 'documento', 'cedula', 'doc_identidad', 'num_documento']),
            'direccion' => $this->buscarCampo($datos, ['direccion', 'domicilio', 'residencia', 'dir', 'direccion_completa']),
            'placa_rodaje' => $this->buscarCampo($datos, ['placa_rodaje', 'placa', 'matricula', 'numero_placa', 'placa_vehiculo']),
            'anio_fabricacion' => $this->buscarCampo($datos, ['anio_fabricacion', 'anio', 'modelo', 'ano_fab', 'year']),
            'marca' => $this->buscarCampo($datos, ['marca', 'fabricante', 'marca_vehiculo', 'marca_moto']),
            'color' => $this->buscarCampo($datos, ['color', 'color_vehiculo', 'color_moto']),
            'numero_motor' => $this->buscarCampo($datos, ['numero_motor', 'motor', 'num_motor', 'motor_numero']),
            'tipo_motor' => $this->buscarCampo($datos, ['tipo_motor', 'motor_tipo', 'tipo_motor']),
            'serie' => $this->buscarCampo($datos, ['serie', 'chasis', 'numero_chasis', 'serie_chasis', 'num_chasis']),
            'fecha_registro' => $this->buscarCampo($datos, ['fecha_registro', 'fecha_ingreso', 'fecha_alta', 'created_at', 'fecha_creacion']),
            'estado_registro' => $this->buscarCampo($datos, ['estado_registro', 'estado', 'status', 'condicion', 'activo'])
        ];
        
        // Información de la empresa (fija para todos los registros)
        $empresaInfo = [
            'razon_social' => 'Mototaxis Huanta',
            'ruc' => '20123456789',
            'direccion' => 'Huanta, Ayacucho, Perú'
        ];
        
        return [
            'numero_asignado' => $mapeoCampos['numero_asignado'] ?? $datos[key($datos)] ?? 'N/A',
            'nombre_completo' => $mapeoCampos['nombre_completo'] ?? 'No disponible',
            'dni' => $mapeoCampos['dni'] ?? 'No disponible',
            'direccion' => $mapeoCampos['direccion'] ?? 'No especificado',
            'placa_rodaje' => $mapeoCampos['placa_rodaje'] ?? 'No disponible',
            'anio_fabricacion' => $mapeoCampos['anio_fabricacion'] ?? 'No especificado',
            'marca' => $mapeoCampos['marca'] ?? 'No especificado',
            'color' => $mapeoCampos['color'] ?? 'No especificado',
            'numero_motor' => $mapeoCampos['numero_motor'] ?? 'No especificado',
            'tipo_motor' => $mapeoCampos['tipo_motor'] ?? 'No especificado',
            'serie' => $mapeoCampos['serie'] ?? 'No especificado',
            'fecha_registro' => $mapeoCampos['fecha_registro'] ?? date('Y-m-d'),
            'estado_registro' => $mapeoCampos['estado_registro'] ?? 'ACTIVO',
            'empresa' => $empresaInfo,
            // Incluir todos los datos originales para debugging
            'datos_originales' => $datos
        ];
    }
    
    private function buscarCampo($datos, $nombresPosibles) {
        foreach ($nombresPosibles as $nombre) {
            if (isset($datos[$nombre]) && !empty($datos[$nombre]) && $datos[$nombre] !== '0000-00-00') {
                return $datos[$nombre];
            }
        }
        return null;
    }
    
    // Método para debugging - mostrar estructura de la BD
    public function obtenerInfoEstructura() {
        try {
            $tablas = [];
            $stmt = $this->db->query("SHOW TABLES");
            $tablasResult = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($tablasResult as $tabla) {
                $stmt = $this->db->prepare("DESCRIBE $tabla");
                $stmt->execute();
                $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $tablas[$tabla] = $campos;
            }
            
            return [
                'success' => true,
                'database' => ApiConfig::SYSTEM_DB_NAME,
                'total_tables' => count($tablas),
                'estructura' => $tablas
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'database' => ApiConfig::SYSTEM_DB_NAME
            ];
        }
    }
    
    // Método para obtener estadísticas
    public function obtenerEstadisticas() {
        try {
            $tablas = $this->obtenerTablas();
            $estadisticas = [];
            
            foreach ($tablas as $tabla) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM $tabla");
                $stmt->execute();
                $count = $stmt->fetch()['total'];
                $estadisticas[$tabla] = $count;
            }
            
            return [
                'success' => true,
                'estadisticas' => $estadisticas,
                'total_tablas' => count($tablas)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>