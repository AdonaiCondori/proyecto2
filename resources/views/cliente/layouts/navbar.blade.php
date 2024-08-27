<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-1 px-lg-0">
        <!-- Logo de la Casa del Libro -->
        <a class="navbar-brand" href="#!">
            <img src="{{ asset('ruta/del/logo.png') }}" alt="" style="width: 40px; height: 40px;">
            La casa del Libro
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item"><a class="nav-link active" aria-current="page"
                        href="{{ route('cliente.dashboard') }}">Pagina Principal</a></li>
                <li class="nav-item"><a class="nav-link" href="#!">Acerca de</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                        data-toggle="dropdown" aria-expanded="false">Menu</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('cliente.layouts.index') }}">Todos los productos</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item" href="{{ route('cliente.layouts.indexc') }}">categorias</a></li>
                        <li><a class="dropdown-item" href="{{ route('cliente.profile.promocion') }}">En promocion</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Campo de Búsqueda -->
            <form class="form-inline my-2 my-lg-0" action="{{ route('cliente.buscar') }}" method="GET">
                <input class="form-control mr-sm-2" type="search" name="search" placeholder="Buscar"
                    aria-label="Buscar">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Buscar</button>
            </form>

            <!-- Enlace de botón carrito -->
            <form class="d-flex">
                <button class="btn btn-outline-dark" type="button"
                    onclick="location.href='{{ route('cliente.carrito.ver') }}'">
                    <i class="bi-cart-fill me-1"></i>
                    Carrito
                    <span class="badge bg-dark text-white ms-1 rounded-pill">{{ $carritoCount }}</span>
                </button>
            </form>

            <!-- Enlace a la página de pedidos -->
            <a href="{{ route('cliente.carrito.pedidos') }}" class="btn btn-outline-dark">Mis Pedidos</a>

            <!-- Menú desplegable de usuario -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle nav-link-lg nav-link-user" data-toggle="dropdown">
                        <img alt="image" src="{{ asset(Auth::user()->image) }}" class="rounded-circle mr-1"
                            width="30" height="30">
                        <span class="d-none d-lg-inline-block">{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-title">Conectado hace 5 min</div>
                        <a href="{{ route('cliente.profile') }}" class="dropdown-item has-icon"><i
                                class="far fa-user"></i> Editar perfil</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="dropdown-item has-icon text-danger"><i class="fas fa-sign-out-alt"></i> Cerrar
                                Sesión</a>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
