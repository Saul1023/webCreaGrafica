@extends('layouts.app')

@section('title', 'CREAGRAFICA - Momentos Personalizados')

@section('content')
<header class="relative bg-gray-900 h-[85vh] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
            alt="Fondo Creagrafica" class="w-full h-full object-cover opacity-60">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
    </div>

    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
        <span
            class="inline-block py-1 px-3 rounded-full bg-blue-600/30 border border-blue-400 text-blue-100 text-sm font-semibold tracking-wide mb-4 animate-fade-in-up">
            PERSONALIZACIÓN DE ALTA CALIDAD
        </span>
        <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
            Tus Ideas, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">Hechas
                Realidad</span>
        </h1>
        <p class="text-lg md:text-2xl text-gray-200 mb-10 max-w-2xl mx-auto font-light">
            Transformamos tazas, camisetas y accesorios en regalos únicos llenos de significado y creatividad.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="#productos"
                class="group bg-blue-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/50 flex items-center">
                Ver Catálogo
                <i class="fas fa-arrow-down ml-2 group-hover:translate-y-1 transition-transform"></i>
            </a>
            <a href="#nosotros"
                class="px-8 py-4 rounded-full font-bold text-lg text-white border-2 border-white/30 hover:bg-white/10 transition-all backdrop-blur-sm">
                Conócenos
            </a>
        </div>
    </div>
</header>

