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
        <h2 class="text-2xl font-bold text-gray-800">📦 Gestión de Productos</h2>
        <button wire:click="create" wire:loading.attr="disabled"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2 disabled:opacity-50">
            <i class="fas fa-plus" wire:loading.remove wire:target="create"></i>
            <i class="fas fa-spinner fa-spin" wire:loading wire:target="create"></i>
            <span>Nuevo Producto</span>
        </button>
    </div>


    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-search text-gray-400 mr-1"></i> Buscar
                </label>
                <input wire:model.live="search" type="text" placeholder="Buscar por nombre o SKU..."
                    class="w-full border rounded-lg p-2 pl-9 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-filter text-gray-400 mr-1"></i> Filtrar por Categoría
                </label>
                <select wire:model.live="categoria_id"
                    class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Imagen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio/Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($productos as $producto)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <img src="{{ $producto->avatar_ruta ? asset('storage/'.$producto->avatar_ruta) : 'https://via.placeholder.com/50' }}"
                                class="w-12 h-12 rounded object-cover shadow-sm">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800">{{ $producto->nombre }}</div>
                            @if($producto->tiene_3d)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                <i class="fas fa-cube mr-1 text-xs"></i> 3D CONFIGURADO
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded">{{ $producto->sku }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-green-600">Bs. {{ number_format($producto->precio, 2) }}
                            </div>
                            <div
                                class="text-xs {{ $producto->stock <= 5 ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                <i class="fas fa-boxes mr-1"></i> Stock: {{ $producto->stock }}
                                @if($producto->stock <= 5 && $producto->stock > 0)
                                    <span class="text-orange-500 ml-1">(Bajo stock)</span>
                                    @elseif($producto->stock == 0)
                                    <span class="text-red-500 ml-1">(Agotado)</span>
                                    @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button wire:click="edit({{ $producto->id }})" wire:loading.attr="disabled"
                                class="text-blue-600 hover:text-blue-800 mr-3 transition disabled:opacity-50"
                                title="Editar">
                                <i class="fas fa-edit text-lg"></i>
                            </button>
                            <button
                                onclick="confirm('¿Eliminar este producto junto con todos sus archivos?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $producto->id }})"
                                class="text-red-600 hover:text-red-800 transition" title="Eliminar">
                                <i class="fas fa-trash-alt text-lg"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400">
                            <i class="fas fa-box-open text-5xl mb-3"></i>
                            <p>No hay productos registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $productos->links() }}
        </div>
    </div>


    <!-- MODAL CORREGIDO: x-show usa $wire.isOpen directamente -->
    <div x-show="$wire.isOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.isOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>


            <div
                class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas text-white text-xl"
                                    :class="$wire.producto_id ? 'fa-edit' : 'fa-box-open'"></i>
                            </div>
                            <div class="text-left">
                                <h3 class="text-lg font-semibold text-white"
                                    x-text="$wire.producto_id ? 'Editar Producto' : 'Nuevo Producto'"></h3>
                                <p class="text-xs text-blue-100"
                                    x-text="$wire.producto_id ? 'Modifica los datos del producto' : 'Completa los datos para crear un nuevo producto'">
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="$wire.isOpen = false"
                            class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>


                <div class="max-h-[65vh] overflow-y-auto px-6 py-4 text-left">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-tag text-gray-400 text-sm"></i></div>
                                <input type="text" wire:model="nombre"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-barcode text-gray-400 text-sm"></i></div>
                                <input type="text" wire:model="sku"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            @error('sku') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-folder text-gray-400 text-sm"></i></div>
                                <select wire:model="categoria_id"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('categoria_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio (Bs.) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-dollar-sign text-gray-400 text-sm"></i></div>
                                <input type="number" step="0.01" wire:model="precio"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            @error('precio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                        class="fas fa-boxes text-gray-400 text-sm"></i></div>
                                <input type="number" wire:model="stock"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>


                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 pointer-events-none"><i
                                        class="fas fa-align-left text-gray-400 text-sm"></i></div>
                                <textarea wire:model="descripcion" rows="2"
                                    class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                            </div>
                        </div>


                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen de Portada</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 text-center bg-gray-50">
                                @if($imagen)
                                <img src="{{ $imagen->temporaryUrl() }}"
                                    class="w-20 h-20 object-cover rounded-lg shadow mx-auto mb-2">
                                @elseif($imagen_actual)
                                <img src="{{ asset('storage/'.$imagen_actual) }}"
                                    class="w-20 h-20 object-cover rounded-lg shadow mx-auto mb-2">
                                @endif
                                <input type="file" wire:model="imagen" accept="image/*" class="text-xs mx-auto block">
                                <div wire:loading wire:target="imagen" class="text-blue-500 text-xs font-semibold mt-1">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando imagen...
                                </div>
                                @error('imagen') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="md:col-span-2 flex flex-wrap gap-4 py-1">
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model="activo"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Producto Activo</span>
                            </label>
                            <label class="flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="tiene_3d"
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Soporta Modelo 3D</span>
                            </label>
                        </div>


                        <div class="md:col-span-2 p-4 bg-purple-50 border border-purple-200 rounded-xl"
                            x-show="$wire.tiene_3d" transition>
                            <label class="block text-sm font-bold text-purple-900 mb-1"><i class="fas fa-cube mr-1"></i>
                                Archivo Tridimensional (.glb / .gltf) <span class="text-red-500">*</span></label>
                            <input type="file" wire:model="file_3d" accept=".glb,.gltf"
                                class="w-full text-xs text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">


                            <div wire:loading wire:target="file_3d" class="text-purple-600 text-xs font-bold mt-2">
                                <i class="fas fa-circle-notch fa-spin mr-1"></i> Transfiriendo render 3D al servidor
                                local...
                            </div>
                            @if($model_3d_actual)
                            <p class="text-[11px] text-purple-700 font-mono mt-1"><i class="fas fa-paperclip mr-1"></i>
                                Guardado: {{ basename($model_3d_actual) }}</p>
                            @endif
                            @error('file_3d') <span
                                class="text-red-500 text-xs block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>


                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2 border-t">
                    <button type="button" @click="$wire.isOpen = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="store" wire:loading.attr="disabled"
                        wire:target="imagen, file_3d, store"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save" wire:loading.remove wire:target="store"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="store"></i>
                        <span x-text="$wire.producto_id ? 'Actualizar Producto' : 'Crear Producto'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
