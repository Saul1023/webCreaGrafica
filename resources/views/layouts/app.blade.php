<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title ?? 'CREAGRAFICA - Productos Personalizados')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Alpine.js es necesario para componentes interactivos --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        scroll-behavior: smooth;
    }

    .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .dropdown:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">
    <nav class="bg-blue-600 shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="text-white text-xl font-bold flex items-center">
                        <i class="fas fa-mug-hot mr-2"></i> CREAGRAFICA
                    </a>
                </div>

                {{-- Navegación Desktop --}}
                <div class="hidden md:flex items-center space-x-8">
                    {{-- CORREGIDO: Apunta al ancla del catálogo público --}}
                    <a href="{{ url('/#productos') }}"
                        class="text-white hover:text-blue-200 transition-all">Productos</a>
                    <a href="{{ url('/#nosotros') }}" class="text-white hover:text-blue-200 transition-all">Nosotros</a>
                    <a href="{{ url('/#contacto') }}" class="text-white hover:text-blue-200 transition-all">Contacto</a>

                    @auth
                    <div class="relative dropdown">
                        <button class="flex items-center text-white hover:text-blue-200 transition-all">
                            <i class="fas fa-user-circle mr-2"></i>
                            <span>{{ Auth::user()->nombre ?? Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div
                            class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border py-2 z-50 text-gray-700">
                            {{-- CORREGIDO: Ajuste para validar si el rol es administrador (rol_id == 1) --}}
                            @if(auth()->user()->rol_id == 1)
                            <a href="{{ route('admin.dashboard') }}" wire:navigate
                                class="block px-4 py-2 hover:bg-blue-50"><i class="fas fa-tachometer-alt mr-2"></i>Panel
                                Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50"><i
                                        class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión</button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" wire:navigate
                            class="text-white hover:text-blue-200 transition-all font-medium">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" wire:navigate
                            class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-all">Registrarse</a>
                    </div>
                    @endauth
                </div>

                {{-- Botón Móvil --}}
                <div class="md:hidden flex items-center">
                    <button id="menu-btn" class="text-white focus:outline-none"><i
                            class="fas fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>

        {{-- Menú Móvil --}}
        <div id="mobile-menu" class="hidden md:hidden bg-blue-700 pb-4">
            <div class="px-2 pt-2 pb-3 space-y-1">
                {{-- CORREGIDO: Apunta al ancla del catálogo público --}}
                <a href="{{ url('/#productos') }}" class="text-white block px-3 py-2">Productos</a>
                <a href="{{ url('/#nosotros') }}" class="text-white block px-3 py-2">Nosotros</a>
                @guest
                <a href="{{ route('login') }}" wire:navigate class="text-white block px-3 py-2">Iniciar Sesión</a>
                <a href="{{ route('register') }}" wire:navigate
                    class="bg-white text-blue-600 block px-3 py-2 rounded-md text-center mt-2">Registrarse</a>
                @endguest
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @if(isset($slot))
        {{ $slot }}
        @else
        @yield('content')
        @endif
    </main>

    <footer class="bg-gray-800 text-white py-8 text-center">
        <p>&copy; {{ date('Y') }} CREAGRAFICA. Todos los derechos reservados.</p>
    </footer>

    @livewireScripts
    <script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn) menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    </script>
</body>

</html>
