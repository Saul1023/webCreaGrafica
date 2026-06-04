<div>
    <!-- Encabezado de Bienvenida -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Hola, {{ Auth::user()->nombre }}</h2>
        <p class="text-gray-500">Aquí tienes el resumen de CREAGRAFICA para hoy.</p>
    </div>

    <!-- Rejilla de Tarjetas Analíticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Tarjeta Ingresos Totales -->
        <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Ingresos Totales</p>
                    <p class="text-2xl font-bold text-gray-800">Bs. {{ number_format($totalVentas, 2) }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg text-green-600">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Tarjeta Pedidos de Hoy -->
        <div class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Pedidos Hoy</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $pedidosHoy }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Botón Usuarios (Mantiene tu funcionalidad) -->
        <button wire:click="cambiarVista('usuarios')"
            class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-purple-500 hover:bg-gray-50 transition text-left">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Usuarios</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalUsuarios }}</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg text-purple-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </button>

        <!-- Tarjeta Stock Crítico -->
        <button wire:click="cambiarVista('productos')"
            class="bg-white p-6 rounded-xl shadow-sm border-t-4 border-red-500 hover:bg-gray-50 transition text-left">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase">Stock Bajo</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stockCritico }}</p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Gráfico de Ventas Mensuales (Placeholder visual) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-700 mb-6">Tendencia de Ventas</h3>
            <div class="h-64 bg-gray-50 rounded-lg flex items-end justify-around p-4">
                {{-- Esto es un gráfico visual simple con CSS --}}
                <div class="bg-blue-200 w-8 h-20 rounded-t"></div>
                <div class="bg-blue-300 w-8 h-32 rounded-t"></div>
                <div class="bg-blue-400 w-8 h-16 rounded-t"></div>
                <div class="bg-blue-500 w-8 h-40 rounded-t"></div>
                <div class="bg-blue-600 w-8 h-56 rounded-t"></div>
            </div>
        </div>

        <!-- Productos Más Vendidos -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-700 mb-6">Top Productos</h3>
            <div class="space-y-4">
                @forelse($topProductos as $item)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $item->nombre }}</span>
                    <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded">{{ $item->ventas_count }}
                        ventas</span>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center">No hay datos de ventas aún.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
