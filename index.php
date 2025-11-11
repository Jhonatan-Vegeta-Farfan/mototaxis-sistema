<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: sistema-principal/dashboard.php');
    exit();
}

require_once 'sistema-principal/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    
    if (!empty($nombre) && !empty($contrasena)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            // En un sistema real, deberías usar password_verify()
            if ($contrasena === $usuario['contrasena']) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                header('Location: sistema-principal/dashboard.php');
                exit();
            } else {
                $error = 'Contraseña incorrecta';
            }
        } else {
            $error = 'Usuario no encontrado';
        }
    } else {
        $error = 'Por favor, complete todos los campos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de MotoTaxis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="sistema-principal/css/style.css">
    <style>
        .login-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .system-choice {
            text-align: center;
            margin-bottom: 2rem;
        }
        .system-btn {
            margin: 0 10px;
            padding: 10px 20px;
        }
    </style>
</head>
<body class="login-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Sistema de MotoTaxis Huanta</h4>
                        <p class="mb-0">Iniciar Sesión</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="system-choice">
                            <h6 class="text-muted mb-3">Seleccione el sistema a utilizar:</h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active system-btn" data-system="principal">
                                    Sistema Principal

                            </div>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar al Sistema Principal</button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                ¿Necesita acceder a la API? 
                                <a href="sistema-api/api.php" target="_blank" class="text-decoration-none">Haga clic aquí</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const systemBtns = document.querySelectorAll('.system-btn');
            
            systemBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    systemBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    if (this.dataset.system === 'principal') {
                        document.querySelector('button[type="submit"]').textContent = 'Ingresar al Sistema Principal';
                    }
                });
            });
        });
    </script>
</body>
</html>