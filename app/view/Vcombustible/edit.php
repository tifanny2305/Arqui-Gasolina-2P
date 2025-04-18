<!-- edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tipo de Combustible</title>
</head>
<body>
    <h1>Editar Tipo de Combustible</h1>

    <form action="index.php?action=actualizar_combustible" method="POST">
        <input type="hidden" name="id" value="<?= $tipo['id']; ?>">

        <label for="tipo">Tipo:</label>
        <input type="text" name="tipo" id="tipo" value="<?= $tipo['tipo']; ?>" required><br>

        <label for="litros">Litros:</label>
        <input type="number" name="litros" id="litros" value="<?= $tipo['litros']; ?>" required><br>

        <button type="submit">Actualizar</button>
    </form>

    <a href="index.php">Volver al listado</a>
</body>
</html>
