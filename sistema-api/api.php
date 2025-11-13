<?php
// Archivo principal de la API pública
require_once 'config/api_config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Pública - Sistema de Mototaxis Huanta</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #1e3c72;
            --secondary-blue: #2a5298;
            --accent-blue: #0f3a4a;
            --light-blue: #e3f2fd;
            --dark-blue: #0d1b2a;
            --success-green: #198754;
            --warning-orange: #fd7e14;
            --light-gray: #f8f9fa;
            --border-gray: #dee2e6;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--light-gray);
            color: #333;
            line-height: 1.6;
        }

        .navbar-public {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--accent-blue);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .card {
            border: 1px solid var(--border-gray);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
        }

        .card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0 !important;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .card-header h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
        }

        .btn-success {
            background: var(--success-green);
            border: none;
        }

        .btn-success:hover {
            background: #157347;
            transform: translateY(-1px);
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid var(--border-gray);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.15);
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--light-blue);
            border: none;
            font-weight: 600;
            color: var(--primary-blue);
            padding: 0.75rem;
        }

        .table td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .badge {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border-left: 4px solid var(--success-green);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #664d03;
            border-left: 4px solid var(--warning-orange);
        }

        .accordion-button {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            font-weight: 500;
            border-radius: 8px;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--light-blue);
            color: var(--primary-blue);
        }

        .text-primary {
            color: var(--primary-blue) !important;
        }

        .border-bottom {
            border-bottom: 2px solid var(--light-blue) !important;
        }

        .spinner-border {
            color: var(--primary-blue);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.65rem 1.25rem;
            }
            
            .container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar Pública -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-public">
        <div class="container">
            <a class="navbar-brand" href="api.php">
                <i class="fas fa-motorcycle me-2"></i>
                Mototaxis Huanta - API Pública
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPublic">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarPublic">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../sistema-principal/login.php" target="_blank">
                            <i class="fas fa-cog me-1"></i>Panel de Administración
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Card de Autenticación -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-key me-2"></i>Autenticación API</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="apiToken" class="form-label">Token de Acceso</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="apiToken" 
                                       placeholder="Ingrese su token de acceso API"
                                       value="">
                                <button class="btn btn-outline-secondary" type="button" id="toggleToken">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                El token debe tener el formato: <code>xxxxxxxx-MOT-n</code>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100" id="validateToken">
                                <i class="fas fa-check-circle me-2"></i>Validar Token
                            </button>
                            <button class="btn btn-info" id="debugStructure" title="Debug Estructura BD">
                                <i class="fas fa-database"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Card de Búsqueda (inicialmente oculta) -->
                <div class="card mb-4 d-none" id="searchCard">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-search me-2"></i>Buscar Mototaxi</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="numeroAsignado" class="form-label">Número de búsqueda</label>
                            <input type="text" class="form-control" id="numeroAsignado" 
                                   placeholder="Ej: MT-001, A-123, DNI, etc.">
                            <div class="form-text">
                                Puede buscar por: número asignado, placa, DNI, código, etc.
                            </div>
                        </div>
                        <button class="btn btn-success w-100" id="searchMototaxi">
                            <i class="fas fa-motorcycle me-2"></i>Buscar Mototaxi
                        </button>
                    </div>
                </div>

                <!-- Resultados (inicialmente oculta) -->
                <div class="card d-none" id="resultsCard">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Mototaxi</h4>
                        <button class="btn btn-sm btn-outline-secondary" id="clearSearch">
                            <i class="fas fa-redo me-1"></i>Nueva Búsqueda
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="loading" class="text-center d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2 text-muted">Buscando información del mototaxi...</p>
                        </div>
                        <div id="resultsContent"></div>
                        
                        <!-- Acordeón para JSON -->
                        <div class="mt-4 d-none" id="jsonSection">
                            <div class="accordion" id="jsonAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#jsonCollapse">
                                            <i class="fas fa-code me-2"></i>Ver Respuesta JSON Completa
                                        </button>
                                    </h2>
                                    <div id="jsonCollapse" class="accordion-collapse collapse" 
                                         data-bs-parent="#jsonAccordion">
                                        <div class="accordion-body p-0">
                                            <pre id="jsonResponse" class="bg-dark text-light p-3 mb-0 rounded-bottom" 
                                                 style="font-size: 0.8rem; max-height: 300px; overflow-y: auto;"></pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="text-light mb-2">
                        <i class="fas fa-code me-2"></i>
                        API Pública - Sistema de Mototaxis Huanta
                    </h5>
                    <p class="mb-0 text-light opacity-75">
                        Interfaz para consultas de mototaxis mediante API REST
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1 text-light opacity-75">
                        &copy; 2025 VegetA CoudinG
                    </p>
                    <p class="mb-0 text-light opacity-75">
                        Todos los derechos reservados
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const apiTokenInput = document.getElementById('apiToken');
        const toggleTokenBtn = document.getElementById('toggleToken');
        const validateTokenBtn = document.getElementById('validateToken');
        const debugStructureBtn = document.getElementById('debugStructure');
        const searchCard = document.getElementById('searchCard');
        const resultsCard = document.getElementById('resultsCard');
        const numeroAsignadoInput = document.getElementById('numeroAsignado');
        const searchMototaxiBtn = document.getElementById('searchMototaxi');
        const clearSearchBtn = document.getElementById('clearSearch');
        const loadingElement = document.getElementById('loading');
        const resultsContent = document.getElementById('resultsContent');
        const jsonSection = document.getElementById('jsonSection');
        const jsonResponse = document.getElementById('jsonResponse');

        // Toggle visibilidad del token
        toggleTokenBtn.addEventListener('click', function() {
            const type = apiTokenInput.getAttribute('type') === 'password' ? 'text' : 'password';
            apiTokenInput.setAttribute('type', type);
            toggleTokenBtn.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });

        // Validar token
        validateTokenBtn.addEventListener('click', function() {
            const token = apiTokenInput.value.trim();
            
            if (!token) {
                showAlert('Por favor ingrese un token', 'error');
                return;
            }

            validateTokenBtn.disabled = true;
            validateTokenBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validando...';

            fetch('api/verify_token.php', {
                method: 'GET',
                headers: {
                    'X-API-Token': token
                }
            })
            .then(response => {
                if (response.status === 403) {
                    throw new Error('Token inválido o desactivado');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert('✅ ' + data.message, 'success');
                    searchCard.classList.remove('d-none');
                    numeroAsignadoInput.focus();
                    localStorage.setItem('apiToken', token);
                } else {
                    showAlert('❌ ' + data.message, 'error');
                    searchCard.classList.add('d-none');
                    resultsCard.classList.add('d-none');
                }
            })
            .catch(error => {
                showAlert('❌ Error: ' + error.message, 'error');
                searchCard.classList.add('d-none');
                resultsCard.classList.add('d-none');
            })
            .finally(() => {
                validateTokenBtn.disabled = false;
                validateTokenBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Validar Token';
            });
        });

        // Debug estructura BD
        debugStructureBtn.addEventListener('click', function() {
            const token = apiTokenInput.value.trim();
            
            if (!token) {
                showAlert('Primero ingrese un token válido', 'error');
                return;
            }

            debugStructureBtn.disabled = true;
            debugStructureBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch('api/debug_estructura.php', {
                method: 'GET',
                headers: {
                    'X-API-Token': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Estructura BD:', data);
                if (data.success) {
                    jsonResponse.textContent = JSON.stringify(data, null, 2);
                    jsonSection.classList.remove('d-none');
                    // Expandir el acordeón
                    new bootstrap.Collapse(document.getElementById('jsonCollapse')).show();
                    showAlert('✅ Estructura de BD obtenida (ver consola para detalles)', 'success');
                } else {
                    showAlert('❌ Error obteniendo estructura: ' + (data.error || 'Desconocido'), 'error');
                }
            })
            .catch(error => {
                showAlert('❌ Error: ' + error.message, 'error');
            })
            .finally(() => {
                debugStructureBtn.disabled = false;
                debugStructureBtn.innerHTML = '<i class="fas fa-database"></i>';
            });
        });

        // Buscar mototaxi
        searchMototaxiBtn.addEventListener('click', function() {
            const token = apiTokenInput.value.trim();
            const numero = numeroAsignadoInput.value.trim();
            
            if (!token) {
                showAlert('Token no válido', 'error');
                return;
            }
            
            if (!numero) {
                showAlert('Por favor ingrese un término de búsqueda', 'error');
                return;
            }

            searchMototaxi(token, numero);
        });

        // Limpiar búsqueda
        clearSearchBtn.addEventListener('click', function() {
            resultsCard.classList.add('d-none');
            jsonSection.classList.add('d-none');
            numeroAsignadoInput.value = '';
            numeroAsignadoInput.focus();
        });

        // Función para buscar mototaxi
        function searchMototaxi(token, numero) {
            searchMototaxiBtn.disabled = true;
            searchMototaxiBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Buscando...';
            loadingElement.classList.remove('d-none');
            resultsCard.classList.remove('d-none');
            resultsContent.innerHTML = '';
            jsonSection.classList.add('d-none');

            fetch(`api/buscar.php?numero=${encodeURIComponent(numero)}`, {
                method: 'GET',
                headers: {
                    'X-API-Token': token
                }
            })
            .then(response => {
                if (response.status === 403) {
                    throw new Error('Token inválido o desactivado');
                }
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingElement.classList.add('d-none');
                
                if (data.success) {
                    displayMototaxiInfo(data.data);
                    jsonResponse.textContent = JSON.stringify(data, null, 2);
                    jsonSection.classList.remove('d-none');
                } else {
                    resultsContent.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${data.message || 'No se encontraron resultados'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                loadingElement.classList.add('d-none');
                resultsContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error de búsqueda: ${error.message}
                    </div>
                `;
            })
            .finally(() => {
                searchMototaxiBtn.disabled = false;
                searchMototaxiBtn.innerHTML = '<i class="fas fa-motorcycle me-2"></i>Buscar Mototaxi';
            });
        }

        // Mostrar información del mototaxi
        function displayMototaxiInfo(mototaxi) {
            // Verificar si mototaxi es un array (múltiples resultados)
            const datos = Array.isArray(mototaxi) ? mototaxi[0] : mototaxi;
            
            // Verificar que la empresa existe
            const empresa = datos.empresa || {
                razon_social: 'Mototaxis Huanta',
                ruc: '20123456789',
                direccion: 'Huanta, Ayacucho, Perú'
            };

            const infoHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-user me-2"></i>Información Personal
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Número Asignado:</th>
                                <td><strong class="text-success">${datos.numero_asignado || 'N/A'}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nombre Completo:</th>
                                <td>${datos.nombre_completo || 'No disponible'}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">DNI:</th>
                                <td><span class="badge bg-info">${datos.dni || 'No disponible'}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Dirección:</th>
                                <td>${datos.direccion || 'No especificado'}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-motorcycle me-2"></i>Información del Vehículo
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Placa de Rodaje:</th>
                                <td><span class="badge bg-secondary">${datos.placa_rodaje || 'No disponible'}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Año Fabricación:</th>
                                <td>${datos.anio_fabricacion || 'No especificado'}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Marca:</th>
                                <td>${datos.marca || 'No especificado'}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Color:</th>
                                <td>
                                    <span class="badge" style="background-color: ${getColorValue(datos.color)}; color: white;">
                                        ${datos.color || 'No especificado'}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-cogs me-2"></i>Especificaciones Técnicas
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Número Motor:</th>
                                <td>${datos.numero_motor || 'No especificado'}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tipo Motor:</th>
                                <td>${datos.tipo_motor || 'No especificado'}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Serie:</th>
                                <td>${datos.serie || 'No especificado'}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="fas fa-building me-2"></i>Información Adicional
                        </h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th class="text-muted" style="width: 40%;">Fecha Registro:</th>
                                <td><span class="badge bg-dark">${datos.fecha_registro || 'No especificado'}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Empresa:</th>
                                <td><strong class="text-primary">${empresa.razon_social}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">RUC Empresa:</th>
                                <td>${empresa.ruc}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Estado:</th>
                                <td><span class="badge bg-success">${datos.estado_registro || 'ACTIVO'}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                ${Array.isArray(mototaxi) && mototaxi.length > 1 ? `
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Se encontraron ${mototaxi.length} resultados. Mostrando el primero.
                </div>
                ` : ''}
            `;
            
            resultsContent.innerHTML = infoHtml;
        }

        // Función auxiliar para colores
        function getColorValue(color) {
            if (!color || color === 'No especificado') return '#6c757d';
            const colors = {
                'rojo': '#dc3545', 'azul': '#0d6efd', 'verde': '#198754',
                'amarillo': '#ffc107', 'negro': '#212529', 'blanco': '#f8f9fa',
                'gris': '#6c757d', 'naranja': '#fd7e14', 'morado': '#6f42c1'
            };
            return colors[color.toLowerCase()] || '#6c757d';
        }

        // Mostrar alertas
        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            document.querySelector('.container').insertAdjacentHTML('afterbegin', alertHtml);
            
            setTimeout(() => {
                const alert = document.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Cargar token guardado si existe
        const savedToken = localStorage.getItem('apiToken');
        if (savedToken) {
            apiTokenInput.value = savedToken;
            setTimeout(() => validateTokenBtn.click(), 1000);
        }

        // Permitir búsqueda con Enter
        numeroAsignadoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchMototaxiBtn.click();
            }
        });

        apiTokenInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                validateTokenBtn.click();
            }
        });
    });
    </script>
</body>
</html>