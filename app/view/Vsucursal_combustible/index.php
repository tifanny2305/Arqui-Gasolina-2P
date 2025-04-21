<!-- filepath: c:\xampp\htdocs\gasolinera\views\Vsucursal_combustible\index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Sucursales</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 30px;
            color: #2c3e50;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .volver-link {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e1e1e1;
        }

        th {
            background-color: #2ecc71;
            color: white;
        }

        td a {
            padding: 6px 10px;
            background-color: #3498db;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        td a:hover {
            background-color: #2980b9;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            table, th, td {
                font-size: 14px;
            }

            td a {
                display: inline-block;
                padding: 8px 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h1>Listado de Sucursales-Tanque</h1>

    <a class="volver-link" href="index.php">← Volver al Menú Principal</a>

    <table>
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
