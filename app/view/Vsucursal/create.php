<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nueva Sucursal</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f3f5f9;
            margin: 0;
            padding: 30px;
            color: #2c3e50;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input[type="checkbox"] {
            margin-right: 6px;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: #34495e;
        }

        .combustibles-list {
            margin-bottom: 20px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #2ecc71;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #27ae60;
        }

        .volver-link {
            display: block;
            text-align: center;
            margin-bottom: 20px;
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }

        .volver-link:hover {
            text-decoration: underline;
        }

        .no-combustibles {
            color: #c0392b;
            margin-bottom: 15px;
        }

        .no-combustibles a {
            color: #e67e22;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            form {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <h1>Registrar Nueva Sucursal</h1>
    <a class="volver-link" href="index.php?action=sucursales">← Volver a la lista de sucursales</a>

    <form action="index.php?action=registrar_sucursal" method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        
        <label>Ubicación:</label>
        <input type="text" name="ubicacion" required>
        
        <label>Número de Bombas:</label>
        <input type="number" name="bombas" min="1" required>
        
        <h3>Seleccione los combustibles disponibles:</h3>
        <div class="combustibles-list">
            <?php 
            if ($combustibles && $combustibles->num_rows > 0): 
                while ($combustible = $combustibles->fetch_assoc()): 
            ?>
                <input type="checkbox" name="combustibles[]" value="<?= $combustible['id'] ?>">
                <label><?= $combustible['tipo'] ?></label><br>
            <?php 
                endwhile; 
            else: 
            ?>
                <p class="no-combustibles">No hay combustibles registrados. 
                    <a href="index.php?action=crear_combustible">Crear nuevo combustible</a>
                </p>
            <?php endif; ?>
        </div>

        <input type="submit" value="Registrar">
    </form>
</body>
</html>
