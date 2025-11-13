<?php
$page_title = "Gestión de Tokens - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Manejar activación/desactivación de tokens
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $tokenId = intval($_GET['id']);
    $status = $_GET['toggle_status'] === 'activate' ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE token_api SET activo = ? WHERE id = ?");
        $stmt->execute([$status, $tokenId]);
        
        $action = $status ? 'activado' : 'desactivado';
        header('Location: tokens.php?mensaje=Token ' . $action . ' correctamente');
        exit();
    } catch (PDOException $e) {
        header('Location: tokens.php?mensaje=Error al cambiar estado del token: ' . $e->getMessage());
        exit();
    }
}

// Obtener todos los tokens
try {
    $stmt = $pdo->query("SELECT * FROM token_api ORDER BY id DESC");
    $tokens = $stmt->fetchAll();
} catch (PDOException $e) {
    $tokens = [];
    $error = "Error al cargar tokens: " . $e->getMessage();
}
?>

<?php include 'includes/header.php'; ?>

<!-- Contenedor de Notificaciones Toast -->
<div class="toast-container" id="toastContainer"></div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">
        <i class="fas fa-key me-2"></i>Gestión de Tokens
    </h1>
    <div>
        <a href="generar_token.php" class="btn btn-primary me-2">
            <i class="fas fa-bolt me-1"></i>Generar Token Auto
        </a>
        <a href="agregar_token.php" class="btn btn-success me-2">
            <i class="fas fa-plus me-1"></i>Agregar Manual
        </a>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Tokens
            <span class="badge bg-primary ms-2"><?php echo count($tokens); ?> tokens</span>
        </h5>
    </div>
    <div class="card-body">
        <?php if (count($tokens) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>Token</th>
                            <th>Estado</th>
                            <th>Formato</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tokens as $token): 
                            // Asegurarse de que el campo activo existe
                            $activo = isset($token['activo']) ? $token['activo'] : 1;
                            $descripcion = isset($token['descripcion']) ? $token['descripcion'] : '';
                            $fecha_creacion = isset($token['fecha_creacion']) ? $token['fecha_creacion'] : '';
                        ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo $token['id']; ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($descripcion)): ?>
                                        <?php echo htmlspecialchars($descripcion); ?>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">Sin descripción</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="token-container">
                                        <code class="token-preview" id="token-preview-<?php echo $token['id']; ?>">
                                            <?php echo substr($token['token'], 0, 30) . '...'; ?>
                                        </code>
                                        <code class="token-complete d-none" id="token-complete-<?php echo $token['id']; ?>">
                                            <?php echo htmlspecialchars($token['token']); ?>
                                        </code>
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" 
                                                onclick="toggleToken(<?php echo $token['id']; ?>)">
                                            <span class="toggle-text-<?php echo $token['id']; ?>">Ver</span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($activo): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $es_formato_nuevo = preg_match('/^[a-f0-9]{32}-MOT-\d+$/', $token['token']);
                                    if ($es_formato_nuevo): ?>
                                        <span class="badge bg-success">Auto-Generado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo !empty($fecha_creacion) ? date('d/m/Y', strtotime($fecha_creacion)) : 'N/A'; ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php if ($activo): ?>
                                            <a href="tokens.php?id=<?php echo $token['id']; ?>&toggle_status=deactivate" 
                                               class="btn btn-sm btn-warning" 
                                               title="Desactivar token"
                                               onclick="return confirmNotification('¿Estás seguro de desactivar este token?', this)">
                                                <i class="fas fa-pause"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="tokens.php?id=<?php echo $token['id']; ?>&toggle_status=activate" 
                                               class="btn btn-sm btn-success" 
                                               title="Activar token"
                                               onclick="return confirmNotification('¿Estás seguro de activar este token?', this)">
                                                <i class="fas fa-play"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="ver_token.php?id=<?php echo $token['id']; ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver token completo">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar_token.php?id=<?php echo $token['id']; ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Editar token">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="eliminar_token.php?id=<?php echo $token['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirmNotification('¿Estás seguro de eliminar este token?', this)"
                                           title="Eliminar token">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-key fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No hay tokens registrados</h4>
                <p class="text-muted mb-4">Comience agregando su primer token al sistema.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-center">
                    <a href="generar_token.php" class="btn btn-primary btn-lg me-md-2">
                        <i class="fas fa-bolt me-2"></i>Generar Primer Token
                    </a>
                    <a href="agregar_token.php" class="btn btn-success btn-lg">
                        <i class="fas fa-plus me-2"></i>Agregar Token Manual
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas de Tokens -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Total Tokens</h5>
                        <h3><?php echo count($tokens); ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-key fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Activos</h5>
                        <h3>
                            <?php 
                            $activos = 0;
                            foreach ($tokens as $token) {
                                if (isset($token['activo']) && $token['activo']) {
                                    $activos++;
                                }
                            }
                            echo $activos;
                            ?>
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Inactivos</h5>
                        <h3><?php echo count($tokens) - $activos; ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-pause-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>Auto-Generados</h5>
                        <h3>
                            <?php 
                            $auto_generados = 0;
                            foreach ($tokens as $token) {
                                if (preg_match('/^[a-f0-9]{32}-MOT-\d+$/', $token['token'])) {
                                    $auto_generados++;
                                }
                            }
                            echo $auto_generados;
                            ?>
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-bolt fa-2x"></i>
                    </div>
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

function toggleToken(tokenId) {
    const preview = document.getElementById('token-preview-' + tokenId);
    const complete = document.getElementById('token-complete-' + tokenId);
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

// Función para confirmaciones con notificaciones
function confirmNotification(message, link) {
    if (confirm(message)) {
        notifications.info('Procesando solicitud...', 3000);
        return true;
    }
    return false;
}

// Función para copiar token
function copiarToken(tokenId) {
    const tokenCompleto = document.getElementById('token-complete-' + tokenId).textContent;
    
    navigator.clipboard.writeText(tokenCompleto).then(function() {
        notifications.success('Token copiado al portapapeles');
    }).catch(function(err) {
        notifications.error('Error al copiar el token: ' + err);
    });
}

// Mostrar notificación de carga
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        notifications.info('Gestión de tokens cargada correctamente');
    }, 1000);
});
</script>

<?php include 'includes/footer.php'; ?>