<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nueva Sucursal</title>
</head>
<body>
    <h1>Registrar Nueva Sucursal</h1>
    <a href="index.php?action=sucursales">Volver</a>

    <form action="index.php?action=registrar_sucursal" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required><br><br>
        
        <label>Ubicación:</label>
        <input type="text" name="ubicacion" required><br><br>
        
        <label>Número de Bombas:</label>
        <input type="number" name="bombas" min="1" required><br><br>
        
        <h3>Seleccione los combustibles disponibles:</h3>
        <?php 
        // Asegúrate que $combustibles está definido y tiene datos
        if ($combustibles && $combustibles->num_rows > 0): 
            while ($combustible = $combustibles->fetch_assoc()): 
        ?>
            <input type="checkbox" name="combustibles[]" value="<?= $combustible['id'] ?>">
            <label><?= $combustible['tipo'] ?></label><br>
        <?php 
            endwhile; 
        else: 
        ?>
            <p>No hay combustibles registrados. <a href="index.php?action=crear_combustible">Crear nuevo combustible</a></p>
        <?php endif; ?>
        
        <br>
        <input type="submit" value="Registrar">
    </form>
</body>
</html>