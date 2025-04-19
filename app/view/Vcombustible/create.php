<!-- view/Vcombustible/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Tipo de Combustible</title>
</head>
<body>
    <h1>Crear Nuevo Tipo de Combustible</h1>

    <form action="index.php?action=registrar_combustible" method="POST">
        <label for="tipo">Tipo:</label>
        <input type="text" name="tipo" id="tipo" required><br>

        <button type="submit">Crear</button>
    </form>
</body>
</html>
