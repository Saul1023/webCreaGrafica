<div class="container mx-auto">
    {{-- Notificaciones --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg text-emerald-800 flex items-center justify-between shadow-sm animate-fadeIn">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                <p class="text-sm font-medium">{{ session('message') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-lg text-rose-800 flex items-center justify-between shadow-sm animate-fadeIn">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl text-rose-600"></i>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <span>📥 Reabastecimiento de Inventario (Compras)</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Registra compras de mercadería para reponer stock y auditar costos de insumos.
            </p>
        </div>

        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition flex items-center gap-2 text-sm shadow-md font-semibold">
            <i class="fas fa-plus"></i>
            <span>Registrar Compra</span>
        </button>
    </div>

    {{-- Panel de Filtros --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Buscar por Nro. Factura / Recibo</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search_factura" placeholder="Ej. FACT-4512..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Proveedor</label>
                <select wire:model.live="filtro_proveedor" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los Proveedores</option>
                    @foreach($proveedores_select as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Historial de Compras --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b bg-gray-50/50">
                        <th class="px-6 py-3.5">ID Compra</th>
                        <th class="px-6 py-3.5">Proveedor</th>
                        <th class="px-6 py-3.5">Nro. Factura / Recibo</th>
                        <th class="px-6 py-3.5">Fecha de Compra</th>
                        <th class="px-6 py-3.5 text-right">Total</th>
                        <th class="px-6 py-3.5 text-center">Usuario Receptor</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($compras as $comp)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-mono font-bold text-gray-800">
                                #{{ str_pad($comp->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-700">
                                {{ $comp->proveedor->nombre }}
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-600">
                                {{ $comp->numero_factura ?: 'Sin documento' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ date('d/m/Y H:i', strtotime($comp->creado_en)) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                Bs. {{ number_format($comp->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center text-xs font-medium text-gray-500">
                                {{ $comp->usuario->nombre }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="verDetallesCompra({{ $comp->id }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs inline-flex items-center gap-1.5">
                                    <i class="fas fa-eye"></i> Detalles
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                                <i class="fas fa-boxes text-4xl block mb-2"></i>
                                No se encontraron compras registradas en el historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $compras->links() }}
        </div>
    </div>

    {{-- MODAL: REGISTRAR COMPRA --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <div class="relative bg-white rounded-2xl max-w-4xl w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 my-8 animate-fadeIn">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Registrar Compra / Reposición</span>
                    </h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 max-h-[75vh] overflow-y-auto">
                    <form wire:submit.prevent="store" class="space-y-6">
                        {{-- Cabecera de Compra --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Proveedor *</label>
                                <select wire:model="proveedor_id" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccione Proveedor</option>
                                    @foreach($proveedores_select as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proveedor_id') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nro. Factura / Recibo / Remisión</label>
                                <input type="text" wire:model="numero_factura" placeholder="Ej. FAC-10025..." 
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                                @error('numero_factura') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Sección de selección de productos --}}
                        <div class="border-t pt-4">
                            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Agregar Productos a la Compra</h4>
                            
                            <div class="relative">
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                        <i class="fas fa-barcode"></i>
                                    </span>
                                    <input type="text" wire:model.live.debounce.150ms="producto_search" placeholder="Buscar producto por nombre o SKU..." 
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Dropdown de resultados autocomplete --}}
                                @if(!empty($productos_buscados))
                                    <div class="absolute z-20 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto divide-y">
                                        @foreach($productos_buscados as $p)
                                            <div wire:click="seleccionarProducto({{ $p->id }})" class="px-4 py-3 hover:bg-blue-50 cursor-pointer transition flex items-center justify-between text-sm">
                                                <div>
                                                    <span class="font-bold text-gray-800">{{ $p->nombre }}</span>
                                                    <span class="block text-xs text-gray-400 font-mono">SKU: {{ $p->sku }} | Stock Actual: {{ $p->stock }}</span>
                                                </div>
                                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded">Agregar</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tabla de Detalles --}}
                        <div class="border-t pt-4">
                            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Detalle de Reposición</h4>
                            @error('detalles') <span class="block text-rose-500 text-xs font-semibold mb-3">{{ $message }}</span> @enderror

                            @if(!empty($detalles))
                                <div class="border rounded-xl overflow-hidden shadow-sm">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left font-bold text-gray-600 text-xs">Producto / SKU</th>
                                                <th class="px-4 py-3 text-center font-bold text-gray-600 text-xs w-32">Cantidad</th>
                                                <th class="px-4 py-3 text-right font-bold text-gray-600 text-xs w-36">Costo Unit. (Bs.)</th>
                                                <th class="px-4 py-3 text-right font-bold text-gray-600 text-xs w-36">Subtotal</th>
                                                <th class="px-4 py-3 text-center w-16"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($detalles as $index => $det)
                                                <tr class="hover:bg-gray-50/20">
                                                    <td class="px-4 py-3">
                                                        <div class="font-semibold text-gray-800">{{ $det['producto_nombre'] }}</div>
                                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $det['producto_sku'] }}</div>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="number" wire:model.live="detalles.{{ $index }}.cantidad" min="1" 
                                                            class="w-20 border border-gray-300 rounded-lg p-1.5 text-center text-sm font-semibold focus:ring-1 focus:ring-blue-500">
                                                        @error("detalles.{$index}.cantidad") <span class="block text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <input type="number" step="0.01" wire:model.live="detalles.{{ $index }}.costo_unitario" min="0" 
                                                            class="w-28 border border-gray-300 rounded-lg p-1.5 text-right text-sm font-mono focus:ring-1 focus:ring-blue-500">
                                                        @error("detalles.{$index}.costo_unitario") <span class="block text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-bold text-gray-800 font-mono">
                                                        Bs. {{ number_format($det['subtotal'], 2) }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button" wire:click="quitarProducto({{ $index }})" class="text-red-500 hover:text-red-700">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50/50 font-bold border-t">
                                            <tr>
                                                <td colspan="3" class="px-4 py-3 text-right text-gray-500 uppercase tracking-wider text-xs">Total Factura:</td>
                                                <td class="px-4 py-3 text-right text-emerald-600 text-lg font-mono">Bs. {{ number_format($total, 2) }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="border border-dashed rounded-xl p-8 text-center text-gray-400">
                                    <i class="fas fa-barcode text-3xl block mb-2"></i>
                                    Escribe en el buscador superior para agregar productos a esta orden de compra.
                                </div>
                            @endif
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-lg border text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <span wire:loading.remove wire:target="store">Confirmar y Reabastecer</span>
                                <span wire:loading wire:target="store" class="flex items-center gap-1.5">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: VER DETALLE DE COMPRA --}}
    @if($compra_detalle)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cerrarVerDetalles"></div>

            <div class="relative bg-white rounded-2xl max-w-2xl w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 animate-fadeIn">
                <div class="bg-gray-950 text-white px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold flex items-center gap-2">
                            <i class="fas fa-file-invoice"></i>
                            <span>Resumen de Compra</span>
                        </h3>
                        <span class="text-xs text-gray-450 font-mono mt-0.5">Orden ID: #{{ str_pad($compra_detalle->id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <button wire:click="cerrarVerDetalles" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6">
                    {{-- Ficha del Proveedor y Factura --}}
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border mb-6 text-sm">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Proveedor:</span>
                            <span class="font-bold text-gray-800">{{ $compra_detalle->proveedor->nombre }}</span>
                            @if($compra_detalle->proveedor->nit)
                                <span class="block text-xs text-gray-400 mt-0.5">NIT: {{ $compra_detalle->proveedor->nit }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Documento Nro:</span>
                            <span class="font-semibold text-gray-700 font-mono">{{ $compra_detalle->numero_factura ?: 'Sin documento adjunto' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Fecha de Compra:</span>
                            <span class="font-semibold text-gray-700">{{ date('d/m/Y H:i', strtotime($compra_detalle->creado_en)) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Recibido por:</span>
                            <span class="font-semibold text-gray-750">{{ $compra_detalle->usuario->nombre }}</span>
                        </div>
                    </div>

                    {{-- Lista de Ítems --}}
                    <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Productos Ingresados</h4>
                    <div class="border rounded-xl overflow-hidden mb-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-bold text-gray-600 text-xs">Producto / SKU</th>
                                    <th class="px-4 py-2.5 text-center font-bold text-gray-600 text-xs w-24">Cant.</th>
                                    <th class="px-4 py-2.5 text-right font-bold text-gray-600 text-xs w-32">Costo Unit.</th>
                                    <th class="px-4 py-2.5 text-right font-bold text-gray-600 text-xs w-32">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($compra_detalle->detalles as $det)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-800">{{ $det->producto->nombre ?? 'Producto Eliminado' }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $det->producto->sku ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 font-semibold">{{ $det->cantidad }}</td>
                                        <td class="px-4 py-3 text-right text-gray-500 font-mono">Bs. {{ number_format($det->costo_unitario, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-800 font-mono">Bs. {{ number_format($det->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Resumen Total --}}
                    <div class="space-y-2 border-t pt-4 text-sm flex flex-col items-end">
                        <div class="flex justify-between w-64 text-gray-900 text-base font-bold">
                            <span>Total de la Compra:</span>
                            <span class="font-mono text-emerald-600 text-lg">Bs. {{ number_format($compra_detalle->total, 2) }}</span>
                        </div>
                    </div>

                    {{-- Botón Cerrar --}}
                    <div class="flex justify-end pt-4 mt-6 border-t">
                        <button type="button" wire:click="cerrarVerDetalles" class="px-5 py-2 rounded-lg bg-gray-950 hover:bg-gray-900 text-white text-xs font-bold transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Soporte de Animación --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.98) translateY(5px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out forwards;
        }
    </style>
</div>
