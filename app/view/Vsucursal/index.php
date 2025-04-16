<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sucursales</title>
</head>
<body>

    <h1>Lista de Sucursales</h1>
    <a href="index.php?action=crear_sucursal">Agregar Nueva Sucursal</a>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Bombas</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sucursales as $sucursal): ?>
                <tr>
                    <td><?= $sucursal['id'] ?></td>
                    <td><?= $sucursal['nombre'] ?></td>
                    <td><?= $sucursal['ubicacion'] ?></td>
                    <td><?= $sucursal['bombas'] ?></td>
                    <td>
                        <a href="index.php?action=editar_sucursal&id=<?= $sucursal['id'] ?>">Editar</a> |
                        <a href="index.php?action=eliminar_sucursal&id=<?= $sucursal['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar esta sucursal?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
