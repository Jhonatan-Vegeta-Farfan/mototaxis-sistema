<?php
$page_title = "Panel de Control - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Manejar activación/desactivación de tokens desde el dashboard
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $tokenId = intval($_GET['id']);
    $status = $_GET['toggle_status'] === 'activate' ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE token_api SET activo = ? WHERE id = ?");
        $stmt->execute([$status, $tokenId]);
        
        $action = $status ? 'activado' : 'desactivado';
        header('Location: dashboard.php?mensaje=Token ' . $action . ' correctamente');
        exit();
    } catch (PDOException $e) {
        header('Location: dashboard.php?mensaje=Error al cambiar estado del token: ' . $e->getMessage());
        exit();
    }
}

// Obtener estadísticas
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total_tokens FROM token_api");
    $total_tokens = $stmt->fetch()['total_tokens'];

    $stmt = $pdo->query("SELECT COUNT(*) as total_usuarios FROM usuarios");
    $total_usuarios = $stmt->fetch()['total_usuarios'];

    // Obtener estadísticas de tokens auto-generados
    $stmt = $pdo->query("SELECT COUNT(*) as auto_generados FROM token_api WHERE token REGEXP '^[a-f0-9]{32}-MOT-[0-9]+$'");
    $auto_generados = $stmt->fetch()['auto_generados'];

    $tokens_manuales = $total_tokens - $auto_generados;

    // Obtener estadísticas de tokens activos/inactivos
    $stmt = $pdo->query("SELECT COUNT(*) as activos FROM token_api WHERE activo = 1");
    $tokens_activos = $stmt->fetch()['activos'];
    
    $tokens_inactivos = $total_tokens - $tokens_activos;

    // Obtener todos los tokens para mostrar en el dashboard
    $stmt = $pdo->query("SELECT * FROM token_api ORDER BY id DESC LIMIT 6");
    $tokens = $stmt->fetchAll();
} catch (PDOException $e) {
    $total_tokens = 0;
    $total_usuarios = 0;
    $auto_generados = 0;
    $tokens_manuales = 0;
    $tokens_activos = 0;
    $tokens_inactivos = 0;
    $tokens = [];
    $error = "Error al cargar estadísticas: " . $e->getMessage();
}
?>

<?php include 'includes/header.php'; ?>

<!-- Contenedor de Notificaciones Toast -->
<div class="toast-container" id="toastContainer"></div>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-3">PANEL DE CONTROL</h1>
        
        <div class="alert alert-info">
            <i class="fas fa-user me-2"></i>
            Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
        </div>
    </div>
</div>

<!-- Estadísticas Principales -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Tokens</h5>
                        <h3><?php echo $total_tokens; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-key fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <small>
                        <i class="fas fa-check-circle me-1"></i><?php echo $tokens_activos; ?> activos
                        <i class="fas fa-pause-circle ms-2 me-1"></i><?php echo $tokens_inactivos; ?> inactivos
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Tokens Activos</h5>
                        <h3><?php echo $tokens_activos; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <small>
                        <i class="fas fa-bolt me-1"></i><?php echo $auto_generados; ?> auto-generados
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Tokens Inactivos</h5>
                        <h3><?php echo $tokens_inactivos; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <small>
                        <i class="fas fa-edit me-1"></i><?php echo $tokens_manuales; ?> manuales
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Usuarios</h5>
                        <h3><?php echo $total_usuarios; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <small>
                        <i class="fas fa-user me-1"></i>Sistema de gestión
                    </small>
                </div>
            </div>
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

<!-- Vista Previa de Tokens Recientes -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>Tokens Recientes
                </h5>
                <div>
                    <span class="badge bg-primary"><?php echo count($tokens); ?> tokens</span>
                    <a href="tokens.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-right me-1"></i>Ver Todos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($tokens) > 0): ?>
                    <div class="row">
                        <?php foreach ($tokens as $token): 
                            // Asegurarse de que el campo activo existe
                            $activo = isset($token['activo']) ? $token['activo'] : 1;
                            $descripcion = isset($token['descripcion']) ? $token['descripcion'] : '';
                        ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 token-card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">Token #<?php echo $token['id']; ?></h6>
                                            <div>
                                                <?php if ($activo): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                                <?php 
                                                $es_auto_generado = preg_match('/^[a-f0-9]{32}-MOT-\d+$/', $token['token']);
                                                if ($es_auto_generado): ?>
                                                    <span class="badge bg-success">Auto</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Manual</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <!-- Descripción -->
                                        <?php if (!empty($descripcion)): ?>
                                            <p class="card-text mb-2">
                                                <strong>Descripción:</strong><br>
                                                <?php echo htmlspecialchars($descripcion); ?>
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
                                            
                                            <!-- Botón Activar/Desactivar -->
                                            <?php if ($activo): ?>
                                                <a href="dashboard.php?id=<?php echo $token['id']; ?>&toggle_status=deactivate" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Desactivar token"
                                                   onclick="return confirm('¿Estás seguro de desactivar este token?')">
                                                    <i class="fas fa-pause"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="dashboard.php?id=<?php echo $token['id']; ?>&toggle_status=activate" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="Activar token"
                                                   onclick="return confirm('¿Estás seguro de activar este token?')">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="ver_token.php?id=<?php echo $token['id']; ?>" class="btn btn-sm btn-outline-info">
                                                Completo
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted small">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>ID: <?php echo $token['id']; ?></span>
                                            <div>
                                                <a href="editar_token.php?id=<?php echo $token['id']; ?>" 
                                                   class="text-warning me-2" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="eliminar_token.php?id=<?php echo $token['id']; ?>" 
                                                   class="text-danger" 
                                                   onclick="return confirm('¿Estás seguro de eliminar este token?')"
                                                   title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Mensaje si hay más tokens -->
                    <?php if ($total_tokens > 6): ?>
                        <div class="text-center mt-3">
                            <p class="text-muted">
                                Mostrando los 6 tokens más recientes. 
                                <a href="tokens.php" class="text-decoration-none">Ver todos los <?php echo $total_tokens; ?> tokens</a>
                            </p>
                        </div>
                    <?php endif; ?>
                    
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

