<?php
$page_title = "Agregar Token - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$mensaje = '';

// Función para generar token automáticamente
function generarTokenAutomatico($pdo) {
    // Obtener el último ID para determinar el siguiente número
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM token_api");
    $result = $stmt->fetch();
    $siguiente_numero = ($result['max_id'] ?? 0) + 1;
    
    // Generar parte aleatoria del token (32 caracteres hexadecimales)
    $parte_aleatoria = bin2hex(random_bytes(16));
    
    // Generar el token con el formato: [random]-[prefijo]-[número]
    $token = $parte_aleatoria . '-MOT-' . $siguiente_numero;
    
    return $token;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descripcion = $_POST['descripcion'] ?? '';
    
    try {
        // Generar token automáticamente
        $token = generarTokenAutomatico($pdo);
        
        $stmt = $pdo->prepare("INSERT INTO token_api (token, descripcion) VALUES (?, ?)");
        $stmt->execute([$token, $descripcion]);
        
        header('Location: tokens.php?mensaje=Token generado y agregado correctamente');
        exit();
    } catch (PDOException $e) {
        $mensaje = 'Error al generar el token: ' . $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h2 class="card-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Generar Nuevo Token
                </h2>
            </div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-tag me-1"></i>Descripción (Opcional)
                        </label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" 
                               placeholder="Ej: Token para API de producción" maxlength="255">
                        <div class="form-text">Describe para qué se usará este token.</div>
                    </div>
                    
                    <div class="mb-4 p-3 bg-light rounded">
                        <label class="form-label fw-bold">
                            <i class="fas fa-info-circle me-1"></i>Información del Token
                        </label>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-key me-2"></i>
                            <strong>El token se generará automáticamente</strong> con el formato:<br>
                            <code>[32-caracteres-hexadecimales]-MOT-[número-consecutivo]</code>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-magic me-1"></i>Generar Token Automáticamente
                        </button>
                        <a href="tokens.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información Importante
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Los tokens se generan automáticamente con formato único</li>
                    <li><i class="fas fa-check text-success me-2"></i>Formato: <code>xxxxxxxx-MOT-n</code> (donde n es número consecutivo)</li>
                    <li><i class="fas fa-check text-success me-2"></i>Cada token es único y se genera de forma segura</li>
                    <li><i class="fas fa-check text-success me-2"></i>No es necesario ingresar manualmente el token</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>