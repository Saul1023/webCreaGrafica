<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Notificaciones de Éxito / Error --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-lg text-emerald-800 flex items-center justify-between shadow-sm animate-fadeIn">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-xl text-emerald-600"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
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

    {{-- Encabezado del Portal --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800">Mi Portal de Cliente 🛍️</h1>
            <p class="text-gray-500 text-sm mt-1">Explora productos únicos, personaliza tus artículos y sigue el estado de tus pedidos.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="selectTab('catalogo')" 
                class="px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 {{ $active_tab === 'catalogo' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                <i class="fas fa-store"></i>
                <span>Catálogo</span>
            </button>
            <button wire:click="selectTab('pedidos')" 
                class="px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 {{ $active_tab === 'pedidos' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                <i class="fas fa-shopping-bag"></i>
                <span>Mis Pedidos</span>
            </button>
            <button wire:click="selectTab('perfil')" 
                class="px-5 py-2.5 rounded-lg text-sm font-bold transition flex items-center gap-2 {{ $active_tab === 'perfil' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-600 border hover:bg-gray-50' }}">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </button>
        </div>
    </div>

    {{-- CONTENIDO: CATÁLOGO DE PRODUCTOS --}}
    @if($active_tab === 'catalogo')
        <div class="grid grid-cols-1 {{ empty($carrito) ? '' : 'lg:grid-cols-3' }} gap-8 items-start">
            {{-- Columna izquierda: Catálogo y Filtros --}}
            <div class="{{ empty($carrito) ? '' : 'lg:col-span-2' }} space-y-6">
                {{-- Filtros y Búsqueda --}}
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:w-96">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, SKU o palabra clave..." 
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider mr-2">Categoría:</span>
                        <button wire:click="$set('categoria_filtro', '')" 
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition {{ $categoria_filtro === '' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            Todas
                        </button>
                        @foreach($categorias as $cat)
                            <button wire:click="$set('categoria_filtro', '{{ $cat->id }}')" 
                                class="px-3 py-1.5 rounded-full text-xs font-bold transition {{ $categoria_filtro == $cat->id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $cat->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Grilla de Productos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 {{ empty($carrito) ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-6">
                    @forelse($productos as $prod)
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group flex flex-col h-full">
                            {{-- Imagen o Placeholder --}}
                            <div class="relative overflow-hidden bg-gray-50 h-64 flex items-center justify-center border-b">
                                @if($prod->avatar_ruta)
                                    <img src="{{ asset('storage/' . $prod->avatar_ruta) }}" alt="{{ $prod->nombre }}"
                                        class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <i class="fas fa-image text-5xl text-gray-300"></i>
                                @endif

                                @if($prod->tiene_3d)
                                    <span class="absolute top-3 right-3 bg-purple-600 text-white text-[10px] font-black tracking-wider uppercase px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                                        <i class="fas fa-cube"></i> 3D Disponible
                                    </span>
                                @endif
                            </div>

                            {{-- Cuerpo de la Tarjeta --}}
                            <div class="p-6 flex flex-col flex-grow">
                                <span class="text-xs font-bold text-blue-600 tracking-wider uppercase mb-1">{{ $prod->categoria->nombre ?? 'Sublimación' }}</span>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 leading-snug group-hover:text-blue-600 transition">{{ $prod->nombre }}</h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-3 leading-relaxed flex-grow">{{ $prod->descripcion ?: 'Producto premium listo para personalizar.' }}</p>

                                {{-- Precios y Stock --}}
                                <div class="flex items-center justify-between border-t pt-4 mt-auto">
                                    <div>
                                        @if($prod->en_oferta)
                                            <span class="text-xs text-gray-400 line-through block">Bs. {{ number_format($prod->precio, 2) }}</span>
                                            <span class="text-2xl font-black text-red-600 block">Bs. {{ number_format($prod->precio_final, 2) }}</span>
                                        @else
                                            <span class="text-2xl font-black text-gray-900 block">Bs. {{ number_format($prod->precio, 2) }}</span>
                                        @endif
                                        <span class="block text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $prod->sku }}</span>
                                    </div>

                                    @if($prod->stock > 0)
                                        <button wire:click="abrirModalPedido({{ $prod->id }})" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition shadow-sm hover:shadow-md flex items-center gap-1.5">
                                            <i class="fas fa-paint-brush"></i> Personalizar
                                        </button>
                                    @else
                                        <button disabled class="bg-gray-100 text-gray-400 text-xs font-bold px-4 py-2.5 rounded-lg cursor-not-allowed">
                                            Sin Stock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-2xl shadow-sm border p-12 text-center">
                            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-bold text-gray-800">No se encontraron productos</h3>
                            <p class="text-gray-500 text-sm mt-1">Prueba a modificar tus criterios de búsqueda o filtros.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Columna derecha: Mi Pedido Actual (solo si hay items) --}}
            @if(!empty($carrito))
                <div class="lg:col-span-1 sticky top-20 bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden p-6 space-y-6">
                    <div class="flex items-center justify-between border-b pb-4">
                        <h3 class="text-lg font-black text-gray-805 flex items-center gap-2">
                            <i class="fas fa-shopping-basket text-blue-600"></i>
                            <span>Mi Pedido Actual</span>
                        </h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-black px-2.5 py-1 rounded-full">
                            {{ count($carrito) }} {{ count($carrito) === 1 ? 'ítem' : 'ítems' }}
                        </span>
                    </div>

                    {{-- Lista de productos agregados --}}
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-1">
                        @foreach($carrito as $index => $item)
                            <div class="py-4 flex gap-3 text-sm">
                                <div class="w-12 h-12 rounded-lg border bg-gray-50 flex-shrink-0 overflow-hidden flex items-center justify-center">
                                    @if(!empty($item['avatar_ruta']))
                                        <img src="{{ asset('storage/' . $item['avatar_ruta']) }}" alt="{{ $item['nombre'] }}" class="object-cover w-full h-full">
                                    @else
                                        <i class="fas fa-image text-gray-300 text-lg"></i>
                                    @endif
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-bold text-gray-800 truncate leading-snug">{{ $item['nombre'] }}</h4>
                                    <div class="text-[10px] text-gray-400 font-mono">SKU: {{ $item['sku'] }}</div>
                                    <div class="text-xs text-gray-500 font-medium mt-1">
                                        Bs. {{ number_format($item['precio_unitario'], 2) }} x {{ $item['cantidad'] }}
                                    </div>
                                    @if(!empty($item['personalizacion']))
                                        <div class="text-xs text-blue-600 mt-1 bg-blue-50/50 p-1.5 rounded border border-dashed border-blue-100 italic">
                                            "{{ $item['personalizacion'] }}"
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right flex flex-col justify-between items-end flex-shrink-0">
                                    <span class="font-bold font-mono text-gray-800">Bs. {{ number_format($item['subtotal'], 2) }}</span>
                                    <button wire:click="eliminarDelCarrito({{ $index }})" 
                                        class="text-gray-400 hover:text-red-600 transition p-1" title="Eliminar del pedido">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Formulario consolidado --}}
                    <div class="space-y-4 pt-4 border-t border-gray-100">
                        {{-- Fecha de entrega --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Fecha Sugerida de Retiro (Tentativa)</label>
                            <input type="date" wire:model="fecha_entrega" 
                                class="w-full border border-gray-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                            @error('fecha_entrega') <span class="block text-rose-500 text-[10px] font-semibold mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Código de cupón --}}
                        <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Cupón de Descuento</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="codigo_cupon" placeholder="Escribe tu cupón..." 
                                    class="flex-grow border border-gray-300 rounded-lg px-2.5 py-1.5 text-xs uppercase placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" wire:click.prevent="aplicarCupon"
                                    class="bg-gray-900 hover:bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition flex-shrink-0">
                                    Aplicar
                                </button>
                            </div>
                            @if (session()->has('error_cupon'))
                                <span class="block text-rose-500 text-[10px] font-semibold mt-1">
                                    <i class="fas fa-exclamation-circle mr-0.5"></i> {{ session('error_cupon') }}
                                </span>
                            @endif
                            @if (session()->has('success_cupon'))
                                <span class="block text-emerald-600 text-[10px] font-semibold mt-1">
                                    <i class="fas fa-check-circle mr-0.5"></i> {{ session('success_cupon') }}
                                </span>
                            @endif
                            @if ($cupon_aplicado)
                                <div class="mt-2 flex items-center justify-between text-[10px] text-emerald-800 font-bold bg-emerald-50 p-1.5 rounded border border-emerald-100">
                                    <span>Activo: {{ $cupon_aplicado->codigo }}</span>
                                    <span>Rebaja: -Bs. {{ number_format($descuento_calculado, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Totales --}}
                    <div class="pt-4 border-t border-dashed border-gray-200 space-y-2 text-xs">
                        <div class="flex justify-between text-gray-500">
                            <span>Subtotal:</span>
                            <span class="font-bold font-mono">Bs. {{ number_format($this->getCarritoSubtotal(), 2) }}</span>
                        </div>
                        @if($descuento_calculado > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Descuento por Cupón:</span>
                                <span class="font-bold font-mono">-Bs. {{ number_format($descuento_calculado, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center border-t border-dashed border-gray-200 pt-3 text-gray-900 font-bold">
                            <span class="uppercase tracking-wider text-[10px] text-gray-500">Total Final Estimado:</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-blue-600 font-mono">Bs. {{ number_format(max($this->getCarritoSubtotal() - $descuento_calculado, 0), 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Enviar Pedido --}}
                    <button type="button" wire:click="realizarPedido" wire:loading.attr="disabled"
                        class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="realizarPedido">
                            <i class="fas fa-paper-plane mr-1"></i> Enviar Pedido a la Tienda
                        </span>
                        <span wire:loading wire:target="realizarPedido" class="flex items-center gap-1.5">
                            <i class="fas fa-spinner fa-spin"></i> Enviando Pedido...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- CONTENIDO: HISTORIAL DE PEDIDOS --}}
    @if($active_tab === 'pedidos')
        {{-- Resumen Financiero del Cliente Rediseñado --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 text-white">
            {{-- Tarjeta 1: Pedidos Realizados --}}
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-md p-6 flex items-center gap-5 transition hover:scale-[1.01] duration-300">
                <div class="p-4 bg-white/10 rounded-xl">
                    <i class="fas fa-receipt text-3xl text-white"></i>
                </div>
                <div>
                    <span class="text-xs text-blue-100 font-bold uppercase tracking-wider block">Pedidos Realizados</span>
                    <h3 class="text-3xl font-black mt-1">{{ $resumen['total_pedidos'] }}</h3>
                </div>
            </div>

            {{-- Tarjeta 2: Total Invertido --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-md p-6 flex items-center gap-5 transition hover:scale-[1.01] duration-300">
                <div class="p-4 bg-white/10 rounded-xl">
                    <i class="fas fa-wallet text-3xl text-white"></i>
                </div>
                <div>
                    <span class="text-xs text-emerald-100 font-bold uppercase tracking-wider block">Total Invertido</span>
                    <h3 class="text-3xl font-black mt-1">Bs. {{ number_format($resumen['total_invertido'], 2) }}</h3>
                </div>
            </div>

            {{-- Tarjeta 3: Saldo Pendiente --}}
            <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl shadow-md p-6 flex items-center gap-5 transition hover:scale-[1.01] duration-300">
                <div class="p-4 bg-white/10 rounded-xl">
                    <i class="fas fa-clock text-3xl text-white"></i>
                </div>
                <div>
                    <span class="text-xs text-rose-100 font-bold uppercase tracking-wider block">Saldo Pendiente</span>
                    <h3 class="text-3xl font-black mt-1">Bs. {{ number_format($resumen['saldo_pendiente'], 2) }}</h3>
                </div>
            </div>
        </div>

        {{-- Tabla de Pedidos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800">Mi Historial de Compras</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b bg-gray-50/50">
                            <th class="px-6 py-3.5">Número de Pedido</th>
                            <th class="px-6 py-3.5">Fecha</th>
                            <th class="px-6 py-3.5 text-center">Estado</th>
                            <th class="px-6 py-3.5 text-right">Total</th>
                            <th class="px-6 py-3.5 text-right">Pagado</th>
                            <th class="px-6 py-3.5 text-right">Saldo</th>
                            <th class="px-6 py-3.5 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pedidos as $ped)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $ped->numero_pedido }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ date('d/m/Y H:i', strtotime($ped->creado_en)) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeStyles = match($ped->estado) {
                                            'cotizacion' => ['class' => 'bg-gray-100 text-gray-700 border-gray-200', 'icon' => 'fas fa-file-invoice'],
                                            'pendiente' => ['class' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fas fa-clock'],
                                            'en_diseno' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => 'fas fa-palette'],
                                            'aprobado' => ['class' => 'bg-indigo-100 text-indigo-800 border-indigo-200', 'icon' => 'fas fa-check-double'],
                                            'en_produccion' => ['class' => 'bg-purple-100 text-purple-800 border-purple-200', 'icon' => 'fas fa-tools'],
                                            'listo' => ['class' => 'bg-teal-100 text-teal-800 border-teal-200', 'icon' => 'fas fa-box-open'],
                                            'entregado' => ['class' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fas fa-handshake'],
                                            'cancelado' => ['class' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fas fa-ban'],
                                            default => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fas fa-info-circle']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles['class'] }}">
                                        <i class="{{ $badgeStyles['icon'] }} text-[10px]"></i>
                                        <span>{{ $estados_traduccion[$ped->estado] ?? $ped->estado }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-gray-800">Bs. {{ number_format($ped->total, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium text-emerald-600">Bs. {{ number_format($ped->monto_pagado, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-rose-600">
                                    Bs. {{ number_format($ped->total - $ped->monto_pagado, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button wire:click="abrirModalDetalle({{ $ped->id }})" 
                                        class="text-blue-600 hover:text-blue-800 font-bold text-xs inline-flex items-center gap-1">
                                        <i class="fas fa-eye"></i> Detalles
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                                    <i class="fas fa-shopping-basket text-4xl block mb-2"></i>
                                    Aún no has realizado ningún pedido en nuestro portal. ¡Explora el catálogo y personaliza tu primer producto!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- PESTAÑA: MI PERFIL --}}
    @if($active_tab === 'perfil')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Columna 1: Datos Personales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="border-b pb-4 flex items-center gap-3">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-user-edit text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Datos Personales</h3>
                        <p class="text-xs text-gray-400">Actualiza tu información de contacto básica.</p>
                    </div>
                </div>

                {{-- Notificación Éxito Perfil --}}
                @if (session()->has('success_perfil'))
                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>{{ session('success_perfil') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre</label>
                            <input type="text" wire:model="nombre" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                            @error('nombre') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Apellido</label>
                            <input type="text" wire:model="apellido" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                            @error('apellido') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre de Usuario</label>
                        <input type="text" wire:model="nombre_usuario" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('nombre_usuario') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Correo Electrónico</label>
                        <input type="email" wire:model="correo" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('correo') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Teléfono</label>
                        <input type="text" wire:model="telefono" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('telefono') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </form>
            </div>

            {{-- Columna 2: Seguridad y Contraseña --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <div class="border-b pb-4 flex items-center gap-3">
                    <div class="p-2.5 bg-rose-50 text-rose-600 rounded-lg">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Seguridad</h3>
                        <p class="text-xs text-gray-400">Cambia tu contraseña para mantener tu cuenta segura.</p>
                    </div>
                </div>

                {{-- Notificación Éxito Contraseña --}}
                @if (session()->has('success_password'))
                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-lg text-emerald-800 text-xs font-semibold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>{{ session('success_password') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Contraseña Actual</label>
                        <input type="password" wire:model="current_password" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('current_password') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nueva Contraseña</label>
                        <input type="password" wire:model="password" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('password') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Confirmar Nueva Contraseña</label>
                        <input type="password" wire:model="password_confirmation" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-semibold text-gray-700">
                        @error('password_confirmation') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-key"></i> Actualizar Contraseña
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL: PERSONALIZACIÓN (AGREGAR AL CARRITO) --}}
    @if($productoSeleccionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            {{-- Fondo Oscuro con Desenfoque --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cerrarModalPedido"></div>

            {{-- Contenedor del Modal --}}
            <div class="relative bg-white rounded-2xl max-w-xl w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 my-8 animate-fadeIn">
                <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-paint-brush"></i>
                        <span>Personalizar Producto</span>
                    </h3>
                    <button wire:click="cerrarModalPedido" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6 max-h-[75vh] overflow-y-auto">
                    {{-- Ficha Corta del Producto --}}
                    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl mb-6 border">
                        <div class="w-16 h-16 bg-white rounded-lg border overflow-hidden flex items-center justify-center flex-shrink-0">
                            @if($productoSeleccionado->avatar_ruta)
                                <img src="{{ asset('storage/' . $productoSeleccionado->avatar_ruta) }}" alt="{{ $productoSeleccionado->nombre }}" class="object-cover w-full h-full">
                            @else
                                <i class="fas fa-image text-gray-300 text-2xl"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-base leading-snug">{{ $productoSeleccionado->nombre }}</h4>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">SKU: {{ $productoSeleccionado->sku }}</p>
                            @if($productoSeleccionado->en_oferta)
                                <span class="block text-xs text-gray-400 line-through mt-1">Precio Reg: Bs. {{ number_format($productoSeleccionado->precio, 2) }}</span>
                                <span class="block text-sm font-black text-red-600">Oferta: Bs. {{ number_format($productoSeleccionado->precio_final, 2) }}</span>
                            @else
                                <span class="block text-sm font-black text-gray-900 mt-1">Precio Unitario: Bs. {{ number_format($productoSeleccionado->precio, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Formulario --}}
                    <form wire:submit.prevent="agregarAlCarrito" class="space-y-5">
                        {{-- Cantidad --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Cantidad a Solicitar</label>
                            <div class="flex items-center gap-3">
                                <input type="number" wire:model.live="cantidad" min="1" max="{{ $productoSeleccionado->stock }}" 
                                    class="w-32 border border-gray-300 rounded-lg p-2 text-center font-bold text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <span class="text-xs font-semibold text-gray-400">
                                    (Máximo disponible: <strong>{{ $productoSeleccionado->stock }}</strong> unidades)
                                </span>
                            </div>
                            @error('cantidad') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Instrucciones de Personalización --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Instrucciones de Sublimación / Diseño</label>
                            <textarea wire:model="personalizacion" rows="4" placeholder="Escribe el nombre, frase, colores, o detalles para tu diseño personalizado..." 
                                class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder:text-gray-400"></textarea>
                            <span class="block text-[10px] text-gray-400 mt-1">Indica detalladamente tu idea de diseño. Te contactaremos si es necesario aclarar detalles.</span>
                            @error('personalizacion') <span class="block text-rose-500 text-xs font-semibold mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Cálculo de Costo Item --}}
                        <div class="border-t border-dashed pt-4 mt-6 space-y-2 text-sm">
                            <div class="flex justify-between items-center border-t border-dashed pt-2 font-bold text-gray-900">
                                <span class="uppercase tracking-wider text-xs text-gray-500">Subtotal del Artículo:</span>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-blue-600 font-mono">Bs. {{ number_format($productoSeleccionado->precio_final * (int)$cantidad, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Control --}}
                        <div class="flex justify-end gap-3 pt-4 border-t">
                            <button type="button" wire:click="cerrarModalPedido" class="px-5 py-2.5 rounded-lg border text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                <span wire:loading.remove wire:target="agregarAlCarrito">Agregar al Pedido</span>
                                <span wire:loading wire:target="agregarAlCarrito" class="flex items-center gap-1.5">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Agregando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: VER DETALLE DEL PEDIDO --}}
    @if($pedidoDetalle)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" role="dialog" aria-modal="true">
            {{-- Fondo Oscuro --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="cerrarModalDetalle"></div>

            {{-- Contenedor del Modal --}}
            <div class="relative bg-white rounded-2xl max-w-2xl w-full mx-4 shadow-2xl border border-gray-100 overflow-hidden z-10 my-8 animate-fadeIn">
                <div class="bg-gray-900 text-white px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold flex items-center gap-2">
                            <i class="fas fa-file-invoice"></i>
                            <span>Desglose de Pedido</span>
                        </h3>
                        <span class="text-xs text-gray-400 font-mono mt-0.5">{{ $pedidoDetalle->numero_pedido }}</span>
                    </div>
                    <button wire:click="cerrarModalDetalle" class="text-white/80 hover:text-white transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="p-6">
                    {{-- Información General del Pedido --}}
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border mb-6 text-sm">
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Fecha de Registro:</span>
                            <span class="font-semibold text-gray-700">{{ date('d/m/Y H:i', strtotime($pedidoDetalle->creado_en)) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Fecha sugerida de entrega:</span>
                            <span class="font-semibold text-gray-700">{{ $pedidoDetalle->fecha_entrega ? $pedidoDetalle->fecha_entrega->format('d/m/Y') : 'Por acordar' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Estado actual:</span>
                            <span class="font-bold text-blue-600">{{ $estados_traduccion[$pedidoDetalle->estado] ?? $pedidoDetalle->estado }}</span>
                        </div>
                    </div>

                    {{-- Stepper de Progreso del Pedido --}}
                    @php
                        $etapas = [
                            ['key' => 'pendiente', 'label' => 'Recibido', 'icon' => 'fas fa-inbox'],
                            ['key' => 'en_diseno', 'label' => 'Diseño', 'icon' => 'fas fa-palette'],
                            ['key' => 'aprobado', 'label' => 'Aprobado', 'icon' => 'fas fa-check-double'],
                            ['key' => 'en_produccion', 'label' => 'Producción', 'icon' => 'fas fa-tools'],
                            ['key' => 'listo', 'label' => 'Listo', 'icon' => 'fas fa-box-open'],
                            ['key' => 'entregado', 'label' => 'Entregado', 'icon' => 'fas fa-handshake']
                        ];

                        // Mapear cotización a pendiente para el stepper
                        $estadoActual = $pedidoDetalle->estado;
                        if ($estadoActual === 'cotizacion') {
                            $estadoActual = 'pendiente';
                        }

                        // Obtener el índice del estado actual
                        $estadoIndex = -1;
                        foreach ($etapas as $idx => $etapa) {
                            if ($etapa['key'] === $estadoActual) {
                                $estadoIndex = $idx;
                                break;
                            }
                        }
                    @endphp

                    @if($pedidoDetalle->estado === 'cancelado')
                        <div class="mb-6 p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-800 flex items-center gap-3">
                            <div class="p-2 bg-rose-200 rounded-lg text-rose-600">
                                <i class="fas fa-ban text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Este pedido está Cancelado</h4>
                                <p class="text-xs text-rose-605 mt-0.5">El pedido ha sido anulado por la administración.</p>
                            </div>
                        </div>
                    @else
                        <div class="mb-8 p-4 bg-gray-50 border rounded-xl">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                <i class="fas fa-route text-blue-600"></i>
                                <span>Estado del Pedido</span>
                            </h4>
                            <div class="relative flex items-center justify-between px-2">
                                {{-- Línea de conexión de fondo --}}
                                <div class="absolute left-0 right-0 h-1 bg-gray-200 top-1/2 -translate-y-1/2 z-0"></div>
                                {{-- Línea de conexión activa --}}
                                <div class="absolute left-0 h-1 bg-blue-600 top-1/2 -translate-y-1/2 z-0 transition-all duration-500" 
                                    style="width: {{ $estadoIndex >= 0 ? ($estadoIndex / (count($etapas) - 1)) * 100 : 0 }}%"></div>

                                @foreach($etapas as $idx => $etapa)
                                    @php
                                        $isCompleted = $idx < $estadoIndex;
                                        $isActive = $idx === $estadoIndex;
                                        $isPending = $idx > $estadoIndex;

                                        $circleClass = 'bg-gray-200 border-gray-300 text-gray-400';
                                        if ($isCompleted) {
                                            $circleClass = 'bg-blue-600 border-blue-600 text-white';
                                        } elseif ($isActive) {
                                            $circleClass = 'bg-white border-4 border-blue-600 text-blue-600 scale-110 shadow-md';
                                        }
                                    @endphp
                                    <div class="relative flex flex-col items-center z-10">
                                        <div class="w-8 h-8 rounded-full border flex items-center justify-center font-bold text-xs shadow transition duration-300 {{ $circleClass }}">
                                            @if($isCompleted)
                                                <i class="fas fa-check text-[10px]"></i>
                                            @else
                                                <i class="{{ $etapa['icon'] }} text-[10px]"></i>
                                            @endif
                                        </div>
                                        <span class="absolute top-9 text-[9px] font-bold mt-1 text-center whitespace-nowrap {{ $isActive ? 'text-blue-600' : 'text-gray-400' }}">
                                            {{ $etapa['label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="h-4"></div>
                        </div>
                    @endif

                    {{-- Productos Solicitados --}}
                    <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Artículos del Pedido</h4>
                    <div class="border rounded-xl overflow-hidden mb-6">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left font-bold text-gray-600 text-xs">Producto</th>
                                    <th class="px-4 py-2.5 text-center font-bold text-gray-600 text-xs">Cant.</th>
                                    <th class="px-4 py-2.5 text-right font-bold text-gray-600 text-xs">Precio</th>
                                    <th class="px-4 py-2.5 text-right font-bold text-gray-600 text-xs">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($pedidoDetalle->detalles as $det)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-800">{{ $det->producto->nombre ?? 'Producto Desconocido' }}</div>
                                            @if($det->personalizacion)
                                                <div class="text-xs text-blue-600 mt-1 italic p-2 bg-blue-50/50 rounded-lg border border-dashed border-blue-100">
                                                    <strong>Diseño:</strong> "{{ $det->personalizacion }}"
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600 font-semibold">{{ $det->cantidad }}</td>
                                        <td class="px-4 py-3 text-right text-gray-500 font-mono">Bs. {{ number_format($det->precio_unitario, 2) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-800 font-mono">Bs. {{ number_format($det->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Resumen Financiero --}}
                    <div class="space-y-2 border-t pt-4 text-sm flex flex-col items-end">
                        <div class="flex justify-between w-64 text-gray-500">
                            <span>Subtotal de Artículos:</span>
                            <span class="font-bold text-gray-800 font-mono">Bs. {{ number_format($pedidoDetalle->detalles->sum('subtotal'), 2) }}</span>
                        </div>
                        @if($pedidoDetalle->descuento > 0)
                            <div class="flex justify-between w-64 text-emerald-600 font-semibold">
                                <span>Descuento (Cupón{{ $pedidoDetalle->cupon ? ': ' . $pedidoDetalle->cupon->codigo : '' }}):</span>
                                <span class="font-mono">-Bs. {{ number_format($pedidoDetalle->descuento, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between w-64 text-gray-800 font-bold border-t pt-1">
                            <span>Monto Total:</span>
                            <span class="font-mono">Bs. {{ number_format($pedidoDetalle->total, 2) }}</span>
                        </div>
                        <div class="flex justify-between w-64 text-emerald-600">
                            <span>Monto Pagado:</span>
                            <span class="font-bold font-mono">Bs. {{ number_format($pedidoDetalle->monto_pagado, 2) }}</span>
                        </div>
                        <div class="flex justify-between w-64 text-rose-600 text-base font-bold border-t border-dashed pt-2 mt-1">
                            <span>Saldo Pendiente:</span>
                            <span class="font-mono">Bs. {{ number_format($pedidoDetalle->total - $pedidoDetalle->monto_pagado, 2) }}</span>
                        </div>
                    </div>

                    {{-- Botón Cerrar --}}
                    <div class="flex justify-end pt-4 mt-6 border-t">
                        <button type="button" wire:click="cerrarModalDetalle" class="px-5 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Soporte de Animación y Estilos --}}
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
