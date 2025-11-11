<?php
$page_title = "Editar Token - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: tokens.php');
    exit();
}

$id = $_GET['id'];
$mensaje = '';

// Obtener token actual
$stmt = $pdo->prepare("SELECT * FROM token_api WHERE id = ?");
$stmt->execute([$id]);
$token = $stmt->fetch();

if (!$token) {
    header('Location: tokens.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_token = $_POST['token'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    
    if (!empty($nuevo_token)) {
        try {
            $stmt = $pdo->prepare("UPDATE token_api SET token = ?, descripcion = ? WHERE id = ?");
            $stmt->execute([$nuevo_token, $descripcion, $id]);
            
            header('Location: tokens.php?mensaje=Token actualizado correctamente');
            exit();
        } catch (PDOException $e) {
            $mensaje = 'Error al actualizar el token: ' . $e->getMessage();
        }
    } else {
        $mensaje = 'Por favor, ingrese un token válido';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h2 class="card-title mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Token
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
                               value="<?php echo htmlspecialchars($token['descripcion'] ?? ''); ?>"
                               placeholder="Ej: Token para API de producción" maxlength="255">
                        <div class="form-text">Describe para qué se usará este token.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="token" class="form-label">
                            <i class="fas fa-key me-1"></i>Token <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="token" name="token" rows="4" required><?php echo htmlspecialchars($token['token']); ?></textarea>
                        <div class="form-text">Token completo. Puede verlo completamente en la página de visualización.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>Actualizar Token
                        </button>
                        <a href="tokens.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información del token -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>Información del Token
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID del Token:</strong> <?php echo $token['id']; ?></p>
                        <p><strong>Longitud:</strong> <?php echo strlen($token['token']); ?> caracteres</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Primeros caracteres:</strong> 
                            <code><?php echo substr($token['token'], 0, 20) . '...'; ?></code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>