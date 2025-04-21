<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sucursales</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f7;
            margin: 0;
            padding: 20px;
            color: #333;
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
            text-decoration: none;
            background-color: #3498db;
            color: white;
            padding: 10px 16px;
            border-radius: 5px;
            margin: 0 5px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f0f3f5;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .table-actions a {
            color: #3498db;
            text-decoration: none;
            margin: 0 5px;
            font-weight: bold;
        }

        .table-actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Lista de Sucursales</h1>

    <div class="actions">
        <a href="index.php?action=crear_sucursal">Agregar Nueva Sucursal</a>
        <a href="index.php">Volver al Menú Principal</a>
    </div>

    <table>
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
                    <td class="table-actions">
                        <a href="index.php?action=editar_sucursal&id=<?= $sucursal['id'] ?>">Editar</a> |
                        <a href="index.php?action=eliminar_sucursal&id=<?= $sucursal['id'] ?>" onclick="return confirm('¿Estás seguro de eliminar esta sucursal?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
