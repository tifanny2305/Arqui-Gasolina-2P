<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Almacenamiento - <?= htmlspecialchars($sucursal['nombre']) ?></title>
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
            margin-bottom: 20px;
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

        .info-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .info-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .info-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-label {
            color: #666;
            font-size: 0.9em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-submit {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 15px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #229954;
        }

        .estado-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .estado-activo {
            background: #d4edda;
            color: #155724;
        }

        .estado-inactivo {
            background: #f8d7da;
            color: #721c24;
        }

        .no-almacenamientos {
            text-align: center;
            padding: 50px;
            color: #666;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h1>⚙️ Gestionar Almacenamiento</h1>
    <p style="text-align: center; color: #666;">
        <strong><?= htmlspecialchars($sucursal['nombre']) ?></strong> - <?= htmlspecialchars($sucursal['ubicacion']) ?>
    </p>

    <div class="actions">
        <a href="index.php?action=almacenamiento">← Volver al Dashboard</a>
        <a href="index.php?action=mostrar_cola&id=<?= $sucursal['id'] ?>">Ver Estimaciones</a>
    </div>

    <!-- Información de la Sucursal -->
    <div class="info-card">
        <h3>📋 Información de la Sucursal</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-value"><?= $sucursal['bombas'] ?></div>
                <div class="info-label">Bombas</div>
            </div>
            <div class="info-item">
                <div class="info-value"><?= count($almacenamientos) ?></div>
                <div class="info-label">Tanques Configurados</div>
            </div>
            <div class="info-item">
                <div class="info-value"><?= count(array_filter($almacenamientos, function($a) { return $a['estado'] === 'activo'; })) ?></div>
                <div class="info-label">Tanques Activos</div>
            </div>
            <div class="info-item">
                <div class="info-value"><?= number_format(array_sum(array_column($almacenamientos, 'cap_actual')), 0) ?>L</div>
                <div class="info-label">Capacidad Total</div>
            </div>
        </div>
    </div>

    <!-- Gestión de Almacenamientos -->
    <?php if (!empty($almacenamientos)): ?>
        <form action="index.php?action=actualizar_almacenamiento" method="POST">
            <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">
            
            <table>
                <thead>
                    <tr>
                        <th>🛢️ Tipo de Combustible</th>
                        <th>📊 Capacidad Actual (Litros)</th>
                        <th>📋 Estado</th>
                        <th>📅 Última Actualización</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($almacenamientos as $almacenamiento): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($almacenamiento['combustible_tipo']) ?></strong>
                            </td>
                            <td>
                                <input type="number" 
                                       name="almacenamientos[<?= $almacenamiento['id'] ?>][cap_actual]" 
                                       value="<?= $almacenamiento['cap_actual'] ?>" 
                                       min="0" 
                                       step="0.01" 
                                       class="form-control"
                                       style="width: 150px;">
                            </td>
                            <td>
                                <select name="almacenamientos[<?= $almacenamiento['id'] ?>][estado]" class="form-control" style="width: 120px;">
                                    <option value="activo" <?= $almacenamiento['estado'] === 'activo' ? 'selected' : '' ?>>✅ Activo</option>
                                    <option value="inactivo" <?= $almacenamiento['estado'] === 'inactivo' ? 'selected' : '' ?>>❌ Inactivo</option>
                                </select>
                            </td>
                            <td>
                                <small><?= $almacenamiento['fecha'] ? date('d/m/Y H:i', strtotime($almacenamiento['fecha'])) : 'No disponible' ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn-submit">💾 Guardar Cambios</button>
        </form>
    <?php else: ?>
        <div class="no-almacenamientos">
            <h3>🛢️ No hay almacenamientos configurados</h3>
            <p>Esta sucursal no tiene ningún tipo de combustible asignado.</p>
            <p>Para configurar almacenamientos, primero debe asignar combustibles a la sucursal.</p>
            <a href="index.php?action=gestionar_tanques&id=<?= $sucursal['id'] ?>" class="actions a" style="margin-top: 20px;">
                ➕ Gestionar Tanques
            </a>
        </div>
    <?php endif; ?>
</body>
</html>