<!-- Resumen de Actividad -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Resumen de Tokens
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-primary"><?php echo $total_tokens; ?></h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h4 class="text-success"><?php echo $tokens_activos; ?></h4>
                            <small class="text-muted">Activos</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div>
                            <h4 class="text-warning"><?php echo $tokens_inactivos; ?></h4>
                            <small class="text-muted">Inactivos</small>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info"><?php echo $auto_generados; ?></h4>
                            <small class="text-muted">Auto-Generados</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div>
                            <h4 class="text-secondary"><?php echo $tokens_manuales; ?></h4>
                            <small class="text-muted">Manuales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>Gestión Rápida
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="generar_token.php" class="btn btn-outline-primary text-start">
                        <i class="fas fa-bolt me-2"></i>Generar Token Automático
                    </a>
                    <a href="tokens.php" class="btn btn-outline-success text-start">
                        <i class="fas fa-list me-2"></i>Gestionar Todos los Tokens
                    </a>
                    <a href="../sistema-api/api.php" target="_blank" class="btn btn-outline-info text-start">
                        <i class="fas fa-external-link-alt me-2"></i>Probar API Pública
                    </a>
                    <?php if ($tokens_inactivos > 0): ?>
                        <a href="tokens.php?filter=inactive" class="btn btn-outline-warning text-start">
                            <i class="fas fa-pause-circle me-2"></i>Ver Tokens Inactivos (<?php echo $tokens_inactivos; ?>)
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de Notificaciones Toast
class NotificationSystem {
    constructor() {
        this.container = document.getElementById('toastContainer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'toast-container';
            this.container.id = 'toastContainer';
            document.body.appendChild(this.container);
        }
        this.toastCount = 0;
    }

    show(message, type = 'info', duration = 5000) {
        const toastId = 'toast-' + Date.now() + '-' + this.toastCount++;
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-triangle',
            warning: 'fa-exclamation-circle',
            info: 'fa-info-circle'
        };

        const toastHTML = `
            <div id="${toastId}" class="custom-toast toast-${type}" role="alert">
                <div class="toast-header">
                    <i class="fas ${icons[type]} me-2"></i>
                    <strong class="me-auto">${this.getTitle(type)}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
                <div class="toast-progress"></div>
            </div>
        `;

        this.container.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.getElementById(toastId);

        // Auto-remove after duration
        setTimeout(() => {
            this.hide(toastElement);
        }, duration);

        // Auto-remove on close button click
        toastElement.querySelector('[data-bs-dismiss="toast"]').addEventListener('click', () => {
            this.hide(toastElement);
        });

        return toastElement;
    }

    getTitle(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información'
        };
        return titles[type] || 'Notificación';
    }

    hide(toastElement) {
        if (toastElement) {
            toastElement.classList.add('hiding');
            setTimeout(() => {
                if (toastElement.parentNode) {
                    toastElement.parentNode.removeChild(toastElement);
                }
            }, 300);
        }
    }

    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 5000) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
}

// Inicializar sistema de notificaciones
const notifications = new NotificationSystem();

// Mostrar notificaciones de mensajes existentes
<?php if (isset($_GET['mensaje'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        notifications.success('<?php echo addslashes($_GET['mensaje']); ?>');
    }, 1000);
});
<?php endif; ?>

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
        notifications.error('Error al copiar el token: ' + err);
    });
}

// Actualizar la página cada 30 segundos para mantener las estadísticas actualizadas
setTimeout(function() {
    window.location.reload();
}, 30000);

// Mostrar notificación de bienvenida
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        notifications.info('Bienvenido al Panel de Control de MotoTaxis Huanta');
    }, 1500);
});
</script>

<?php include 'includes/footer.php'; ?>