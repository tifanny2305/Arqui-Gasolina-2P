<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimación para <?= htmlspecialchars($sucursal['nombre']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        td {
            background-color: #fafafa;
        }

        tr:hover td {
            background-color: #f1f1f1;
        }

        .btn-volver {
            display: inline-block;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .btn-volver:hover {
            background: #1e8449;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #888;
            padding: 20px;
        }
    </style>
</head>
<body>
    <h1>Estimación para <?= htmlspecialchars($sucursal['nombre']) ?></h1>
    
    <div class="card">
        <h2>Datos de Estimación</h2>
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
                            <td><?= htmlspecialchars($est['fecha_actualizada']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">No hay datos de estimación disponibles para esta sucursal.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="index.php?action=estimacion_cola" class="btn-volver">← Volver al listado</a>
</body>
</html>
