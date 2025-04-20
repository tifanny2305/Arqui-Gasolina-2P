<!-- filepath: c:\xampp\htdocs\gasolinera\views\Vsucursal_combustible\gestionar.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tanques</title>
</head>
<body>
    <h1>Gestionar Tanques de la Sucursal: <?= $sucursal['nombre'] ?></h1>
    <form action="index.php?action=actualizar_tanques" method="POST">
        <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">
        <table border="1">
            <thead>
                <tr>
                    <th>Combustible</th>
                    <th>Capacidad Actual</th>
                    <th>Fecha Actualizada</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($combustibles as $combustible): ?>
                    <tr>
                        <td><?= htmlspecialchars($combustible['tipo']) ?></td>
                        <td>
                            <input 
                                type="number" 
                                name="combustibles[<?= $combustible['id'] ?>][capacidad_actual]" 
                                value="<?= htmlspecialchars($combustible['capacidad_actual']) ?>" 
                                required>
                        </td>
                        <td>
                            <?= htmlspecialchars($combustible['fecha_actualizada']) ?>
                        </td>
                        <td>
                            <select name="combustibles[<?= $combustible['id'] ?>][estado]" required>
                                <option value="activo" <?= $combustible['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $combustible['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </td>
                        <td>
                            <form 
                                action="index.php?action=eliminar_combustible_sucursal&id=<?= urlencode($sucursal['id']) ?>" 
                                method="POST" 
                                onsubmit="return confirm('¿Estás seguro de eliminar este combustible?');">
                                <input type="hidden" name="sucursal_id" value="<?= htmlspecialchars($sucursal['id']) ?>">
                                <input type="hidden" name="combustible_id" value="<?= htmlspecialchars($combustible['id']) ?>">
                                <button type="submit" class="btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Actualizar Tanques</button>
    </form>
</body>
</html>