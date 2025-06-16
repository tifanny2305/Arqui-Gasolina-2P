<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Almacenamiento - <?= htmlspecialchars($datos_vista['sucursal']['nombre']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            color: #495057;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-item strong {
            display: block;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .info-item span {
            font-size: 1.1rem;
            color: #212529;
        }

        .table-responsive {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-primary:hover { background: #5a6fd8; }
        .btn-success:hover { background: #218838; }
        .btn-danger:hover { background: #c82333; }

        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }

        .combustible-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #e9ecef;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .capacity-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .capacity-bar {
            width: 60px;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .capacity-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }

        .actions-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .form-actions {
            background: #f8f9fa;
            padding: 1.5rem;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-group {
                flex-direction: column;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.75rem 0.5rem;
            }

            .form-actions {
                flex-direction: column;
            }
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .input-addon {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚙️ Gestionar Almacenamiento</h1>
        <p>🏪 <?= htmlspecialchars($datos_vista['sucursal']['nombre']) ?> - <?= htmlspecialchars($datos_vista['sucursal']['ubicacion']) ?></p>
    </div>

    <div class="container">
        <?php
        // Mostrar mensajes de sesión
        if (isset($_SESSION['success'])):
        ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ❌ <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning">
                ⚠️ <?= htmlspecialchars($_SESSION['warning']) ?>
            </div>
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>

        <!-- Información de la Sucursal -->
        <div class="card">
            <div class="card-header">
                <h2>📋 Información de la Sucursal</h2>
                <div class="actions-group">
                    <a href="index.php?action=mostrar_cola&id=<?= $datos_vista['sucursal']['id'] ?>" class="btn btn-success">
                        📊 Ver Estimaciones
                    </a>
                    <a href="index.php?action=almacenamiento" class="btn btn-secondary">
                        ← Volver al Dashboard
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>📍 Ubicación</strong>
                        <span><?= htmlspecialchars($datos_vista['sucursal']['ubicacion']) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>⛽ Bombas</strong>
                        <span><?= $datos_vista['sucursal']['bombas'] ?> unidades</span>
                    </div>
                    <div class="info-item">
                        <strong>🛢️ Tanques Configurados</strong>
                        <span><?= count($datos_vista['almacenamientos']) ?> tipos de combustible</span>
                    </div>
                    <div class="info-item">
                        <strong>✅ Tanques Activos</strong>
                        <span><?= count(array_filter($datos_vista['almacenamientos'], function($a) { return $a['estado'] === 'activo'; })) ?> de <?= count($datos_vista['almacenamientos']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Almacenamientos -->
        <?php if (!empty($datos_vista['almacenamientos'])): ?>
            <div class="card">
                <div class="card-header">
                    <h2>🛢️ Gestión de Almacenamientos</h2>
                    <span class="badge badge-success">
                        <?= count($datos_vista['almacenamientos']) ?> tanques configurados
                    </span>
                </div>
                <div class="card-body">
                    <form action="index.php?action=actualizar_almacenamiento" method="POST" id="almacenamientoForm">
                        <input type="hidden" name="sucursal_id" value="<?= $datos_vista['sucursal']['id'] ?>">
                        
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>🛢️ Tipo de Combustible</th>
                                        <th>📊 Capacidad Actual</th>
                                        <th>📋 Estado</th>
                                        <th>📅 Última Actualización</th>
                                        <th>📈 Progreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($datos_vista['almacenamientos'] as $almacenamiento): ?>
                                        <tr data-id="<?= $almacenamiento['id'] ?>">
                                            <td>
                                                <div class="combustible-tag">
                                                    <?= htmlspecialchars($almacenamiento['combustible_tipo']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="almacenamientos[<?= $almacenamiento['id'] ?>][cap_actual]" 
                                                           value="<?= $almacenamiento['cap_actual'] ?>" 
                                                           min="0" 
                                                           step="0.01" 
                                                           class="form-control"
                                                           style="width: 120px;"
                                                           onchange="updateCapacityDisplay(this, <?= $almacenamiento['id'] ?>)">
                                                    <span class="input-addon">Litros</span>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="almacenamientos[<?= $almacenamiento['id'] ?>][estado]" 
                                                        class="form-control" 
                                                        style="width: 130px;"
                                                        onchange="updateEstadoBadge(this, <?= $almacenamiento['id'] ?>)">
                                                    <option value="activo" <?= $almacenamiento['estado'] === 'activo' ? 'selected' : '' ?>>✅ Activo</option>
                                                    <option value="inactivo" <?= $almacenamiento['estado'] === 'inactivo' ? 'selected' : '' ?>>❌ Inactivo</option>
                                                </select>
                                            </td>
                                            <td>
                                                <small>
                                                    <?= $almacenamiento['fecha'] ? date('d/m/Y H:i', strtotime($almacenamiento['fecha'])) : 'No disponible' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="capacity-display">
                                                    <span class="capacity-text" id="capacity-<?= $almacenamiento['id'] ?>">
                                                        <?= number_format($almacenamiento['cap_actual'], 0) ?>L
                                                    </span>
                                                    <div class="capacity-bar">
                                                        <div class="capacity-fill" 
                                                             id="progress-<?= $almacenamiento['id'] ?>"
                                                             style="width: <?= min(100, ($almacenamiento['cap_actual'] / max(1000, $almacenamiento['cap_actual'])) * 100) ?>%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        💾 Guardar Cambios
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        🔄 Restablecer
                    </button>
                    <a href="index.php?action=detalle_almacenamiento&sucursal_id=<?= $datos_vista['sucursal']['id'] ?>" class="btn btn-primary">
                        📊 Ver Detalles Completos
                    </a>
                </div>
                </form>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="icon">🛢️</div>
                        <h3>No hay almacenamientos configurados</h3>
                        <p>Esta sucursal no tiene ningún tipo de combustible asignado.</p>
                        <p>Para configurar almacenamientos, primero debe asignar combustibles a la sucursal.</p>
                        <div style="margin-top: 1.5rem;">
                            <a href="index.php?action=gestionar_tanques&id=<?= $datos_vista['sucursal']['id'] ?>" class="btn btn-primary">
                                ➕ Gestionar Tanques
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Actualizar display de capacidad
        function updateCapacityDisplay(input, almacenamientoId) {
            const capacidad = parseFloat(input.value) || 0;
            const capacityText = document.getElementById('capacity-' + almacenamientoId);
            const progressBar = document.getElementById('progress-' + almacenamientoId);
            
            if (capacityText) {
                capacityText.textContent = capacidad.toLocaleString() + 'L';
            }
            
            if (progressBar) {
                // Calcular porcentaje basado en una capacidad máxima estimada
                const maxCapacity = Math.max(1000, capacidad * 1.2);
                const percentage = Math.min(100, (capacidad / maxCapacity) * 100);
                progressBar.style.width = percentage + '%';
                
                // Cambiar color según la capacidad
                if (capacidad > 500) {
                    progressBar.style.background = 'linear-gradient(90deg, #28a745, #20c997)';
                } else if (capacidad > 100) {
                    progressBar.style.background = 'linear-gradient(90deg, #ffc107, #fd7e14)';
                } else {
                    progressBar.style.background = 'linear-gradient(90deg, #dc3545, #e83e8c)';
                }
            }
        }

        // Actualizar badge de estado
        function updateEstadoBadge(select, almacenamientoId) {
            const row = select.closest('tr');
            const estado = select.value;
            
            // Agregar clase visual al row
            if (estado === 'activo') {
                row.style.backgroundColor = '#d4edda';
            } else {
                row.style.backgroundColor = '#f8d7da';
            }
            
            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 1000);
        }

        // Validación del formulario
        document.getElementById('almacenamientoForm').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[type="number"]');
            let hasError = false;
            
            inputs.forEach(input => {
                const value = parseFloat(input.value);
                if (isNaN(value) || value < 0) {
                    input.style.borderColor = '#dc3545';
                    hasError = true;
                } else {
                    input.style.borderColor = '#ced4da';
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Por favor, revise los valores de capacidad. Deben ser números positivos.');
            }
        });

        // Auto-guardar cada 30 segundos (opcional)
        let autoSaveInterval;
        
        function enableAutoSave() {
            autoSaveInterval = setInterval(() => {
                const form = document.getElementById('almacenamientoForm');
                const formData = new FormData(form);
                
                // Solo auto-guardar si hay cambios
                if (hasFormChanges()) {
                    console.log('Auto-guardando cambios...');
                    // Aquí podrías implementar AJAX para auto-guardar
                }
            }, 30000);
        }
        
        function hasFormChanges() {
            // Implementar lógica para detectar cambios
            return false;
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar displays de capacidad
            document.querySelectorAll('input[name*="[cap_actual]"]').forEach(input => {
                const almacenamientoId = input.name.match(/\[(\d+)\]/)[1];
                updateCapacityDisplay(input, almacenamientoId);
            });
            
            // Habilitar auto-guardado (opcional)
            // enableAutoSave();
        });
    </script>
</body>
</html>