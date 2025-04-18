<!-- view/Vcombustible/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tipos de Combustible</title>
</head>
<body>
    <h1>Listado de Tipos de Combustible</h1>
    <a href="index.php?action=crear_combustible">Agregar Combustible</a>
    </p>
    <a href="index.php">Volver al Menú Principal</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Litros</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tipo = $tipos->fetch_assoc()): ?>
                <tr>
                    <td><?= $tipo['id']; ?></td>
                    <td><?= $tipo['tipo']; ?></td>
                    <td><?= $tipo['litros']; ?></td>
                    <td>
                        <a href="index.php?action=editar_combustible&id=<?= $tipo['id']; ?>">Editar</a> 
                        <a href="index.php?action=eliminar_combustible&id=<?= $tipo['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este tipo de combustible?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
