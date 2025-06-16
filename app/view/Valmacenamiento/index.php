<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Almacenamiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .actions a {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .actions a:hover {
            background-color: #2980b9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .sucursales-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .sucursal-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .sucursal-header {
            background: #34495e;
            color: white;
            padding: 15px;
        }

        .sucursal-header h3 {
            margin: 0 0 5px 0;
        }

        .sucursal-body {
            padding: 15px;
        }

        .tanque-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .tanque-item:last-child {
            border-bottom: none;
        }

        .estado-activo {
            color: #27ae60;
            font-weight: bold;
        }

        .estado-inactivo {
            color: #e74c3c;
            font-weight: bold;
        }

        .btn-gestionar {
            display: block;
            width: 100%;
            padding: 10px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            margin-top: 15px;
        }

        .btn-gestionar:hover {
            background: #229954;
        }

        .no-tanques {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <h1>🏭 Dashboard de Almacenamiento</h1>

    <div class="actions">
        <a href="index.php">← Menú Principal</a>
        <a href="index.php?action=estimacion_cola">Ver Estimaciones</a>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $estadisticas['total_tanques'] ?? 0 ?></div>
            <div class="stat-label">Total Tanques</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $estadisticas['tanques_activos'] ?? 0 ?></div>
            <div class="stat-label">Tanques Activos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $estadisticas['tanques_inactivos'] ?? 0 ?></div>
            <div class="stat-label">Tanques Inactivos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= number_format($estadisticas['capacidad_total'] ?? 0, 0) ?>L</div>
            <div class="stat-label">Capacidad Total</div>
        </div>
    </div>

    <!-- Lista de Sucursales -->
    <div class="sucursales-grid">
        <?php if (!empty($sucursales)): ?>
            <?php foreach ($sucursales as $sucursal): ?>
                <div class="sucursal-card">
                    <div class="sucursal-header">
                        <h3><?= htmlspecialchars($sucursal['nombre']) ?></h3>
                        <p><?= htmlspecialchars($sucursal['ubicacion']) ?></p>
                        <small><?= $sucursal['bombas'] ?> bombas</small>
                    </div>
                    <div class="sucursal-body">
                        <?php if (!empty($sucursal['almacenamientos'])): ?>
                            <?php foreach ($sucursal['almacenamientos'] as $almacenamiento): ?>
                                <div class="tanque-item">
                                    <div>
                                        <strong><?= htmlspecialchars($almacenamiento['combustible_tipo']) ?></strong><br>
                                        <small><?= number_format($almacenamiento['cap_actual'], 0) ?> L</small>
                                    </div>
                                    <div class="<?= $almacenamiento['estado'] === 'activo' ? 'estado-activo' : 'estado-inactivo' ?>">
                                        <?= ucfirst($almacenamiento['estado']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-tanques">
                                No hay tanques configurados
                            </div>
                        <?php endif; ?>
                        
                        <a href="index.php?action=gestionar_almacenamiento&id=<?= $sucursal['id'] ?>" class="btn-gestionar">
                            Gestionar Almacenamiento
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-tanques" style="grid-column: 1/-1;">
                <h3>No hay sucursales disponibles</h3>
                <p>Primero debe crear sucursales y asignar combustibles.</p>
                <a href="index.php?action=sucursales" class="actions a">Ir a Sucursales</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>