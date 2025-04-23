<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gasolinera - Menú Principal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .menu-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }
        .menu-btn {
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
        }
        .menu-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GASOLINERA</h1>
        
        <div class="menu-options">
            <a href="index.php?action=sucursales" class="menu-btn">Gestión de Sucursales</a>
            <a href="index.php?action=combustibles" class="menu-btn">Gestión de Combustibles</a>
            <a href="index.php?action=tanques" class="menu-btn">Gestión de Tanques</a>
            <a href="index.php?action=parametros_combustible" class="menu-btn blue">Parámetros de Combustible</a>
            <a href="index.php?action=estimacion_cola" class="menu-btn blue">Estimación de Colas</a>
        </div>
    </div>
</body>
</html>