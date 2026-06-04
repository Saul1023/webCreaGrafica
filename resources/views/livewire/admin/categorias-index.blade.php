<div>
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

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">📁 Categorías de Productos</h2>

        <div class="flex gap-4 w-full md:w-auto">
            <div class="relative flex-grow md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input wire:model.live="search" type="text" placeholder="Buscar categoría..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <button wire:click="create"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-plus"></i>
                <span>Nueva Categoría</span>
            </button>
        </div>
    </div>

    <!-- Tabla de categorías -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orden
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categorias as $categoria)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-500">#{{ $categoria->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                                {{ substr($categoria->nombre, 0, 1) }}
                            </div>
                            <span class="ml-3 font-medium text-gray-800">{{ $categoria->nombre }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $categoria->slug }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $categoria->orden }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-box mr-1 text-xs"></i> {{ $categoria->productos()->count() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <button wire:click="edit({{ $categoria->id }})"
                            class="text-blue-600 hover:text-blue-800 mr-3 transition" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                            class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-folder-open text-5xl mb-3"></i>
                        <p>No hay categorías registradas</p>
                        <button wire:click="create" class="mt-2 text-blue-600 hover:text-blue-800 transition">
                            <i class="fas fa-plus mr-1"></i> Crear la primera categoría
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $categorias->links() }}
        </div>
    </div>

    <!-- MODAL MEJORADO PARA CREAR/EDITAR CATEGORÍA -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <!-- Header con gradiente -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i
                                    class="fas {{ $categoria_id ? 'fa-edit' : 'fa-folder-plus' }} text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    {{ $categoria_id ? 'Editar Categoría' : 'Nueva Categoría' }}
                                </h3>
                                <p class="text-xs text-blue-100">
                                    {{ $categoria_id ? 'Modifica los datos de la categoría' : 'Completa los datos para crear una nueva categoría' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="px-6 py-5 space-y-4">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Categoría <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-tag text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" wire:model="nombre"
                                class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Ej: Tazas Personalizadas">
                        </div>
                        @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug (se genera automáticamente) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Slug <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-link text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" wire:model="slug"
                                class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="tazas-personalizadas">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Identificador único para la URL (se genera
                            automáticamente)</p>
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Orden -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Orden de Visualización
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-sort-numeric-down text-gray-400 text-sm"></i>
                            </div>
                            <input type="number" wire:model="orden"
                                class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="0">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Número de orden (menor número = aparece primero)</p>
                        @error('orden') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Footer con botones -->
                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="store" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-save" wire:loading.remove></i>
                        <i class="fas fa-spinner fa-spin" wire:loading></i>
                        {{ $categoria_id ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL CONFIRMAR ELIMINAR MEJORADO -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="p-6 text-center">
                    <div class="mx-auto w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmar Eliminación</h3>
                    <p class="text-sm text-gray-500">
                        ¿Estás seguro de eliminar la categoría
                        <strong class="text-red-600">{{ $categoria_nombre_eliminar }}</strong>?
                    </p>
                    <p class="text-xs text-gray-400 mt-2">Esta acción no se puede deshacer.</p>
                </div>
                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2">
                    <button wire:click="closeDeleteModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteCategoria"
                        class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                        <i class="fas fa-trash-alt mr-1"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
