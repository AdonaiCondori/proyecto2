<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Página de inicio</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="{{ asset('customer/assets/favicon.ico') }}" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="{{ asset('customer/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('frontend/css/styles.css') }}" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="{{ asset('frontend/css/custom.css') }}" rel="stylesheet" />
</head>

<body class="day youth">
    <!-- Barra de navegación -->
    @include('vendedor.layouts.navbar')

    <!-- Botón desplegable para cambiar temas y accesibilidad -->
    <div class="dropdown my-3 d-flex justify-content">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
            aria-expanded="false">
            <i class="fas fa-adjust"></i> Cambiar Tema y Accesibilidad
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#" onclick="toggleNightMode()"><i class="fas fa-sun"></i> Modo Día/Noche</a></li>
            <li><a class="dropdown-item" href="#" onclick="setTheme('theme-kids')"><i class="fas fa-child"></i> Tema para Niños</a></li>
            <li><a class="dropdown-item" href="#" onclick="setTheme('theme-youth')"><i class="fas fa-user-graduate"></i> Tema para Jóvenes</a></li>
            <li><a class="dropdown-item" href="#" onclick="setTheme('theme-adult')"><i class="fas fa-user"></i> Tema para Adultos</a></li>
            <li><a class="dropdown-item" href="#" onclick="setFontSize('font-small')"><i class="fas fa-text-height"></i> Fuente Pequeña</a></li>
            <li><a class="dropdown-item" href="#" onclick="setFontSize('font-medium')"><i class="fas fa-text-height"></i> Fuente Mediana</a></li>
            <li><a class="dropdown-item" href="#" onclick="setFontSize('font-large')"><i class="fas fa-text-height"></i> Fuente Grande</a></li>
            <li><a class="dropdown-item" href="#" onclick="toggleHighContrast()"><i class="fas fa-adjust"></i> Alto Contraste</a></li>
        </ul>
    </div>

    <!-- Contenido -->
    @yield('content')

    <!-- jQuery and Bootstrap Bundle JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Core theme JS-->
    <script src="{{ asset('customer/js/scripts.js') }}"></script>
    <!-- Custom JS -->
    <script>
        // Funciones para cambiar temas, tamaño de fuente y modo noche
        // (las mismas que en la página principal)
        function toggleNightMode() {
            document.body.classList.toggle('night-mode');
            document.body.classList.toggle('day-mode');
        }

        function setTheme(theme) {
            document.body.classList.remove('theme-kids', 'theme-youth', 'theme-adult');
            document.body.classList.add(theme);
        }

        function setFontSize(size) {
            document.body.classList.remove('font-small', 'font-medium', 'font-large');
            document.body.classList.add(size);
        }

        function toggleHighContrast() {
            document.body.classList.toggle('high-contrast');
        }

        // Detectar la hora del cliente y cambiar el modo automáticamente
        window.onload = function() {
            var hour = new Date().getHours();
            if (hour >= 18 || hour < 6) {
                document.body.classList.add('night-mode');
            } else {
                document.body.classList.add('day-mode');
            }
        }
    </script>
</body>

</html>
