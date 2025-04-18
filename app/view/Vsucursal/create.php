<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Sucursal</title>
</head>
<body>

    <h1>Registrar Nueva Sucursal</h1>

    <form action="index.php?action=registrar_sucursal" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="ubicacion">Ubicación:</label>
        <input type="text" id="ubicacion" name="ubicacion" required><br><br>

        <label for="bombas">Número de Bombas:</label>
        <input type="number" id="bombas" name="bombas" required><br><br>

        <button type="submit">Registrar</button>
    </form>

</body>
</html>
