<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Gasolinera</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 900px;
            width: 90%;
            text-align: center;
        }

        .logo {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 300;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 1.2rem;
            margin-bottom: 3rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .menu-item {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem 1.5rem;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-color: #667eea;
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .menu-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .menu-description {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.4;
        }

        .version {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 2rem;
                margin: 1rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .menu-item {
                padding: 1.5rem;
            }
        }

        .highlight {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            border-color: #28a745;
        }

        .highlight .menu-icon {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">⛽</div>
        <h1>GASOLINERA</h1>
        <p class="subtitle">Sistema de Gestión y Estimación de Colas</p>

        <div class="menu-grid">
            <a href="index.php?action=sucursales" class="menu-item">
                <span class="menu-icon">🏪</span>
                <div class="menu-title">Gestión de Sucursales</div>
                <div class="menu-description">
                    Administrar sucursales, ubicaciones y configuración de bombas
                </div>
            </a>

            <a href="index.php?action=combustibles" class="menu-item">
                <span class="menu-icon">🛢️</span>
                <div class="menu-title">Gestión de Combustibles</div>
                <div class="menu-description">
                    Administrar tipos de combustible disponibles en el sistema
                </div>
            </a>

            <a href="index.php?action=parametros_combustible" class="menu-item">
                <span class="menu-icon">📊</span>
                <div class="menu-title">Parámetros de Combustible</div>
                <div class="menu-description">
                    Configurar consumo promedio, tiempo de carga y medidas por vehículo
                </div>
            </a>

            <a href="index.php?action=tanques" class="menu-item">
                <span class="menu-icon">⚙️</span>
                <div class="menu-title">Gestión de Tanques</div>
                <div class="menu-description">
                    Asignar combustibles a sucursales y configurar relaciones
                </div>
            </a>

            <a href="index.php?action=almacenamiento" class="menu-item highlight">
                <span class="menu-icon">🏭</span>
                <div class="menu-title">Gestión de Almacenamiento</div>
                <div class="menu-description">
                    Administrar capacidades, estados y niveles de combustible
                </div>
            </a>

            <a href="index.php?action=estimacion_cola" class="menu-item highlight">
                <span class="menu-icon">📈</span>
                <div class="menu-title">Estimación de Colas</div>
                <div class="menu-description">
                    Visualizar estimaciones de tiempo, distancia y cantidad de vehículos
                </div>
            </a>
        </div>

        <div class="version">
            <strong>Sistema de Gasolinera v2.0</strong><br>
            Arquitectura MVC | Patrón Memento<br>
            <small>Tablas: sucursal → sucursal_combustible → almacenamiento → cola_estimada</small><br>
            Universidad Autónoma Gabriel René Moreno<br>
            Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones
        </div>
    </div>

    <script>
        // Animación de entrada para los elementos del menú
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.5s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>