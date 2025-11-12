<?php
$page_title = "Generar Token Individual - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

$mensaje = '';
$token_generado = '';

// Función para generar token individual
function generarTokenIndividual($pdo) {
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generar'])) {
    try {
        $token_generado = generarTokenIndividual($pdo);
    } catch (Exception $e) {
        $mensaje = 'Error al generar el token: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    $token = $_POST['token'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    
    if (!empty($token)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO token_api (token, descripcion) VALUES (?, ?)");
            $stmt->execute([$token, $descripcion]);
            
            header('Location: tokens.php?mensaje=Token guardado correctamente');
            exit();
        } catch (PDOException $e) {
            $mensaje = 'Error al guardar el token: ' . $e->getMessage();
        }
    } else {
        $mensaje = 'No hay token para guardar';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="card-title mb-0">
                    <i class="fas fa-key me-2"></i>Generador de Tokens
                </h2>
            </div>
            <div class="card-body">
                <?php if ($mensaje): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Formulario para generar token -->
                <form method="POST" class="mb-4">
                    <div class="d-grid">
                        <button type="submit" name="generar" class="btn btn-primary btn-lg">
                            <i class="fas fa-bolt me-2"></i>Generar Nuevo Token
                        </button>
                    </div>
                </form>
                
                <?php if ($token_generado): ?>
                <!-- Mostrar token generado -->
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-success">
                            <i class="fas fa-check-circle me-2"></i>Token Generado
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Token Generado:</label>
                            <div class="card bg-dark text-light">
                                <div class="card-body">
                                    <code class="token-full" style="font-size: 0.9em; word-break: break-all; font-family: 'Courier New', monospace;">
                                        <?php echo htmlspecialchars($token_generado); ?>
                                    </code>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-success mb-3" onclick="copiarToken()">
                            <i class="fas fa-copy me-2"></i>Copiar Token
                        </button>
                        
                        <!-- Formulario para guardar token -->
                        <form method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token_generado); ?>">
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Descripción (Opcional)
                                </label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" 
                                       placeholder="Ej: Token para API de producción" maxlength="255">
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" name="guardar" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Guardar Token en Base de Datos
                                </button>
                                <a href="tokens.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Información del formato -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Formato de Tokens
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Estructura del Token:</h6>
                        <ul class="list-unstyled">
                            <li><code>[32-caracteres-hex]</code> - Parte aleatoria segura</li>
                            <li><code>-MOT-</code> - Prefijo identificador</li>
                            <li><code>[número]</code> - Número consecutivo único</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Ejemplos:</h6>
                        <ul class="list-unstyled">
                            <li><code>8ed9873d99e3ab18c922eaf4af3ee20f-MOT-1</code></li>
                            <li><code>759503318040d2bea544ac4449aa8707b-MOT-2</code></li>
                            <li><code>a1b2c3d4e5f6789012345678901234567-MOT-3</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copiarToken() {
    const tokenText = `<?php echo $token_generado; ?>`;
    
    navigator.clipboard.writeText(tokenText).then(function() {
        // Mostrar mensaje de éxito
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>¡Copiado!';
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-success');
        }, 2000);
    }).catch(function(err) {
        alert('Error al copiar el token: ' + err);
    });
}
</script>

<?php include 'includes/footer.php'; ?>