<?php
$page_title = "Panel de Control - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Obtener estadísticas
$stmt = $pdo->query("SELECT COUNT(*) as total_tokens FROM token_api");
$total_tokens = $stmt->fetch()['total_tokens'];

$stmt = $pdo->query("SELECT COUNT(*) as total_usuarios FROM usuarios");
$total_usuarios = $stmt->fetch()['total_usuarios'];

// Obtener estadísticas de tokens auto-generados
$stmt = $pdo->query("SELECT COUNT(*) as auto_generados FROM token_api WHERE token REGEXP '^[a-f0-9]{32}-MOT-[0-9]+$'");
$auto_generados = $stmt->fetch()['auto_generados'];

$tokens_manuales = $total_tokens - $auto_generados;

// Obtener todos los tokens para mostrar en el dashboard
$stmt = $pdo->query("SELECT * FROM token_api ORDER BY id DESC");
$tokens = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-3">PANEL DE CONTROL</h1>
        <div class="alert alert-info">
            <i class="fas fa-user me-2"></i>
            Bienvenido, <strong><?php echo $_SESSION['usuario_nombre']; ?></strong>
        </div>
    </div>
</div>



<!-- Acciones Rápidas -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex">
                    <a href="generar_token.php" class="btn btn-primary me-md-2">
                        <i class="fas fa-bolt me-1"></i>Generar Nuevo Token
                    </a>
                    <a href="tokens.php" class="btn btn-success me-md-2">
                        <i class="fas fa-list me-1"></i>Ver Todos los Tokens
                    </a>
                    <a href="agregar_token.php" class="btn btn-warning me-md-2">
                        <i class="fas fa-plus me-1"></i>Agregar Token Manual
                    </a>
                    <a href="../sistema-api/api.php" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt me-1"></i>Abrir API Pública
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista Previa de Todos los Tokens -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>Vista Previa de Tokens
                </h5>
                <div>
                    <span class="badge bg-primary"><?php echo $total_tokens; ?> tokens</span>
                    <span class="badge bg-success"><?php echo $auto_generados; ?> auto</span>
                    <span class="badge bg-warning text-dark"><?php echo $tokens_manuales; ?> manual</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($tokens) > 0): ?>
                    <div class="row">
                        <?php foreach ($tokens as $token): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 token-card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">Token #<?php echo $token['id']; ?></h6>
                                            <div>
                                                <?php 
                                                $es_auto_generado = preg_match('/^[a-f0-9]{32}-MOT-\d+$/', $token['token']);
                                                if ($es_auto_generado): ?>
                                                    <span class="badge bg-success">Auto</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Manual</span>
                                                <?php endif; ?>
                                                <span class="badge bg-secondary"><?php echo strlen($token['token']); ?> chars</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Descripción -->
                                        <?php if ($token['descripcion']): ?>
                                            <p class="card-text mb-2">
                                                <strong>Descripción:</strong><br>
                                                <?php echo htmlspecialchars($token['descripcion']); ?>
                                            </p>
                                            <hr>
                                        <?php endif; ?>

                                        <!-- Token con toggle -->
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Token:</label>
                                            <div class="token-container">
                                                <code class="token-preview" id="dashboard-preview-<?php echo $token['id']; ?>">
                                                    <?php echo substr($token['token'], 0, 25) . '...'; ?>
                                                </code>
                                                <code class="token-complete d-none" id="dashboard-complete-<?php echo $token['id']; ?>">
                                                    <?php echo htmlspecialchars($token['token']); ?>
                                                </code>
                                            </div>
                                        </div>

                                        <!-- Información del formato -->
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <?php if ($es_auto_generado): ?>
                                                    Formato: <code>xxxxxxxx-MOT-n</code>
                                                <?php else: ?>
                                                    Formato personalizado
                                                <?php endif; ?>
                                            </small>
                                        </div>

                                        <!-- Acciones rápidas -->
                                        <div class="btn-group w-100" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="toggleDashboardToken(<?php echo $token['id']; ?>)">
                                                <span class="toggle-text-<?php echo $token['id']; ?>">Ver</span>
                                            </button>
                                            <a href="ver_token.php?id=<?php echo $token['id']; ?>" class="btn btn-sm btn-outline-info">
                                                Completo
                                            </a>
                                            <a href="editar_token.php?id=<?php echo $token['id']; ?>" class="btn btn-sm btn-outline-warning">
                                                Editar
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted small">
                                        <div class="d-flex justify-content-between">
                                            <span>ID: <?php echo $token['id']; ?></span>
                                            <a href="eliminar_token.php?id=<?php echo $token['id']; ?>" 
                                               class="text-danger" 
                                               onclick="return confirm('¿Estás seguro de eliminar este token?')">
                                                Eliminar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay tokens registrados.</p>
                        <div class="d-grid gap-2 d-md-flex justify-content-center">
                            <a href="generar_token.php" class="btn btn-primary me-md-2">
                                <i class="fas fa-bolt me-1"></i>Generar Primer Token
                            </a>
                            <a href="agregar_token.php" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>Agregar Token Manual
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDashboardToken(tokenId) {
    const preview = document.getElementById('dashboard-preview-' + tokenId);
    const complete = document.getElementById('dashboard-complete-' + tokenId);
    const toggleText = document.querySelector('.toggle-text-' + tokenId);
    
    if (preview.classList.contains('d-none')) {
        preview.classList.remove('d-none');
        complete.classList.add('d-none');
        toggleText.textContent = 'Ver';
    } else {
        preview.classList.add('d-none');
        complete.classList.remove('d-none');
        toggleText.textContent = 'Ocultar';
    }
}

// Función para copiar token desde el dashboard
function copiarDashboardToken(tokenId) {
    const tokenCompleto = document.getElementById('dashboard-complete-' + tokenId).textContent;
    
    navigator.clipboard.writeText(tokenCompleto).then(function() {
        // Mostrar mensaje de éxito
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        alert('Error al copiar el token: ' + err);
    });
}
</script>

<?php include 'includes/footer.php'; ?>