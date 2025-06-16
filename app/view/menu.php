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
            max-width: 800px;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">⛽</div>
        <h1>GASOLINERA</h1>
        <p class="subtitle">Sistema de Gestión y Estimación de Colas</p>

        <?php
        // Mostrar mensajes de sesión si existen
        if (isset($_SESSION['success'])):
        ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['info'])): ?>
            <div class="alert alert-info">
                ℹ️ <?= htmlspecialchars($_SESSION['info']) ?>
            </div>
            <?php unset($_SESSION['info']); ?>
        <?php endif; ?>

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
                    Administrar tipos de combustible y sus parámetros
                </div>
            </a>

            <a href="index.php?action=tanques" class="menu-item">
                <span class="menu-icon">⚙️</span>
                <div class="menu-title">Gestión de Tanques</div>
                <div class="menu-description">
                    Configurar capacidades y estados de almacenamiento
                </div>
            </a>

            <a href="index.php?action=parametros_combustible" class="menu-item">
                <span class="menu-icon">📊</span>
                <div class="menu-title">Parámetros de Combustible</div>
                <div class="menu-description">
                    Configurar consumo promedio, tiempo de carga y medidas
                </div>
            </a>

            <a href="index.php?action=estimacion_cola" class="menu-item">
                <span class="menu-icon">📈</span>
                <div class="menu-title">Estimación de Colas</div>
                <div class="menu-description">
                    Visualizar estimaciones de tiempo y distancia de colas
                </div>
            </a>
        </div>

        <div class="version">
            <strong>Sistema de Gasolinera v2.0</strong><br>
            Arquitectura MVC | Patrón Memento<br>
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

        // Efecto de ripple en los botones
        document.querySelectorAll('.menu-item').forEach(button => {
            button.addEventListener('click', function(e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);

                let x = e.clientX - e.target.offsetLeft;
                let y = e.clientY - e.target.offsetTop;

                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                setTimeout(() => {
                    ripple.remove();
                }, 300);
            });
        });
    </script>
</body>
</html>