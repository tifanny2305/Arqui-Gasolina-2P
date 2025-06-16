<!-- filepath: c:\xampp\htdocs\gasolinera\views\Vsucursal_combustible\gestionar.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Tanques</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 30px;
            color: #2c3e50;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2ecc71;
            color: white;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button, .btn-eliminar {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover, .btn-eliminar:hover {
            background-color: #c0392b;
        }

        form[action*="actualizar_tanques"] > button {
            background-color: #3498db;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        form[action*="actualizar_tanques"] > button:hover {
            background-color: #2980b9;
        }

        td form {
            margin: 0;
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 14px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <h1>Gestionar Tanques de la Sucursal: <?= htmlspecialchars($sucursal['nombre']) ?></h1>

    <a class="back-link" href="index.php?action=tanques">Volver</a></p>
    <form action="index.php?action=actualizar_tanques" method="POST">
        <input type="hidden" name="sucursal_id" value="<?= $sucursal['id'] ?>">

        <table>
            <thead>
                <tr>
                    <th>Combustible</th>
                    <th>Capacidad Actual</th>
                    <th>Fecha Actualizada</th>
                    <th>Estado</th>
                    <th>Acción</th>
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
                        <td><?= htmlspecialchars($combustible['fecha_actualizada']) ?></td>
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
