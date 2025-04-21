<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Sucursal</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f7;
            margin: 0;
            padding: 30px;
            color: #2c3e50;
        }

        h1 {
            text-align: center;
            color: #34495e;
            margin-bottom: 30px;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .checkbox-group {
            margin-bottom: 20px;
        }

        .checkbox-group input {
            margin-right: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #219150;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Editar Sucursal</h1>

    <form action="index.php?action=actualizar_sucursal" method="POST">
        <input type="hidden" name="id" value="<?= $sucursal['id'] ?>">

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?= $sucursal['nombre'] ?>" required>

        <label for="ubicacion">Ubicación:</label>
        <input type="text" id="ubicacion" name="ubicacion" value="<?= $sucursal['ubicacion'] ?>" required>

        <label for="bombas">Número de Bombas:</label>
        <input type="number" id="bombas" name="bombas" value="<?= $sucursal['bombas'] ?>" required>

        <label>Combustibles:</label>
        <div class="checkbox-group">
            <?php foreach ($combustibles as $combustible): ?>
                <input type="checkbox" name="combustibles[]" id="combustible_<?= $combustible['id'] ?>" 
                       value="<?= $combustible['id'] ?>"
                       <?= in_array($combustible['id'], $combustibles_seleccionados) ? 'checked' : '' ?>>
                <label for="combustible_<?= $combustible['id'] ?>"><?= $combustible['tipo'] ?></label><br>
            <?php endforeach; ?>
        </div>

        <button type="submit">Actualizar</button>
    </form>

    <a class="back-link" href="index.php?action=sucursales">Volver</a>

</body>
</html>
