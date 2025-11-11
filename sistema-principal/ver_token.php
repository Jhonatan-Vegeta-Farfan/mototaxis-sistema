<?php
$page_title = "Ver Token - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: tokens.php');
    exit();
}

$id = $_GET['id'];

// Obtener token actual
$stmt = $pdo->prepare("SELECT * FROM token_api WHERE id = ?");
$stmt->execute([$id]);
$token = $stmt->fetch();

if (!$token) {
    header('Location: tokens.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                <h2 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>Visualización Completa del Token
                </h2>
                <a href="tokens.php" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Volver a la lista
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="border-bottom pb-2">
                            <i class="fas fa-info-circle me-2"></i>Información del Token
                        </h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%" class="bg-light">ID:</th>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo $token['id']; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Descripción:</th>
                                <td>
                                    <?php if ($token['descripcion']): ?>
                                        <?php echo htmlspecialchars($token['descripcion']); ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Sin descripción</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Longitud:</th>
                                <td>
                                    <span class="badge bg-primary"><?php echo strlen($token['token']); ?> caracteres</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Primeros caracteres:</th>
                                <td>
                                    <code class="text-muted"><?php echo substr($token['token'], 0, 30) . '...'; ?></code>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-key me-2"></i>Token Completo:
                    </label>
                    <div class="card bg-dark text-light">
                        <div class="card-body">
                            <code class="token-full" style="font-size: 0.9em; word-break: break-all; white-space: pre-wrap; font-family: 'Courier New', monospace;">
                                <?php echo htmlspecialchars($token['token']); ?>
                            </code>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-success" onclick="copiarToken()">
                        <i class="fas fa-copy me-2"></i>Copiar Token al Portapapeles
                    </button>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Nota:</strong> Este token es sensible. Asegúrese de manejarlo con cuidado y no compartirlo innecesariamente.
                </div>

                <div class="d-grid gap-2 d-md-flex mt-4">
                    <a href="editar_token.php?id=<?php echo $token['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar Token
                    </a>
                    <a href="tokens.php" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i>Volver a la Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copiarToken() {
    const tokenText = `<?php echo $token['token']; ?>`;
    
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