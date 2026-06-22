<div>
    {{-- Encabezado del Módulo --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span>📊 Centro de Descargas y Reportes</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Genera y descarga reportes oficiales en formato PDF para los diferentes módulos y períodos de fecha.
            </p>
        </div>
    </div>

    {{-- Filtros de Fecha --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-8 border border-gray-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Rango del Reporte</label>
                <select wire:model.live="periodo" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="hoy">Hoy</option>
                    <option value="semana">Últimos 7 días</option>
                    <option value="mes_actual">Este Mes</option>
                    <option value="ano">Este Año</option>
                    <option value="personalizado">Personalizado</option>
                </select>
            </div>

            @if($periodo === 'personalizado')
            <div class="animate-fadeIn">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha Inicio</label>
                <input type="date" wire:model.live="fecha_inicio" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="animate-fadeIn">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha Fin</label>
                <input type="date" wire:model.live="fecha_fin" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            @else
            <div class="col-span-2 text-xs text-gray-400 self-center pb-2.5 italic">
                Rango seleccionado: <strong>{{ date('d/m/Y', strtotime($fecha_inicio)) }}</strong> al <strong>{{ date('d/m/Y', strtotime($fecha_fin)) }}</strong>
            </div>
            @endif
        </div>
    </div>

    {{-- Descargas por Módulos --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-base font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-file-pdf text-rose-500 text-lg"></i>
            <span>Exportar Módulos Disponibles</span>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Botón Usuarios -->
            <button wire:click="descargarPdf('usuarios')" wire:loading.attr="disabled" class="flex items-center justify-between p-5 rounded-xl border border-gray-200 hover:border-blue-500 hover:bg-blue-50/10 transition text-left group">
                <div class="flex items-center gap-4">
                    <div class="p-3.5 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-500 group-hover:text-white transition">
                        <i wire:loading.remove wire:target="descargarPdf('usuarios')" class="fas fa-users text-xl"></i>
                        <i wire:loading wire:target="descargarPdf('usuarios')" class="fas fa-spinner fa-spin text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">Reporte de Usuarios</h4>
                        <p class="text-xs text-gray-400 mt-1">Todos los usuarios, roles y datos de contacto registrados.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span wire:loading wire:target="descargarPdf('usuarios')" class="text-xs text-blue-600 font-semibold animate-pulse">Generando...</span>
                    <i wire:loading.remove wire:target="descargarPdf('usuarios')" class="fas fa-download text-gray-400 group-hover:text-blue-500 transition text-lg"></i>
                </div>
            </button>

            <!-- Botón Productos -->
            <button wire:click="descargarPdf('productos')" wire:loading.attr="disabled" class="flex items-center justify-between p-5 rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/10 transition text-left group">
                <div class="flex items-center gap-4">
                    <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-500 group-hover:text-white transition">
                        <i wire:loading.remove wire:target="descargarPdf('productos')" class="fas fa-box text-xl"></i>
                        <i wire:loading wire:target="descargarPdf('productos')" class="fas fa-spinner fa-spin text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">Reporte de Productos</h4>
                        <p class="text-xs text-gray-400 mt-1">Catálogo completo de productos, stock actual, mínimo y categorías.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span wire:loading wire:target="descargarPdf('productos')" class="text-xs text-emerald-600 font-semibold animate-pulse">Generando...</span>
                    <i wire:loading.remove wire:target="descargarPdf('productos')" class="fas fa-download text-gray-400 group-hover:text-emerald-500 transition text-lg"></i>
                </div>
            </button>

            <!-- Botón Ventas -->
            <button wire:click="descargarPdf('ventas')" wire:loading.attr="disabled" class="flex items-center justify-between p-5 rounded-xl border border-gray-200 hover:border-violet-500 hover:bg-violet-50/10 transition text-left group">
                <div class="flex items-center gap-4">
                    <div class="p-3.5 bg-violet-50 text-violet-600 rounded-lg group-hover:bg-violet-500 group-hover:text-white transition">
                        <i wire:loading.remove wire:target="descargarPdf('ventas')" class="fas fa-shopping-cart text-xl"></i>
                        <i wire:loading wire:target="descargarPdf('ventas')" class="fas fa-spinner fa-spin text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">Reporte de Ventas</h4>
                        <p class="text-xs text-gray-400 mt-1">Historial detallado de pedidos, montos facturados, cobrados y pendientes del período.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span wire:loading wire:target="descargarPdf('ventas')" class="text-xs text-violet-600 font-semibold animate-pulse">Generando...</span>
                    <i wire:loading.remove wire:target="descargarPdf('ventas')" class="fas fa-download text-gray-400 group-hover:text-violet-500 transition text-lg"></i>
                </div>
            </button>

            <!-- Botón Productos Más Vendidos -->
            <button wire:click="descargarPdf('mas-vendidos')" wire:loading.attr="disabled" class="flex items-center justify-between p-5 rounded-xl border border-gray-200 hover:border-amber-500 hover:bg-amber-50/10 transition text-left group">
                <div class="flex items-center gap-4">
                    <div class="p-3.5 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-500 group-hover:text-white transition">
                        <i wire:loading.remove wire:target="descargarPdf('mas-vendidos')" class="fas fa-fire text-xl"></i>
                        <i wire:loading wire:target="descargarPdf('mas-vendidos')" class="fas fa-spinner fa-spin text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">Productos Más Vendidos</h4>
                        <p class="text-xs text-gray-400 mt-1">Ranking ordenado por volumen de demanda y facturación en el período.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span wire:loading wire:target="descargarPdf('mas-vendidos')" class="text-xs text-amber-600 font-semibold animate-pulse">Generando...</span>
                    <i wire:loading.remove wire:target="descargarPdf('mas-vendidos')" class="fas fa-download text-gray-400 group-hover:text-amber-500 transition text-lg"></i>
                </div>
            </button>

        </div>
    </div>

    {{-- Soporte de animación FadeIn --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.25s ease-out forwards;
        }
    </style>
</div>
