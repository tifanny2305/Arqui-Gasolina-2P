<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Combustibles</title>
    <style>
        <?php include 'estilos_base.css'; // o copia el mismo CSS que ya usas ?>
    </style>
</head>
<body>

    <h1>Lista de Combustibles</h1>

    <div class="actions">
        <a href="index.php">Volver al Menú Principal</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($combustibles as $combustible): ?>
                <tr>
                    <td><?= $combustible['id'] ?></td>
                    <td><?= htmlspecialchars($combustible['tipo']) ?></td>
                    <td class="table-actions">
                        <a href="index.php?action=editar_parametros&id=<?= $combustible['id'] ?>">Editar Parámetros</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
