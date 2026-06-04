<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - CREAGRAFICA</title>

    {{-- Tailwind por CDN (ok para desarrollo, no para producción) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Estilos de Livewire --}}
    @livewireStyles

    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <span class="text-xl font-bold">
                    <i class="fas fa-mug-hot mr-2"></i>CREAGRAFICA
                </span>
            </div>

            <div class="p-4 border-b border-gray-800 text-center bg-gray-950/30">
                <div
                    class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-lg font-bold mx-auto mb-2 text-white shadow-md">
                    {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
                <p class="text-sm font-medium text-gray-200">{{ Auth::user()->nombre ?? 'Admin' }}</p>
                <p class="text-xs text-gray-400 mb-3">{{ Auth::user()->rol->nombre ?? 'Administrador' }}</p>

                <a href="{{ route('admin.perfil') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-all border {{ request()->routeIs('admin.perfil') ? 'bg-blue-600/30 border-blue-500 text-blue-400 font-semibold' : 'border-gray-700 text-gray-400 hover:text-white hover:bg-gray-800 hover:border-gray-600' }}">
                    <i class="fas fa-user-edit text-[10px]"></i>
                    <span>Editar Perfil</span>
                </a>
            </div>

            <nav class="flex-1 py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" wire:navigate
                            class="w-full flex items-center px-6 py-3 transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span class="ml-2">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users') }}" wire:navigate
                            class="w-full flex items-center px-6 py-3 transition {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-users w-6"></i>
                            <span class="ml-2">Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.productos') }}" wire:navigate
                            class="w-full flex items-center px-6 py-3 transition {{ request()->routeIs('admin.productos') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-box-open w-6"></i>
                            <span class="ml-2">Productos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.categorias') }}" wire:navigate
                            class="w-full flex items-center px-6 py-3 transition {{ request()->routeIs('admin.categorias') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-tags w-6"></i>
                            <span class="ml-2">Categorías</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pedidos') }}" wire:navigate
                            class="w-full flex items-center px-6 py-3 transition {{ request()->routeIs('admin.pedidos') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-shopping-cart w-6"></i>
                            <span class="ml-2">Pedidos</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-2 text-red-400 hover:bg-gray-800 rounded transition">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span class="ml-2">Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido principal --}}
        <main class="flex-1 overflow-y-auto p-6">
            @if(isset($slot))
            {{ $slot }}
            @else
            @yield('content')
            @endif
        </main>
    </div>

    {{-- Scripts de Livewire (incluyen Alpine integrado en v3) --}}
    @livewireScripts
</body>

</html>
