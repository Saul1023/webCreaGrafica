<div>
    <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="w-full md:w-1/3 relative">
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar productos..."
                       class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 justify-center md:justify-end">
                <button wire:click="selectCategory('todos')"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $selectedCategory === 'todos' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Todos
                </button>
                @foreach($categories as $category)
                <button wire:click="selectCategory('{{ $category }}')"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-all {{ $selectedCategory === $category ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    {{ ucfirst($category) }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    @if($product->image)
                        {{-- CORRECCIÓN DE RUTA DE IMAGEN --}}
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fas fa-image text-5xl"></i>
                        </div>
                    @endif

                    @if($product->featured)
                        <span class="absolute top-2 left-2 bg-yellow-400 text-white px-2 py-1 rounded-full text-xs font-bold shadow-sm flex items-center">
                            <i class="fas fa-star mr-1"></i> Top
                        </span>
                    @endif

                    @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center backdrop-blur-sm">
                            <span class="text-white font-bold text-lg border-2 border-white px-4 py-1 rounded uppercase tracking-wide">Agotado</span>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase tracking-wide">
                            {{ $product->category }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mb-2 truncate" title="{{ $product->name }}">{{ $product->name }}</h3>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2 h-10">{{ $product->description }}</p>

                    <div class="flex justify-between items-end border-t pt-4 border-gray-100">
                        <div>
                            <span class="block text-xs text-gray-400">Precio</span>
                            <span class="text-xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        </div>

                        <button wire:click="addToCart({{ $product->id }})"
                                wire:loading.attr="disabled"
                                class="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors shadow-lg disabled:bg-gray-300 disabled:cursor-not-allowed group-hover:bg-blue-700"
                                {{ $product->stock <= 0 ? 'disabled' : '' }}
                                title="Añadir al carrito">
                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">
                                <i class="fas fa-cart-plus"></i>
                            </span>
                            <span wire:loading wire:target="addToCart({{ $product->id }})">
                                <i class="fas fa-spinner fa-spin text-sm"></i>
                            </span>
                        </button>
                    </div>

                    @if($product->stock > 0 && $product->stock <= 5)
                        <p class="text-orange-500 text-xs mt-2 font-medium">
                            <i class="fas fa-fire mr-1"></i> ¡Solo quedan {{ $product->stock }}!
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-sm border border-dashed border-gray-300">
                <div class="inline-block p-4 rounded-full bg-gray-50 mb-4">
                    <i class="fas fa-search text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No encontramos productos</h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    Intenta ajustar tus filtros de búsqueda o selecciona otra categoría.
                </p>
                <button wire:click="$set('search', '')" class="mt-4 text-blue-600 font-medium hover:underline">
                    Limpiar filtros
                </button>
            </div>
        @endforelse
    </div>

    @if($products->hasMorePages())
        <div class="text-center py-8">
            <button wire:click="loadMore"
                    wire:loading.attr="disabled"
                    class="bg-white border border-gray-300 text-gray-700 px-8 py-3 rounded-full font-medium hover:bg-gray-50 hover:shadow-md transition-all disabled:opacity-50">
                <span wire:loading.remove>Ver más productos</span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                </span>
            </button>
        </div>
    @endif

    @if (session()->has('message'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center">
            <div class="bg-green-500 rounded-full p-1 mr-3">
                <i class="fas fa-check text-xs text-white"></i>
            </div>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
