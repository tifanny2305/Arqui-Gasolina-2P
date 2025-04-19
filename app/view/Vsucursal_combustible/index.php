<!-- filepath: c:\xampp\htdocs\gasolinera\views\Vsucursal_combustible\index.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Sucursales</title>
    
</head>
<body>
    <h1>Listado de Sucursales</h1>

    <a href="index.php">Volver al Menú Principal</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sucursales as $sucursal): ?>
                <tr>
                    <td><?= $sucursal['id'] ?></td>
                    <td><?= $sucursal['nombre'] ?></td>
                    <td>
                        <a href="index.php?action=gestionar_tanques&id=<?= $sucursal['id'] ?>">Tanques</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>