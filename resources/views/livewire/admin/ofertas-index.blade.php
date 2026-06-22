<div x-data="{ openModal: $wire.entangle('isOpen') }"
    x-init="$watch('openModal', value => { if(!value) $wire.closeModal() })">

    @if (session()->has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow">
        <p>{{ session('message') }}</p>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">🏷️ Gestión de Ofertas Especiales</h2>
        <button wire:click="create" wire:loading.attr="disabled"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2 disabled:opacity-50">
            <i class="fas fa-plus" wire:loading.remove wire:target="create"></i>
            <i class="fas fa-spinner fa-spin" wire:loading wire:target="create"></i>
            <span>Nueva Oferta</span>
        </button>
    </div>

    {{-- Buscador --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-search text-gray-400 mr-1"></i> Buscar Oferta
            </label>
            <input wire:model.live="search" type="text" placeholder="Buscar por nombre de campaña..."
                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>
    </div>

    {{-- Tabla de Ofertas --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaña</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vigencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos Asociados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($ofertas as $oferta)
                    @php
                        $estaVigente = $oferta->activo && $oferta->fecha_inicio->isPast() && $oferta->fecha_fin->isFuture();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $oferta->nombre }}</div>
                            @if($estaVigente)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 mt-1 uppercase tracking-wider">
                                    Vigente
                                </span>
                            @elseif(!$oferta->activo)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-gray-100 text-gray-800 mt-1 uppercase tracking-wider">
                                    Desactivada
                                </span>
                            @elseif($oferta->fecha_inicio->isFuture())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800 mt-1 uppercase tracking-wider">
                                    Programada
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 mt-1 uppercase tracking-wider">
                                    Expirada
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black text-red-650 bg-red-50 px-2 py-1 rounded-lg">-{{ number_format($oferta->descuento, 0) }}%</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-600 leading-relaxed font-mono">
                            <div><strong>Inicio:</strong> {{ $oferta->fecha_inicio->format('d/m/Y H:i') }}</div>
                            <div class="mt-1"><strong>Fin:</strong> {{ $oferta->fecha_fin->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @forelse($oferta->productos as $p)
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-750 text-[10px] rounded font-semibold border border-gray-200" title="{{ $p->nombre }}">
                                        {{ Str::limit($p->nombre, 18) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Sin productos</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleActivo({{ $oferta->id }})"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition border {{ $oferta->activo ? 'bg-emerald-50 text-emerald-800 border-emerald-250 hover:bg-emerald-100' : 'bg-gray-150 text-gray-800 border-gray-300 hover:bg-gray-250' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $oferta->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $oferta->activo ? 'Activa' : 'Inactiva' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button wire:click="edit({{ $oferta->id }})"
                                class="text-blue-600 hover:text-blue-800 mr-3 transition" title="Editar">
                                <i class="fas fa-edit text-base"></i>
                            </button>
                            <button
                                onclick="confirm('¿Está seguro de eliminar esta oferta de campaña?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $oferta->id }})"
                                class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                                <i class="fas fa-trash-alt text-base"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <i class="fas fa-tags text-5xl mb-3"></i>
                            <p>No hay campañas de ofertas registradas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $ofertas->links() }}
        </div>
    </div>

    {{-- MODAL DE CREACIÓN/EDICIÓN --}}
    <div x-show="$wire.isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.isOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white text-xl">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white"
                                    x-text="$wire.oferta_id ? 'Editar Campaña de Oferta' : 'Nueva Campaña de Oferta'"></h3>
                                <p class="text-xs text-blue-100">Configura los descuentos aplicables por rangos de fechas</p>
                            </div>
                        </div>
                        <button type="button" @click="$wire.isOpen = false" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 text-left">
                    <div class="space-y-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Campaña <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="nombre" placeholder="Ej. Ofertas del Día del Padre"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nombre') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Descuento --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Porcentaje de Descuento (%) <span class="text-red-500">*</span></label>
                            <div class="relative max-w-xs">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-550 font-bold">%</span>
                                <input type="number" step="0.01" wire:model="descuento" placeholder="Ej. 15"
                                    class="w-full pl-8 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            @error('descuento') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Fecha Inicio --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora de Inicio <span class="text-red-500">*</span></label>
                                <input type="datetime-local" wire:model="fecha_inicio"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('fecha_inicio') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Fecha Fin --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora de Fin <span class="text-red-500">*</span></label>
                                <input type="datetime-local" wire:model="fecha_fin"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('fecha_fin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Productos --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Seleccionar Productos en Oferta</label>
                            <div class="border rounded-lg p-3 bg-gray-50/50 max-h-48 overflow-y-auto space-y-2.5 custom-scrollbar">
                                @forelse($todosProductos as $p)
                                    <label class="flex items-start cursor-pointer select-none">
                                        <input type="checkbox" wire:model="productos_seleccionados" value="{{ $p->id }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                                        <div class="ml-2.5">
                                            <span class="text-sm text-gray-800 font-semibold block leading-tight">{{ $p->nombre }}</span>
                                            <span class="text-[10px] text-gray-450 font-mono">SKU: {{ $p->sku }} - Bs. {{ number_format($p->precio, 2) }}</span>
                                        </div>
                                    </label>
                                @empty
                                    <p class="text-xs text-gray-400 italic text-center py-4">No hay productos activos para agregar.</p>
                                @endforelse
                            </div>
                            @error('productos_seleccionados') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Activo --}}
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model="activo"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Habilitar esta campaña de oferta</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2 border-t">
                    <button type="button" @click="$wire.isOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="store" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <i class="fas fa-save" wire:loading.remove wire:target="store"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="store"></i>
                        <span x-text="$wire.oferta_id ? 'Actualizar Oferta' : 'Crear Oferta'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
