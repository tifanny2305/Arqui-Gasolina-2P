<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Sucursal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
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
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        thead {
            background-color: #2c3e50;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #eaf1f8;
        }

        .btn-estimar {
            padding: 5px 10px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }

        .btn-estimar:hover {
            background-color: #1976d2;
        }
    </style>
</head>
<body>
    <h1>Seleccione Sucursal para Estimación</h1>

    <div class="actions">
        <a href="index.php">Volver al Menú Principal</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Bombas</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sucursales as $sucursal): ?>
            <tr>
                <td><?= htmlspecialchars($sucursal['nombre']) ?></td>
                <td><?= htmlspecialchars($sucursal['ubicacion']) ?></td>
                <td><?= htmlspecialchars($sucursal['bombas']) ?></td>
                <td>
                    <a href="index.php?action=mostrar_cola&id=<?= $sucursal['id'] ?>" class="btn-estimar">Estimar Cola</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
