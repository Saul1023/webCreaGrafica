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
                <span>🏢 Directorio de Proveedores</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Registra y administra las empresas o contratistas de insumos para CREAGRAFICA.
            </p>
        </div>

        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition flex items-center gap-2 text-sm shadow-md font-semibold">
            <i class="fas fa-plus"></i>
            <span>Nuevo Proveedor</span>
        </button>
    </div>

    {{-- Panel de Filtros y Búsqueda --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Buscar Proveedor</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, NIT, contacto o correo..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Estado</label>
                <select wire:model.live="filtro_activo" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabla de Proveedores --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b bg-gray-50/50">
                        <th class="px-6 py-3.5">Proveedor</th>
                        <th class="px-6 py-3.5">NIT/CI</th>
                        <th class="px-6 py-3.5">Contacto</th>
                        <th class="px-6 py-3.5">Contacto Rápido</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proveedores as $prov)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $prov->nombre }}</div>
                                <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $prov->direccion ?: 'Sin dirección registrada' }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-600">
                                {{ $prov->nit ?: 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-700">{{ $prov->contacto_nombre ?: 'Sin especificar' }}</div>
                                <div class="text-xs text-gray-400">{{ $prov->correo ?: 'Sin correo' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($prov->telefono)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <i class="fas fa-phone text-[10px]"></i> {{ $prov->telefono }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">No disponible</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($prov->activo)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-750 border border-gray-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $prov->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $prov->id }}, '{{ $prov->nombre }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                <i class="fas fa-folder-open text-4xl block mb-2"></i>
                                No se encontraron proveedores registrados en el directorio.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $proveedores->links() }}
        </div>
    </div>

    {{-- MODAL: NUEVO / EDITAR PROVEEDOR --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            {{-- Fondo oscuro --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            {{-- Contenedor del Modal --}}
            <div class="relative bg-white rounded-2xl max-w-lg w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 my-8 animate-fadeIn">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-building"></i>
                        <span>{{ $proveedor_id ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</span>
                    </h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="store" class="space-y-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nombre / Razón Social *</label>
                            <input type="text" wire:model="nombre" placeholder="Nombre comercial o social" 
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('nombre') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- NIT y Contacto --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">NIT / CI</label>
                                <input type="text" wire:model="nit" placeholder="Identificación tributaria" 
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('nit') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nombre de Contacto</label>
                                <input type="text" wire:model="contacto_nombre" placeholder="Representante de ventas" 
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('contacto_nombre') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Teléfono y Correo --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Teléfono</label>
                                <input type="text" wire:model="telefono" placeholder="Número telefónico" 
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('telefono') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Correo Electrónico</label>
                                <input type="email" wire:model="correo" placeholder="correo@empresa.com" 
                                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('correo') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Dirección --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Dirección Física</label>
                            <textarea wire:model="direccion" rows="2" placeholder="Ubicación de la oficina o planta de despacho" 
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('direccion') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Activo --}}
                        <div class="flex items-center gap-2 pt-2">
                            <input type="checkbox" wire:model="activo" id="activo" 
                                class="w-4.5 h-4.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="activo" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">Proveedor activo para transacciones</label>
                        </div>

                        {{-- Botones --}}
                        <div class="flex justify-end gap-3 pt-4 border-t mt-6">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-lg border text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button type="submit" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: CONFIRMAR ELIMINACIÓN --}}
    @if($isDeleteOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeDeleteModal"></div>

            <div class="relative bg-white rounded-2xl max-w-md w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 animate-fadeIn">
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 text-red-600 mb-4">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-950 mb-2">Eliminar Proveedor</h3>
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que deseas eliminar al proveedor <strong>{{ $proveedor_para_eliminar_nombre }}</strong>? Esta acción no se puede deshacer si no tiene compras relacionadas.
                        </p>
                    </div>

                    <div class="flex justify-center gap-3 pt-6 border-t mt-6">
                        <button type="button" wire:click="closeDeleteModal" class="px-5 py-2.5 rounded-lg border text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="button" wire:click="deleteProveedor" class="px-6 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                            Confirmar Eliminar
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