<main>
    @php
    $productosEnOferta = \App\Models\Producto::where('activo', true)
        ->enOferta()
        ->orderBy('id', 'desc')
        ->get();
    @endphp

    @if($productosEnOferta->count() > 0)
    <section class="py-20 bg-gradient-to-b from-gray-900 to-slate-900 text-white overflow-hidden relative">
        <div class="absolute inset-0 opacity-5 pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-500 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-purple-500 blur-3xl"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <span class="inline-block py-1 px-3 rounded-full bg-red-500/20 border border-red-500/30 text-red-300 text-xs font-bold uppercase tracking-wider mb-3 animate-pulse">
                    🔥 Grandes Descuentos
                </span>
                <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight">Ofertas Especiales</h2>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto text-sm md:text-base font-light">
                    ¡Aprovecha nuestras promociones exclusivas por tiempo limitado en artículos personalizables premium!
                </p>
            </div>

            <div x-data="{ 
                activeIndex: 0, 
                itemsCount: {{ $productosEnOferta->count() }}, 
                width: window.innerWidth, 
                init() { 
                    window.addEventListener('resize', () => this.width = window.innerWidth); 
                    setInterval(() => { this.next() }, 5000); 
                }, 
                next() { 
                    let perView = this.width < 768 ? 1 : (this.width < 1024 ? 2 : 3);
                    let maxIndex = Math.max(0, this.itemsCount - perView);
                    this.activeIndex = this.activeIndex >= maxIndex ? 0 : this.activeIndex + 1; 
                }, 
                prev() { 
                    let perView = this.width < 768 ? 1 : (this.width < 1024 ? 2 : 3);
                    let maxIndex = Math.max(0, this.itemsCount - perView);
                    this.activeIndex = this.activeIndex <= 0 ? maxIndex : this.activeIndex - 1; 
                } 
            }" class="relative px-4">
                
                {{-- Carousel Track --}}
                <div class="overflow-hidden rounded-2xl">
                    <div class="flex transition-transform duration-500 ease-out -mx-3"
                         :style="'transform: translateX(-' + (activeIndex * (100 / (width < 768 ? 1 : (width < 1024 ? 2 : 3)))) + '%)'">
                        
                        @foreach($productosEnOferta as $prod)
                            <div class="w-full md:w-1/2 lg:w-1/3 flex-shrink-0 px-3">
                                <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl overflow-hidden hover:border-blue-500/50 hover:shadow-2xl hover:shadow-blue-500/5 transition-all duration-300 group flex flex-col h-full backdrop-blur-sm">
                                    {{-- Image --}}
                                    <div class="relative overflow-hidden bg-gray-950 h-56 flex items-center justify-center border-b border-gray-700/50">
                                        @if($prod->avatar_ruta)
                                            <img src="{{ asset('storage/' . $prod->avatar_ruta) }}" alt="{{ $prod->nombre }}"
                                                class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                                        @else
                                            <i class="fas fa-image text-4xl text-gray-700"></i>
                                        @endif

                                        {{-- Discount Badge --}}
                                        @php
                                            $ofertaActiva = $prod->oferta_activa;
                                            $porcentaje = $ofertaActiva ? round($ofertaActiva->descuento) : 0;
                                        @endphp
                                        @if($ofertaActiva)
                                        <span class="absolute top-3 left-3 bg-red-600 text-white text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-md" title="{{ $ofertaActiva->nombre }}">
                                            -{{ $porcentaje }}% OFF
                                        </span>
                                        @endif

                                        @if($prod->tiene_3d)
                                            <span class="absolute top-3 right-3 bg-purple-600/80 backdrop-blur-sm text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow flex items-center gap-1">
                                                <i class="fas fa-cube text-[8px]"></i> 3D
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Body --}}
                                    <div class="p-6 flex flex-col flex-grow">
                                        <span class="text-xs font-bold text-blue-400 tracking-wide uppercase mb-1">{{ $prod->categoria->nombre ?? 'Oferta' }}</span>
                                        <h3 class="text-lg font-bold text-white mb-2 line-clamp-1 group-hover:text-blue-400 transition">{{ $prod->nombre }}</h3>
                                        <p class="text-gray-400 text-xs line-clamp-2 mb-4 leading-relaxed flex-grow">
                                            {{ $prod->descripcion ?? 'Producto personalizable premium en oferta especial.' }}
                                        </p>

                                        {{-- Prices --}}
                                        <div class="flex items-center justify-between border-t border-gray-700/50 pt-4 mt-auto">
                                            <div>
                                                <span class="text-xs text-gray-500 line-through block">Bs. {{ number_format($prod->precio, 2) }}</span>
                                                <span class="text-2xl font-black text-red-500 block">Bs. {{ number_format($prod->precio_final, 2) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <a href="{{ route('cliente.dashboard', ['producto_seleccionado_id' => $prod->id]) }}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center gap-1.5">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>Comprar</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                {{-- Navigation Arrows (hidden if itemsCount <= perView) --}}
                <template x-if="itemsCount > (width < 768 ? 1 : (width < 1024 ? 2 : 3))">
                    <div>
                        <button @click="prev()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white flex items-center justify-center transition shadow-lg z-20">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>
                        <button @click="next()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white flex items-center justify-center transition shadow-lg z-20">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </section>
    @endif

    <section id="productos" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Nuestros Productos Destacados</h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-blue-500 to-purple-600 mx-auto rounded-full"></div>
                <p class="text-gray-600 mt-6 max-w-2xl mx-auto text-lg">
                    Explora nuestra colección. Cada pieza es elaborada con materiales premium y técnicas de sublimación
                    de última generación.
                </p>
            </div>

            {{-- Buscador de Productos Premium --}}
            <div class="max-w-xl mx-auto mb-16">
                <form action="{{ url('/#productos') }}" method="GET" class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="¿Qué estás buscando? (ej: Taza, Polera...)" 
                        class="w-full pl-11 pr-32 py-3.5 bg-white border border-gray-250 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition">
                    <button type="submit" 
                        class="absolute right-2 top-2 bottom-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 rounded-xl text-xs transition">
                        Buscar
                    </button>
                </form>
                @if(request('search'))
                    <div class="text-center mt-3 text-sm text-gray-500 flex items-center justify-center gap-2">
                        <span>Resultados de búsqueda para: <strong>"{{ request('search') }}"</strong></span>
                        <a href="{{ url('/#productos') }}" class="text-red-500 hover:underline text-xs flex items-center gap-1">
                            <i class="fas fa-times-circle"></i> Limpiar búsqueda
                        </a>
                    </div>
                @endif
            </div>

            @php
            $search = request('search');
            $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
            $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

            $categoriasConProductos = \App\Models\CategoriaProducto::with(['productos' => function ($q) use ($search, $likeOperator) {
                $q->where('activo', true);
                if (!empty($search)) {
                    $q->where(function ($sub) use ($search, $likeOperator) {
                        $sub->where('nombre', $likeOperator, '%' . $search . '%')
                            ->orWhere('sku', $likeOperator, '%' . $search . '%')
                            ->orWhere('descripcion', $likeOperator, '%' . $search . '%');
                    });
                }
                $q->orderBy('id', 'desc');
            }])
            ->orderBy('orden')
            ->get()
            ->filter(function ($cat) {
                return $cat->productos->count() > 0;
            });
            @endphp

            @forelse($categoriasConProductos as $cat)
            <div class="mb-16 last:mb-0">
                <div class="flex items-center justify-between mb-8 border-b pb-4 border-gray-200">
                    <h3 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-blue-600 text-lg"></i>
                        <span>{{ $cat->nombre }}</span>
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                            {{ $cat->productos->count() }} {{ $cat->productos->count() === 1 ? 'producto' : 'productos' }}
                        </span>
                    </h3>
                </div>

                {{-- Carrusel Alpine.js --}}
                <div x-data="{ 
                    activeIndex: 0, 
                    itemsCount: {{ $cat->productos->count() }}, 
                    width: window.innerWidth, 
                    init() { 
                        window.addEventListener('resize', () => this.width = window.innerWidth); 
                    }, 
                    next() { 
                        let perView = this.width < 640 ? 1 : (this.width < 1024 ? 2 : 3);
                        let maxIndex = Math.max(0, this.itemsCount - perView);
                        this.activeIndex = this.activeIndex >= maxIndex ? 0 : this.activeIndex + 1; 
                    }, 
                    prev() { 
                        let perView = this.width < 640 ? 1 : (this.width < 1024 ? 2 : 3);
                        let maxIndex = Math.max(0, this.itemsCount - perView);
                        this.activeIndex = this.activeIndex <= 0 ? maxIndex : this.activeIndex - 1; 
                    } 
                }" class="relative px-2">
                    
                    {{-- Carousel Track --}}
                    <div class="overflow-hidden rounded-2xl">
                        <div class="flex transition-transform duration-500 ease-out -mx-3"
                             :style="'transform: translateX(-' + (activeIndex * (100 / (width < 640 ? 1 : (width < 1024 ? 2 : 3)))) + '%)'">
                            
                            @foreach($cat->productos as $prod)
                                <div class="w-full sm:w-1/2 lg:w-1/3 flex-shrink-0 px-3">
                                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                                        {{-- Imagen --}}
                                        <div class="relative overflow-hidden bg-gray-50 h-60 flex items-center justify-center border-b">
                                            @if($prod->avatar_ruta)
                                                <img src="{{ asset('storage/' . $prod->avatar_ruta) }}" alt="{{ $prod->nombre }}"
                                                    class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                                            @else
                                                <i class="fas fa-image text-5xl text-gray-305 text-gray-300"></i>
                                            @endif

                                            @if($prod->tiene_3d)
                                                <span class="absolute top-3 right-3 bg-purple-650 bg-purple-600 text-white text-[10px] font-black tracking-wider uppercase px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                                                    <i class="fas fa-cube"></i> Ver en 3D
                                                </span>
                                            @endif

                                            @php
                                                $ofertaActiva = $prod->oferta_activa;
                                                $porcentaje = $ofertaActiva ? round($ofertaActiva->descuento) : 0;
                                            @endphp
                                            @if($ofertaActiva)
                                                <span class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full shadow">
                                                    -{{ $porcentaje }}% OFF
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Contenido --}}
                                        <div class="p-5 flex flex-col flex-grow">
                                            <h4 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition truncate">{{ $prod->nombre }}</h4>
                                            <p class="text-gray-500 text-xs line-clamp-2 mb-4 leading-relaxed flex-grow">
                                                {{ $prod->descripcion ?? 'Producto personalizable de alta calidad.' }}
                                            </p>
                                            <div class="flex items-center justify-between border-t pt-4 mt-auto">
                                                <div>
                                                    @if($prod->en_oferta)
                                                        <span class="text-xs text-gray-400 line-through block">Bs. {{ number_format($prod->precio, 2) }}</span>
                                                        <span class="text-xl font-black text-red-600 block">Bs. {{ number_format($prod->precio_final, 2) }}</span>
                                                    @else
                                                        <span class="text-xl font-black text-gray-900 block">Bs. {{ number_format($prod->precio, 2) }}</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('cliente.dashboard', ['producto_seleccionado_id' => $prod->id]) }}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm hover:shadow-md flex items-center gap-1.5">
                                                    <i class="fas fa-shopping-cart"></i>
                                                    <span>Comprar</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- Flechas de control --}}
                    <template x-if="itemsCount > (width < 640 ? 1 : (width < 1024 ? 2 : 3))">
                        <div>
                            <button @click="prev()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-9 h-9 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 flex items-center justify-center transition shadow-md z-20">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </button>
                            <button @click="next()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-9 h-9 rounded-full bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 flex items-center justify-center transition shadow-md z-20">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-150 p-12 text-center max-w-xl mx-auto">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4 font-light"></i>
                <h3 class="text-lg font-bold text-gray-800">No se encontraron productos</h3>
                <p class="text-gray-500 text-sm mt-1">Intenta buscar con otros términos o limpia el buscador.</p>
            </div>
            @endforelse

        </div>
    </section>

    <section class="py-16 bg-blue-900 text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-10">
            <i class="fas fa-mug-hot absolute top-10 left-10 text-9xl transform -rotate-12"></i>
            <i class="fas fa-tshirt absolute bottom-10 right-10 text-9xl transform rotate-12"></i>
        </div>
        <div class="max-w-7xl mx-auto px-4 relative z-10 flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0 text-center md:text-left">
                <h3 class="text-3xl font-bold mb-2">¿Tienes un diseño en mente?</h3>
                <p class="text-blue-200">Podemos personalizar cualquier producto con tu logo o imagen.</p>
            </div>
            <a href="https://wa.me/59169608947" target="_blank"
                class="bg-white text-blue-900 px-8 py-3 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg flex items-center">
                <i class="fab fa-whatsapp mr-2 text-xl"></i> Cotizar Personalizado
            </a>
        </div>
    </section>

    <section id="nosotros" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">

                <div class="lg:w-1/2 relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-100 rounded-full z-0"></div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-purple-100 rounded-full z-0"></div>
                    <img src="https://images.unsplash.com/photo-1556740738-b6a63e27c4df?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        alt="Taller Creagrafica"
                        class="relative z-10 rounded-2xl shadow-2xl w-full object-cover h-[400px] lg:h-[500px]">

                    <div
                        class="absolute -bottom-8 left-8 z-20 bg-white p-6 rounded-xl shadow-xl border-l-4 border-blue-600 hidden md:block">
                        <p class="text-4xl font-bold text-gray-800">100%</p>
                        <p class="text-sm text-gray-500 font-medium">Calidad Garantizada</p>
                    </div>
                </div>

                <div class="lg:w-1/2">
                    <span class="text-blue-600 font-bold tracking-wider uppercase text-sm">Sobre Nosotros</span>
                    <h2 class="text-4xl font-bold text-gray-900 mt-2 mb-6">Pasión por el Detalle y la Creatividad</h2>

                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        En <span class="font-bold text-gray-800">CREAGRAFICA</span>, no solo imprimimos productos,
                        creamos recuerdos. Nos specializeamos en el arte de la sublimación, asegurando que cada taza,
                        camiseta o accesorio tenga colores vibrantes y duraderos.
                    </p>

                    <p class="text-gray-600 mb-8 text-lg leading-relaxed">
                        Nuestro equipo de diseñadores trabaja mano a mano contigo para transformar tus fotos y logotipos
                        en regalos que emocionan.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-start">
                            <div class="bg-blue-50 p-3 rounded-lg text-blue-600 mr-4">
                                <i class="fas fa-award text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Calidad Premium</h4>
                                <p class="text-sm text-gray-500">Materiales seleccionados para durar.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-purple-50 p-3 rounded-lg text-purple-600 mr-4">
                                <i class="fas fa-paint-brush text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Diseños Únicos</h4>
                                <p class="text-sm text-gray-500">Creatividad sin límites para ti.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>
@endsection
