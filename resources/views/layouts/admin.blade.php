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
    /* Thin custom scrollbar for navigation list */
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-950 text-slate-100 flex flex-col h-screen select-none border-r border-slate-800/50 shadow-2xl">
            {{-- Header/Logo --}}
            <div class="h-16 flex items-center justify-center border-b border-slate-800/60 flex-shrink-0 bg-slate-950">
                <span class="text-lg font-black tracking-widest flex items-center uppercase">
                    <i class="fas fa-palette text-blue-500 mr-2.5 text-xl"></i>
                    <span class="bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-400 bg-clip-text text-transparent">CREAGRAFICA</span>
                </span>
            </div>

            {{-- Perfil de Usuario --}}
            <div class="p-4 border-b border-slate-800/60 text-center bg-slate-900/20 flex-shrink-0">
                <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-xl font-black mx-auto mb-2 text-white shadow-lg shadow-blue-500/20 border-2 border-slate-800/60">
                    {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                </div>
                <p class="text-sm font-bold text-slate-200">{{ Auth::user()->nombre ?? 'Admin' }}</p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ Auth::user()->rol->nombre ?? 'Administrador' }}</p>

                <a href="{{ route('admin.perfil') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold transition-all border {{ request()->routeIs('admin.perfil') ? 'bg-blue-600/20 border-blue-500 text-blue-400 shadow-sm' : 'border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 hover:border-slate-700' }}">
                    <i class="fas fa-user-edit text-[10px]"></i>
                    <span>Editar Perfil</span>
                </a>
            </div>

            {{-- Navegación Scrollable --}}
            <nav class="flex-1 py-4 overflow-y-auto min-h-0 custom-scrollbar">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-tachometer-alt w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-users w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Usuarios</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.productos') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.productos') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-box-open w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Productos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.categorias') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.categorias') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-tags w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Categorías</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.pedidos') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.pedidos') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-shopping-cart w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Pedidos</span>
                            @php
                                $pendingOrdersCount = \App\Models\Pedido::where('estado', 'pendiente')->count();
                            @endphp
                            @if($pendingOrdersCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm animate-pulse" title="Pedidos pendientes por revisar">
                                    {{ $pendingOrdersCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.caja') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.caja') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-cash-register w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Control de Caja</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.proveedores') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.proveedores') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-building w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Proveedores</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.compras') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.compras') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-dolly w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Compras (Stock)</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.cupones') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.cupones') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-ticket-alt w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Cupones</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.ofertas') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.ofertas') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-percent w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Ofertas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.reportes') }}" wire:navigate
                            class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.reportes') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-400 hover:bg-slate-900/60 hover:text-slate-200' }}">
                            <i class="fas fa-chart-bar w-5 text-center mr-2.5"></i>
                            <span class="text-sm">Reportes</span>
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Footer / Cerrar Sesión Pinned --}}
            <div class="p-4 border-t border-slate-800/60 bg-slate-950 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center gap-2.5 w-full px-4 py-2.5 bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white border border-red-500/20 hover:border-red-600 rounded-lg transition-all duration-200 font-bold text-sm cursor-pointer shadow-sm hover:shadow-red-600/10">
                        <i class="fas fa-sign-out-alt w-5 text-center text-base"></i>
                        <span>Cerrar Sesión</span>
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
