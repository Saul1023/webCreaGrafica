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
        <h2 class="text-2xl font-bold text-gray-800">🎟️ Gestión de Cupones de Descuento</h2>
        <button wire:click="create" wire:loading.attr="disabled"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2 disabled:opacity-50">
            <i class="fas fa-plus" wire:loading.remove wire:target="create"></i>
            <i class="fas fa-spinner fa-spin" wire:loading wire:target="create"></i>
            <span>Nuevo Cupón</span>
        </button>
    </div>

    {{-- Buscador --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-search text-gray-400 mr-1"></i> Buscar Cupón
            </label>
            <input wire:model.live="search" type="text" placeholder="Buscar por código de cupón..."
                class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
        </div>
    </div>

    {{-- Tabla de Cupones --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compra Mínima</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Límite de Uso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiración</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cupones as $cupon)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold font-mono bg-blue-50 text-blue-800 px-2.5 py-1 rounded-lg">{{ $cupon->codigo }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $cupon->tipo === 'porcentaje' ? 'Porcentaje' : 'Valor Fijo' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">
                            {{ $cupon->tipo === 'porcentaje' ? number_format($cupon->valor, 0) . '%' : 'Bs. ' . number_format($cupon->valor, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            Bs. {{ number_format($cupon->compra_minima, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $cupon->limite_uso ?: 'Ilimitado' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-bold">
                            {{ $cupon->veces_usado }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($cupon->fecha_expiracion)
                                <span class="{{ $cupon->fecha_expiracion->isPast() && !$cupon->fecha_expiracion->isToday() ? 'text-red-500 font-semibold' : '' }}">
                                    {{ $cupon->fecha_expiracion->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">Sin límite</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleActivo({{ $cupon->id }})"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition border {{ $cupon->activo ? 'bg-emerald-50 text-emerald-800 border-emerald-250 hover:bg-emerald-100' : 'bg-gray-150 text-gray-800 border-gray-300 hover:bg-gray-250' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $cupon->activo ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ $cupon->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button wire:click="edit({{ $cupon->id }})"
                                class="text-blue-600 hover:text-blue-800 mr-3 transition" title="Editar">
                                <i class="fas fa-edit text-base"></i>
                            </button>
                            <button
                                onclick="confirm('¿Está seguro de eliminar este cupón?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $cupon->id }})"
                                class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                                <i class="fas fa-trash-alt text-base"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-12 text-gray-400">
                            <i class="fas fa-ticket-alt text-5xl mb-3"></i>
                            <p>No hay cupones registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $cupones->links() }}
        </div>
    </div>

    {{-- MODAL DE CREACIÓN/EDICIÓN --}}
    <div x-show="$wire.isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.isOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white text-xl">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white"
                                    x-text="$wire.cupon_id ? 'Editar Cupón' : 'Nuevo Cupón'"></h3>
                                <p class="text-xs text-blue-100">Configura las reglas del cupón de descuento</p>
                            </div>
                        </div>
                        <button type="button" @click="$wire.isOpen = false" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 text-left">
                    <div class="space-y-4">
                        {{-- Código --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código del Cupón <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="codigo" placeholder="EJ. VERANO20"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
                            @error('codigo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Tipo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Rebaja <span class="text-red-500">*</span></label>
                                <select wire:model.live="tipo"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="fijo">Monto Fijo (Bs.)</option>
                                    <option value="porcentaje">Porcentaje (%)</option>
                                </select>
                            </div>

                            {{-- Valor --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="$wire.tipo === 'porcentaje' ? 'Porcentaje (%) *' : 'Monto (Bs.) *'"></label>
                                <input type="number" step="0.01" wire:model="valor"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('valor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Compra mínima --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Compra Mínima (Bs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" wire:model="compra_minima"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('compra_minima') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Límite de uso --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Límite de Usos (Max)</label>
                                <input type="number" wire:model="limite_uso" placeholder="Ilimitado si está vacío"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('limite_uso') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Fecha Expiración --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Expiración</label>
                            <input type="date" wire:model="fecha_expiracion"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('fecha_expiracion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Activo --}}
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model="activo"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Habilitar cupón de inmediato</span>
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
                        <span x-text="$wire.cupon_id ? 'Actualizar Cupón' : 'Crear Cupón'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
