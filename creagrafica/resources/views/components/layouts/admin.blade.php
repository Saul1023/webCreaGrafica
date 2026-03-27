<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - CREAGRAFICA')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-gray-900 text-white hidden md:flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <a href="{{ route('home') }}" class="text-xl font-bold flex items-center hover:text-blue-400 transition">
                    <i class="fas fa-mug-hot mr-2"></i> CREAGRAFICA
                </a>
            </div>

            <div class="p-4 border-b border-gray-800 flex items-center">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-lg font-bold">
                    {{ substr(Auth::user()->name ?? Auth::user()->nombre_completo, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="font-medium text-sm">{{ Auth::user()->name ?? Auth::user()->nombre_completo }}</p>
                    <p class="text-xs text-gray-500">Administrador</p>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users') }}"
                           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-users w-6"></i>
                            <span>Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center px-6 py-3 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }} transition-colors">
                            <i class="fas fa-box-open w-6"></i>
                            <span>Productos</span>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="flex items-center px-6 py-3 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                            <i class="fas fa-cog w-6"></i>
                            <span>Configuración</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-red-400 hover:bg-gray-800 hover:text-red-300 rounded transition-colors">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="flex justify-between items-center py-4 px-6 bg-white border-b shadow-sm">
                <div class="flex items-center">
                    <button class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Admin')</h2>
                </div>
                <div>
                   <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">Ver sitio web</a>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>

    </div>

    @livewireScripts
</body>
</html>
