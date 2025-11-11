<?php
$page_title = "Gestión de Tokens - MotoTaxis Cliente";
require_once 'includes/auth_check.php';
require_once 'config/database.php';

// Obtener todos los tokens
$stmt = $pdo->query("SELECT * FROM token_api ORDER BY id DESC");
$tokens = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">
        <i class="fas fa-key me-2"></i>Gestión de Tokens
    </h1>
    <div>
        <a href="agregar_token.php" class="btn btn-success me-2">
            <i class="fas fa-plus me-1"></i>Agregar Token
        </a>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($_GET['mensaje']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Lista de Tokens
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
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tokens as $token): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo $token['id']; ?></span>
                                </td>
                                <td>
                                    <?php if ($token['descripcion']): ?>
                                        <?php echo htmlspecialchars($token['descripcion']); ?>
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
                                            <span class="toggle-text">Ver</span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
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
                                           onclick="return confirm('¿Estás seguro de eliminar este token?')"
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
                <a href="agregar_token.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Agregar Primer Token
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleToken(tokenId) {
    const preview = document.getElementById('token-preview-' + tokenId);
    const complete = document.getElementById('token-complete-' + tokenId);
    const button = preview.nextElementSibling;
    const toggleText = button.querySelector('.toggle-text');
    
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
</script>

<?php include 'includes/footer.php'; ?>