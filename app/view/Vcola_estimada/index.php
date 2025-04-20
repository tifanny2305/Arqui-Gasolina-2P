<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar Sucursal</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .btn-estimar { 
            padding: 5px 10px; 
            background: #2196F3; 
            color: white; 
            text-decoration: none;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>Seleccione Sucursal para Estimación</h1>
    <a href="index.php">Volver al Menú Principal</a>
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
                    <a href="index.php?action=mostrar_cola&id=<?= $sucursal['id'] ?>" 
                       class="btn-estimar">
                       Estimar Cola
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>