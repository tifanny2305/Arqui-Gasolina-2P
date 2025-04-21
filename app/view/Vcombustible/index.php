<!-- view/Vcombustible/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tipos de Combustible</title>
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

        .btn-action {
            text-decoration: none;
            padding: 6px 12px;
            margin-right: 5px;
            border-radius: 4px;
            color: white;
        }

        .btn-edit {
            background-color: #27ae60;
        }

        .btn-edit:hover {
            background-color: #1e8449;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Listado de Tipos de Combustible</h1>

    <div class="actions">
        <a href="index.php?action=crear_combustible">Agregar Combustible</a>
        <a href="index.php">Volver al Menú Principal</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($tipo = $tipos->fetch_assoc()): ?>
                <tr>
                    <td><?= $tipo['id']; ?></td>
                    <td><?= $tipo['tipo']; ?></td>
                    <td>
                        <a class="btn-action btn-edit" href="index.php?action=editar_combustible&id=<?= $tipo['id']; ?>">Editar</a>
                        <a class="btn-action btn-delete" href="index.php?action=eliminar_combustible&id=<?= $tipo['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este tipo de combustible?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

