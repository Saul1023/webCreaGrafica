<div>
    <div class="bg-white rounded-xl shadow-md p-6">
        <!-- ELIMINA ESTE TÍTULO INTERNO -->
        <!-- <h2 class="text-2xl font-bold text-gray-800 mb-6">
            {{ $isEditing ? 'Editar Producto' : 'Crear Nuevo Producto' }}
        </h2> -->

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Nombre y Categoría -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Producto *
                    </label>
                    <input type="text" id="name" wire:model="name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Ej: Taza Pareja Romántica">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Categoría *
                    </label>
                    <select id="category" wire:model="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                        <option value="">Selecciona categoría</option>
                        <option value="parejas">Parejas</option>
                        <option value="niños">Niños</option>
                        <option value="graduacion">Graduación</option>
                        <option value="personalizado">Personalizado</option>
                        <option value="otros">Otros</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción *
                </label>
                <textarea id="description" wire:model="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                          placeholder="Describe el producto..."></textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Precio, Stock e Imagen -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Precio (Bs) *
                    </label>
                    <input type="number" id="price" wire:model="price" step="0.01" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-500 @enderror"
                           placeholder="0.00">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                        Stock *
                    </label>
                    <input type="number" id="stock" wire:model="stock" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror"
                           placeholder="0">
                    @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen
                        @if($isEditing)
                            <span class="text-gray-500 text-xs">(vacío = mantener)</span>
                        @endif
                    </label>
                    <input type="file" id="image" wire:model="image"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror"
                           accept="image/*">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Opciones y Botones -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-gray-200">
                <div class="flex space-x-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="featured" wire:model="featured"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="featured" class="ml-2 text-sm text-gray-700">
                            Destacado
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="active" wire:model="active"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="active" class="ml-2 text-sm text-gray-700">
                            Activo
                        </label>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button type="button" wire:click="resetForm"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                        Limpiar
                    </button>

                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:bg-blue-400 disabled:cursor-not-allowed text-sm">
                        <span wire:loading.remove>
                            {{ $isEditing ? 'Actualizar' : 'Crear Producto' }}
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin mr-1"></i>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>

            <!-- Vista previa de imagen -->
            @if($image)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Vista previa:</p>
                    <img src="{{ $image->temporaryUrl() }}"
                         alt="Vista previa" class="w-24 h-24 object-cover rounded-lg border">
                </div>
            @endif
        </form>
    </div>
</div>
