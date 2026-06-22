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
                    @if(!request()->routeIs('cliente.*') && !(auth()->check() && auth()->user()->rol_id == 2))
                    <a href="{{ url('/#nosotros') }}" class="text-white hover:text-blue-200 transition-all">Nosotros</a>
                    <a href="{{ url('/#contacto') }}" class="text-white hover:text-blue-200 transition-all">Contacto</a>
                    @endif
                    @auth
                        @if(auth()->user()->rol_id == 2)
                        <a href="{{ route('cliente.dashboard') }}" wire:navigate
                            class="text-white hover:text-blue-200 transition-all font-semibold"><i class="fas fa-shopping-bag mr-1"></i>Mis Pedidos</a>
                        @endif
                    @endauth

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
                            @if(auth()->user()->rol_id == 2)
                            <a href="{{ route('cliente.dashboard', ['active_tab' => 'pedidos']) }}" wire:navigate
                                class="block px-4 py-2 hover:bg-blue-50 font-medium text-blue-600"><i class="fas fa-shopping-bag mr-2"></i>Mis Pedidos</a>
                            <a href="{{ route('cliente.dashboard', ['active_tab' => 'perfil']) }}" wire:navigate
                                class="block px-4 py-2 hover:bg-blue-50 text-gray-700 hover:text-blue-600"><i class="fas fa-user-cog mr-2"></i>Mi Perfil</a>
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
                @if(!request()->routeIs('cliente.*') && !(auth()->check() && auth()->user()->rol_id == 2))
                <a href="{{ url('/#nosotros') }}" class="text-white block px-3 py-2">Nosotros</a>
                @endif
                @auth
                    @if(auth()->user()->rol_id == 2)
                    <a href="{{ route('cliente.dashboard', ['active_tab' => 'pedidos']) }}" wire:navigate class="text-white block px-3 py-2 font-semibold"><i class="fas fa-shopping-bag mr-1"></i>Mis Pedidos</a>
                    <a href="{{ route('cliente.dashboard', ['active_tab' => 'perfil']) }}" wire:navigate class="text-white block px-3 py-2"><i class="fas fa-user-cog mr-1"></i>Mi Perfil</a>
                    @endif
                @endauth
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

    <footer class="bg-slate-900 text-slate-350 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Column 1: Brand --}}
                <div class="text-left">
                    <span class="text-lg font-black tracking-widest flex items-center text-white uppercase mb-4">
                        <i class="fas fa-palette text-blue-500 mr-2.5 text-xl"></i>
                        <span>CREAGRAFICA</span>
                    </span>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Especialistas en sublimación de alta gama y personalización de tazas, termos, camisetas y accesorios premium. Damos vida a tus recuerdos e ideas con colores vibrantes y duraderos.
                    </p>
                </div>
                
                {{-- Column 2: Contact and Address --}}
                <div class="text-left">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4"><i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>Contacto y Dirección</h3>
                    <ul class="space-y-2.5 text-xs text-slate-400">
                        <li class="flex items-start gap-2.5">
                            <i class="fas fa-map-marked-alt text-slate-500 mt-0.5"></i>
                            <span>Calle Bolívar #320, Zona Central, Santa Cruz de la Sierra - Bolivia</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fas fa-phone-alt text-slate-500"></i>
                            <span>+591 69608947</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i class="fas fa-envelope text-slate-500"></i>
                            <span>contacto@creagrafica.com</span>
                        </li>
                    </ul>
                </div>

                {{-- Column 3: Social Media --}}
                <div class="text-left">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4"><i class="fas fa-share-nodes text-blue-500 mr-2"></i>Nuestras Redes</h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">Síguenos para conocer nuevas promociones, deiseños exclusivos y catálogos de temporada.</p>
                    <div class="flex gap-3">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-blue-600 text-white flex items-center justify-center transition shadow-sm hover:shadow-blue-500/20"
                            title="Facebook">
                            <i class="fab fa-facebook-f text-base"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-gradient-to-tr hover:from-yellow-600 hover:via-red-500 hover:to-purple-600 text-white flex items-center justify-center transition shadow-sm"
                            title="Instagram">
                            <i class="fab fa-instagram text-base"></i>
                        </a>
                        <a href="https://wa.me/59169608947" target="_blank" rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-green-600 text-white flex items-center justify-center transition shadow-sm hover:shadow-green-500/20"
                            title="WhatsApp">
                            <i class="fab fa-whatsapp text-base"></i>
                        </a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" 
                            class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-black text-white flex items-center justify-center transition shadow-sm"
                            title="TikTok">
                            <i class="fab fa-tiktok text-base"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            {{-- Copyright bottom row --}}
            <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} CREAGRAFICA. Todos los derechos reservados.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-slate-300 transition">Términos de Servicio</a>
                    <a href="#" class="hover:text-slate-300 transition">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn) menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    </script>
</body>

</html>
