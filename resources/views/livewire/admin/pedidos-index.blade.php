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

    @php
        $pendingOrdersCount = \App\Models\Pedido::where('estado', 'pendiente')->count();
    @endphp
    @if($pendingOrdersCount > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-4 mb-6 rounded-lg shadow-sm flex items-center justify-between animate-pulse">
        <div class="flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-xl text-amber-600"></i>
            <div>
                <p class="text-sm font-bold text-amber-800">Nuevos Pedidos por Procesar</p>
                <p class="text-xs text-amber-705 text-amber-700">Tienes <strong>{{ $pendingOrdersCount }}</strong> {{ $pendingOrdersCount === 1 ? 'pedido pendiente' : 'pedidos pendientes' }} esperando aprobación y coordinación.</p>
            </div>
        </div>
        <button wire:click="$set('estado_filtro', 'pendiente')" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition shadow-sm">
            Filtrar Pendientes
        </button>
    </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">🛒 Gestión de Pedidos</h2>

        <div class="flex gap-4 w-full md:w-auto">
            <div class="relative flex-grow md:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar pedido o cliente..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2 shadow-md">
                <i class="fas fa-plus"></i>
                <span>Nuevo Pedido</span>
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-filter text-gray-400 mr-1"></i> Estado
                </label>
                <select wire:model.live="estado_filtro" class="w-full border rounded-lg p-2">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $key => $nombre)
                    <option value="{{ $key }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-list-ol text-gray-400 mr-1"></i> Mostrar
                </label>
                <select wire:model.live="perPage" class="w-full border rounded-lg p-2">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tabla de pedidos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"># Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-bold">{{ $pedido->numero_pedido }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ $pedido->cliente->nombre_completo ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $pedido->cliente->correo ?? 'Sin correo' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pedido->creado_en->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-green-600">Bs. {{ number_format($pedido->total, 2) }}</div>
                            <div class="text-xs text-gray-500">Pagado: Bs. {{ number_format($pedido->monto_pagado, 2) }}</div>
                            <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                <div class="bg-green-500 h-1 rounded-full" style="width: {{ $pedido->porcentaje_pagado }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <select wire:change="cambiarEstado({{ $pedido->id }}, $event.target.value)"
                                class="text-xs rounded-full px-2 py-1 border-0 font-semibold
                                {{ $pedido->estado == 'entregado' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $pedido->estado == 'cancelado' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $pedido->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $pedido->estado == 'cotizacion' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $pedido->estado == 'en_produccion' ? 'bg-blue-100 text-blue-800' : '' }}">
                                @foreach($estados as $key => $nombre)
                                <option value="{{ $key }}" {{ $pedido->estado == $key ? 'selected' : '' }}>{{ $nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <button wire:click="verDetalle({{ $pedido->id }})" class="text-green-600 hover:text-green-800 mr-2" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button wire:click="edit({{ $pedido->id }})" class="text-blue-600 hover:text-blue-800 mr-2" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="abrirModalAbonos({{ $pedido->id }})" class="text-emerald-600 hover:text-emerald-800 mr-2" title="Pagos / Registrar Abono" @if($pedido->estado === 'cancelado') disabled @endif>
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $pedido->id }}, '{{ $pedido->numero_pedido }}')" class="text-red-600 hover:text-red-800" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-shopping-cart text-5xl mb-3"></i>
                            <p>No hay pedidos registrados</p>
                            <button wire:click="create" class="mt-2 text-blue-600 hover:text-blue-800 transition">
                                <i class="fas fa-plus mr-1"></i> Crear el primer pedido
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pedidos->links() }}
        </div>
    </div>

    <!-- MODAL CREAR/EDITAR PEDIDO -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    {{ $pedido_id ? 'Editar Pedido' : 'Nuevo Pedido' }}
                                </h3>
                                <p class="text-xs text-blue-100">
                                    {{ $pedido_id ? 'Modifica los datos del pedido' : 'Completa los datos para crear un nuevo pedido' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Formulario -->
                <div class="max-h-[70vh] overflow-y-auto px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Número de Pedido -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Pedido</label>
                            <input type="text" wire:model="numero_pedido" readonly
                                class="w-full px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-lg">
                        </div>

                        <!-- Fecha de Entrega -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Entrega</label>
                            <input type="date" wire:model="fecha_entrega"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('fecha_entrega') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Buscar Cliente / Registro Rápido -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente <span class="text-red-500">*</span></label>
                            @if(!$cliente_id)
                                @if(!$mostrar_formulario_cliente)
                                <div class="relative">
                                    <div class="flex gap-2">
                                        <div class="relative flex-grow">
                                            <input type="text" wire:model.live.debounce.300ms="cliente_search"
                                                class="w-full pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                placeholder="Buscar cliente por nombre, apellido, CI o correo..."
                                                wire:focus="buscarClientes">
                                            <button type="button" wire:click="toggleClientesDropdown" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </button>
                                            @if(count($clientes_buscados) > 0)
                                            <div class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto divide-y divide-gray-100">
                                                @foreach($clientes_buscados as $cliente)
                                                <div wire:click="seleccionarCliente({{ $cliente->id }}, {{ $cliente->is_usuario ? 'true' : 'false' }})" class="px-3 py-2 hover:bg-blue-50 cursor-pointer transition duration-150 text-left">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-medium text-gray-800">
                                                            {{ $cliente->nombre_completo }}
                                                            @if($cliente->is_usuario)
                                                            <span class="text-[10px] text-blue-600 bg-blue-50 font-semibold ml-1.5 px-1.5 py-0.5 rounded border border-blue-200">Usuario Registrado</span>
                                                            @endif
                                                        </span>
                                                        @if($cliente->nit_ci)
                                                        <span class="bg-blue-100 text-blue-800 text-[10px] px-2 py-0.5 rounded font-mono">CI: {{ $cliente->nit_ci }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $cliente->correo ?? 'Sin correo' }}</div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                        <button type="button" wire:click="$set('mostrar_formulario_cliente', true)" 
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-sm transition flex items-center gap-1 border">
                                            <i class="fas fa-plus"></i>
                                            <span>Nuevo</span>
                                        </button>
                                    </div>
                                    @if($cliente_search && count($clientes_buscados) == 0)
                                    <p class="text-xs text-gray-500 mt-1">
                                        No se encontraron clientes. Puedes 
                                        <button type="button" wire:click="$set('mostrar_formulario_cliente', true)" class="text-blue-600 hover:underline">registrar uno nuevo</button>.
                                    </p>
                                    @endif
                                </div>
                                @else
                                <!-- Formulario de registro rápido de cliente -->
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 animate-fadeIn">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="text-sm font-semibold text-gray-700">Registrar Nuevo Cliente</h4>
                                        <button type="button" wire:click="resetNuevoClienteForm" class="text-gray-400 hover:text-gray-600 text-xs">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Nombre <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="nuevo_cliente_nombre" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_nombre') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Apellido <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="nuevo_cliente_apellido" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_apellido') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Correo Electrónico</label>
                                            <input type="email" wire:model="nuevo_cliente_correo" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_correo') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">NIT / CI</label>
                                            <input type="text" wire:model="nuevo_cliente_nit_ci" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_nit_ci') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                                            <input type="text" wire:model="nuevo_cliente_telefono" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_telefono') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">WhatsApp</label>
                                            <input type="text" wire:model="nuevo_cliente_whatsapp" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_whatsapp') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Empresa</label>
                                            <input type="text" wire:model="nuevo_cliente_empresa" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                            @error('nuevo_cliente_empresa') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Canal de Registro</label>
                                            <select wire:model="nuevo_cliente_canal" class="w-full px-3 py-1.5 text-xs border rounded-lg focus:ring-1 focus:ring-blue-500">
                                                <option value="presencial">Presencial</option>
                                                <option value="web">Web</option>
                                                <option value="whatsapp">WhatsApp</option>
                                                <option value="redes_sociales">Redes Sociales</option>
                                                <option value="referido">Referido</option>
                                            </select>
                                            @error('nuevo_cliente_canal') <span class="text-red-500 text-[10px]">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <button type="button" wire:click="registrarCliente" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                                        <i class="fas fa-save mr-1"></i> Registrar y Seleccionar
                                    </button>
                                </div>
                                @endif
                            @else
                            <div class="flex items-center justify-between bg-blue-50 p-2 rounded-lg">
                                <div>
                                    <div class="font-medium">{{ $cliente_nombre }}</div>
                                </div>
                                <button type="button" wire:click="$set('cliente_id', null)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endif
                            @error('cliente_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Productos del Pedido -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Productos</label>
                            <div class="border rounded-lg p-4 mb-2">
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <div class="col-span-2 relative">
                                        @if(!$producto_actual_id)
                                        <div class="relative">
                                            <input type="text" wire:model.live.debounce.300ms="producto_search"
                                                class="w-full pl-3 pr-8 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                                                placeholder="Buscar producto por nombre, SKU..."
                                                wire:focus="buscarProductos">
                                            <button type="button" wire:click="toggleProductosDropdown" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </button>
                                        </div>
                                        @if(count($productos_buscados) > 0)
                                        <div class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto divide-y divide-gray-100">
                                            @foreach($productos_buscados as $prod)
                                            <div wire:click="seleccionarProducto({{ $prod->id }})" class="px-3 py-2 hover:bg-blue-50 cursor-pointer transition duration-150 text-left">
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="font-medium text-gray-800 text-left">{{ $prod->nombre }}</span>
                                                    <span class="bg-gray-100 text-gray-700 px-1.5 py-0.5 rounded font-mono text-[10px]">Stock: {{ $prod->stock }}</span>
                                                </div>
                                                <div class="text-[10px] text-gray-500 flex justify-between mt-0.5">
                                                    <span>SKU: {{ $prod->sku }}</span>
                                                    <span class="font-bold text-green-600">Bs. {{ number_format($prod->precio, 2) }}</span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                        @else
                                        <div class="flex items-center justify-between bg-blue-50 p-2 rounded-lg text-xs">
                                            <div class="font-medium text-gray-800 text-left">{{ $producto_actual_nombre }}</div>
                                            <button type="button" wire:click="$set('producto_actual_id', null)" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    <input type="number" wire:model="producto_actual_cantidad" min="1" placeholder="Cantidad"
                                        class="border rounded-lg p-2 text-sm">
                                </div>
                                <textarea wire:model="producto_actual_personalizacion" rows="2" placeholder="Personalización / Notas del diseño..."
                                    class="w-full border rounded-lg p-2 text-sm mb-2"></textarea>
                                <button type="button" wire:click="agregarProducto" class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm hover:bg-green-600">
                                    <i class="fas fa-plus mr-1"></i> Agregar Producto
                                </button>
                                @error('producto_actual_cantidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @error('productos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Lista de productos agregados -->
                            @if(!empty($detalles))
                            <div class="bg-gray-50 rounded-lg p-3">
                                <table class="w-full text-sm">
                                    <thead class="border-b">
                                        <tr>
                                            <th class="text-left py-2">Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-right">Precio</th>
                                            <th class="text-right">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles as $index => $detalle)
                                        <tr class="border-b">
                                            <td class="py-2">
                                                {{ $detalle['producto_nombre'] }}
                                                @if($detalle['personalizacion'] ?? false)
                                                <div class="text-xs text-gray-500">Personalización: {{ $detalle['personalizacion'] }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $detalle['cantidad'] }}</td>
                                            <td class="text-right">Bs. {{ number_format($detalle['precio_unitario'], 2) }}</td>
                                            <td class="text-right font-medium">Bs. {{ number_format($detalle['subtotal'], 2) }}</td>
                                            <td class="text-center">
                                                <button type="button" wire:click="quitarProducto({{ $index }})" class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-bold">
                                            <td colspan="3" class="text-right pt-2">TOTAL:</td>
                                            <td class="text-right text-green-600 pt-2">Bs. {{ number_format($total, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @endif
                        </div>

                        <!-- Estado y Monto Pagado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select wire:model="estado" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                                @foreach($estados as $key => $nombre)
                                <option value="{{ $key }}">{{ $nombre }}</option>
                                @endforeach
                            </select>
                            @error('estado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Monto Pagado (Bs.)</label>
                            <input type="number" step="0.01" wire:model="monto_pagado" readonly
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                            @error('monto_pagado') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="store" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i>
                        {{ $pedido_id ? 'Actualizar' : 'Guardar' }} Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL VER DETALLE DEL PEDIDO -->
    @if($showDetalleModal && $detalle_pedido_actual)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeDetalleModal"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-3xl w-full">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-receipt mr-2"></i> Detalle del Pedido
                        </h3>
                        <button type="button" wire:click="closeDetalleModal" class="text-white/80 hover:text-white">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Número de Pedido</p>
                            <p class="font-mono font-bold">{{ $detalle_pedido_actual->numero_pedido }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fecha</p>
                            <p>{{ $detalle_pedido_actual->creado_en->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Cliente</p>
                            <p class="font-medium">{{ $detalle_pedido_actual->cliente->nombre_completo ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $detalle_pedido_actual->cliente->correo ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estado</p>
                            <span class="inline-flex px-2 py-1 text-xs rounded-full
                                {{ $detalle_pedido_actual->estado == 'entregado' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $detalle_pedido_actual->estado == 'cancelado' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $detalle_pedido_actual->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $estados[$detalle_pedido_actual->estado] ?? $detalle_pedido_actual->estado }}
                            </span>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 mb-3">Productos del Pedido</h4>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Producto</th>
                                    <th class="px-4 py-2 text-center">Cantidad</th>
                                    <th class="px-4 py-2 text-right">Precio</th>
                                    <th class="px-4 py-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detalle_pedido_actual->detalles as $detalle)
                                <tr class="border-b">
                                    <td class="px-4 py-2">
                                        {{ $detalle->producto->nombre }}
                                        @if($detalle->personalizacion)
                                        <div class="text-xs text-gray-500">Personalización: {{ $detalle->personalizacion }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $detalle->cantidad }}</td>
                                    <td class="px-4 py-2 text-right">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-medium">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right">TOTAL:</td>
                                    <td class="px-4 py-2 text-right text-green-600">Bs. {{ number_format($detalle_pedido_actual->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button wire:click="closeDetalleModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!--     <!-- MODAL DE ABONOS / REGISTRO DE PAGOS -->
    @if($isOpenAbonosModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cerrarModalAbonos"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 rounded-t-xl text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Registro de Abonos / Pagos</h3>
                                <p class="text-xs text-emerald-100">Controle y registre pagos parciales o totales de este pedido</p>
                            </div>
                        </div>
                        <button type="button" wire:click="cerrarModalAbonos" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 text-left grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Historial de Abonos --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="fas fa-history mr-1"></i> Historial de Pagos</h4>
                        <div class="border rounded-xl p-3 bg-gray-50 max-h-64 overflow-y-auto space-y-3 custom-scrollbar">
                            @forelse($abonosHistoricos as $abono)
                                <div class="bg-white p-3 rounded-lg border border-gray-150 shadow-xs text-xs">
                                    <div class="flex justify-between font-bold text-gray-800">
                                        <span>Bs. {{ number_format($abono->monto, 2) }}</span>
                                        <span class="text-gray-400 font-normal font-mono">{{ $abono->creado_en->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="text-gray-500 mt-1 flex items-center gap-1.5 uppercase font-semibold">
                                        @if($abono->metodo_pago === 'efectivo')
                                            <i class="fas fa-coins text-emerald-500"></i> Efectivo
                                        @elseif($abono->metodo_pago === 'qr')
                                            <i class="fas fa-qrcode text-blue-500"></i> QR
                                        @elseif($abono->metodo_pago === 'transferencia')
                                            <i class="fas fa-university text-indigo-500"></i> Transfer
                                        @else
                                            <i class="fas fa-credit-card text-gray-400"></i> Otro
                                        @endif
                                    </div>
                                    @if($abono->referencia)
                                        <div class="text-[10px] text-gray-400 font-mono mt-1">Ref: {{ $abono->referencia }}</div>
                                    @endif
                                    <div class="text-[9px] text-gray-400 mt-1">Registrado por: {{ $abono->usuario->nombre ?? 'Sistema' }}</div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic text-center py-8">No se han registrado abonos para este pedido.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Formulario para Registrar Nuevo Abono --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3"><i class="fas fa-plus-circle mr-1"></i> Registrar Nuevo Abono</h4>
                        
                        @if(session()->has('error_abono'))
                            <div class="bg-red-50 border border-red-200 text-red-700 p-2.5 rounded-lg text-xs mb-3 font-semibold">
                                {{ session('error_abono') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="registrarAbono" class="space-y-4">
                            {{-- Monto Abono --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Monto del Pago (Bs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" wire:model="monto_abono" placeholder="0.00"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                                @error('monto_abono') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Método de Pago --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Método de Pago <span class="text-red-500">*</span></label>
                                <select wire:model="metodo_pago_abono"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition font-semibold">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="qr">Pago QR</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('metodo_pago_abono') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            {{-- Referencia --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Código de Referencia / Comprobante</label>
                                <input type="text" wire:model="referencia_abono" placeholder="Nro de transf, captura, etc."
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                                @error('referencia_abono') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" wire:loading.attr="disabled"
                                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-50">
                                <i class="fas fa-save" wire:loading.remove wire:target="registrarAbono"></i>
                                <i class="fas fa-spinner fa-spin" wire:loading wire:target="registrarAbono"></i>
                                <span>Registrar Pago</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end border-t">
                    <button type="button" wire:click="cerrarModalAbonos"
                        class="px-4 py-2 text-sm font-medium text-gray-750 bg-white border border-gray-350 rounded-lg hover:bg-gray-50 transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
