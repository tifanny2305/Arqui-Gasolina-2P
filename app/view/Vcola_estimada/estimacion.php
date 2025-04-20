<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimación para <?= htmlspecialchars($sucursal['nombre']) ?></title>
    <style>
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }
        .card h2 {
            color: #2c3e50;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn-volver {
            display: inline-block;
            padding: 10px 15px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn-volver:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <h1>Estimación para <?= htmlspecialchars($sucursal['nombre']) ?></h1>
    
    <table>
        <thead>
            <tr>
                <th>Combustible</th>
                <th>Capacidad Actual</th>
                <th>Autos Estimados</th>
                <th>Distancia Cola (m)</th>
                <th>Tiempo Agotamiento</th>
                <th>Actualizado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($datos_vista['estimaciones'])): ?>
                <?php foreach ($datos_vista['estimaciones'] as $est): ?>
                    <tr>
                        <td><?= htmlspecialchars($est['tipo_combustible']) ?></td>
                        <td><?= htmlspecialchars($est['capacidad_actual']) ?></td>
                        <td><?= htmlspecialchars($est['cant_autos']) ?></td>
                        <td><?= htmlspecialchars($est['distancia_cola']) ?></td>
                        <td><?= htmlspecialchars($est['tiempo_agotamiento']) ?></td>
                        <td><?= htmlspecialchars($est['fecha_actualizacion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay datos de estimación disponibles para esta sucursal.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <a href="index.php?action=estimacion_cola" class="btn-volver">← Volver al listado</a>tion=combustibles">Volver al listado</a>
</body>
</html>