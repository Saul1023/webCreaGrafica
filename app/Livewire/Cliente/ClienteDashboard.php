<?php

namespace App\Livewire\Cliente;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\CategoriaProducto;
use App\Models\DetallePedido;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClienteDashboard extends Component
{
    // Pestaña activa ('catalogo', 'pedidos' o 'perfil')
    public $active_tab = 'catalogo';

    // Filtros de búsqueda
    public $search = '';
    public $categoria_filtro = '';

    // Formulario de Pedido
    public $producto_seleccionado_id;
    public $cantidad = 1;
    public $personalizacion = '';
    public $fecha_entrega = '';

    // Carrito de compras temporal del cliente
    public $carrito = [];

    // Cupones y Descuentos
    public $codigo_cupon = '';
    public $cupon_aplicado = null;
    public $descuento_calculado = 0.00;

    // Modal de Detalles de un Pedido Existente
    public $ver_pedido_id;

    // Propiedades para Datos Personales
    public $nombre;
    public $apellido;
    public $nombre_usuario;
    public $correo;
    public $telefono;

    // Propiedades para Cambio de Contraseña
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->fecha_entrega = date('Y-m-d', strtotime('+3 days')); // Sugerir 3 días de margen por defecto

        // Inicializar si se pasa un producto seleccionado desde la URL pública
        if ($this->producto_seleccionado_id) {
            $producto = Producto::find($this->producto_seleccionado_id);
            if (!$producto || !$producto->activo) {
                $this->producto_seleccionado_id = null;
            } else {
                $this->cantidad = 1;
                $this->personalizacion = '';
            }
        }

        // Cargar datos de perfil del usuario logueado
        $usuario = Auth::user();
        if ($usuario) {
            $this->nombre = $usuario->nombre;
            $this->apellido = $usuario->apellido;
            $this->nombre_usuario = $usuario->nombre_usuario;
            $this->correo = $usuario->correo;
            $this->telefono = $usuario->telefono;
        }
    }

    protected $queryString = [
        'active_tab' => ['except' => 'catalogo'],
        'search' => ['except' => ''],
        'categoria_filtro' => ['except' => ''],
        'producto_seleccionado_id' => ['except' => '']
    ];

    public function selectTab($tab)
    {
        $this->active_tab = $tab;
        $this->resetErrorBag();
    }

    public function abrirModalPedido($productoId)
    {
        $producto = Producto::find($productoId);
        if (!$producto || !$producto->activo) {
            session()->flash('error', 'El producto seleccionado no está disponible.');
            return;
        }

        $this->producto_seleccionado_id = $productoId;
        $this->cantidad = 1;
        $this->personalizacion = '';
        $this->fecha_entrega = date('Y-m-d', strtotime('+3 days')); // Sugerir 3 días de margen
        $this->codigo_cupon = '';
        $this->cupon_aplicado = null;
        $this->descuento_calculado = 0.00;
    }

    public function cerrarModalPedido()
    {
        $this->producto_seleccionado_id = null;
        $this->codigo_cupon = '';
        $this->cupon_aplicado = null;
        $this->descuento_calculado = 0.00;
        $this->resetErrorBag();
    }

    public function abrirModalDetalle($pedidoId)
    {
        $this->ver_pedido_id = $pedidoId;
    }

    public function cerrarModalDetalle()
    {
        $this->ver_pedido_id = null;
    }    public function agregarAlCarrito()
    {
        $producto = Producto::find($this->producto_seleccionado_id);
        if (!$producto || !$producto->activo) {
            session()->flash('error', 'El producto seleccionado no está disponible.');
            return;
        }

        $this->validate([
            'cantidad' => 'required|integer|min:1',
            'personalizacion' => 'nullable|string|max:1000',
        ], [
            'cantidad.required' => 'Debes ingresar una cantidad.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima es 1.',
            'personalizacion.max' => 'Los detalles de personalización no deben exceder los 1000 caracteres.',
        ]);

        // Validar Stock
        if ($this->cantidad > $producto->stock) {
            $this->addError('cantidad', "Stock insuficiente. Solo quedan {$producto->stock} unidades disponibles.");
            return;
        }

        // Buscar si ya está en el carrito con la misma personalización
        $encontradoIndex = null;
        foreach ($this->carrito as $index => $item) {
            if ($item['producto_id'] == $producto->id && $item['personalizacion'] == $this->personalizacion) {
                $encontradoIndex = $index;
                break;
            }
        }

        if ($encontradoIndex !== null) {
            // Incrementar cantidad
            $nuevaCantidad = $this->carrito[$encontradoIndex]['cantidad'] + $this->cantidad;
            if ($nuevaCantidad > $producto->stock) {
                $this->addError('cantidad', "No puedes agregar esa cantidad. Ya tienes {$this->carrito[$encontradoIndex]['cantidad']} en el pedido y el stock total es {$producto->stock}.");
                return;
            }
            $this->carrito[$encontradoIndex]['cantidad'] = $nuevaCantidad;
            $this->carrito[$encontradoIndex]['subtotal'] = $nuevaCantidad * $this->carrito[$encontradoIndex]['precio_unitario'];
        } else {
            // Agregar nuevo item
            $precioUnitario = $producto->precio_final;
            $this->carrito[] = [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'sku' => $producto->sku,
                'avatar_ruta' => $producto->avatar_ruta,
                'precio_unitario' => $precioUnitario,
                'cantidad' => $this->cantidad,
                'personalizacion' => $this->personalizacion,
                'subtotal' => $this->cantidad * $precioUnitario,
            ];
        }

        $this->cerrarModalPedido();
        $this->recalcularDescuento();
        session()->flash('success', 'Producto agregado a tu pedido actual.');
    }

    public function eliminarDelCarrito($index)
    {
        if (isset($this->carrito[$index])) {
            unset($this->carrito[$index]);
            $this->carrito = array_values($this->carrito); // Reindexar
            $this->recalcularDescuento();
            session()->flash('success', 'Producto eliminado de tu pedido actual.');
        }
    }

    public function getCarritoSubtotal()
    {
        $subtotal = 0;
        foreach ($this->carrito as $item) {
            $subtotal += $item['precio_unitario'] * $item['cantidad'];
        }
        return $subtotal;
    }

    public function aplicarCupon()
    {
        if (empty($this->carrito)) {
            session()->flash('error_cupon', 'Agregue productos al pedido antes de aplicar un cupón.');
            return;
        }

        if (empty($this->codigo_cupon)) {
            $this->cupon_aplicado = null;
            $this->descuento_calculado = 0.00;
            return;
        }

        $cupon = \App\Models\Cupon::where('codigo', strtoupper(trim($this->codigo_cupon)))->first();

        if (!$cupon) {
            $this->cupon_aplicado = null;
            $this->descuento_calculado = 0.00;
            session()->flash('error_cupon', 'El código de cupón no existe o no es válido.');
            return;
        }

        $subtotal = $this->getCarritoSubtotal();
        $validacion = $cupon->esValidoPara($subtotal);

        if (!$validacion['valido']) {
            $this->cupon_aplicado = null;
            $this->descuento_calculado = 0.00;
            session()->flash('error_cupon', $validacion['mensaje']);
            return;
        }

        $this->cupon_aplicado = $cupon;
        $this->descuento_calculado = $cupon->calcularDescuentoPara($subtotal);
        session()->flash('success_cupon', 'Cupón aplicado con éxito.');
    }

    public function recalcularDescuento()
    {
        if ($this->cupon_aplicado) {
            $subtotal = $this->getCarritoSubtotal();
            $validacion = $this->cupon_aplicado->esValidoPara($subtotal);
            if (!$validacion['valido']) {
                $this->cupon_aplicado = null;
                $this->descuento_calculado = 0.00;
                session()->flash('error_cupon', 'El cupón se removió: ' . $validacion['mensaje']);
            } else {
                $this->descuento_calculado = $this->cupon_aplicado->calcularDescuentoPara($subtotal);
            }
        }
    }

    public function realizarPedido()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('login');
        }

        if (empty($this->carrito)) {
            session()->flash('error', 'Tu pedido actual está vacío. Agrega productos del catálogo primero.');
            return;
        }

        // Reglas de validación para el envío final
        $this->validate([
            'fecha_entrega' => 'nullable|date|after_or_equal:today',
        ], [
            'fecha_entrega.after_or_equal' => 'La fecha de entrega debe ser hoy o una fecha futura.',
        ]);

        // Validar Stock del backend de todos los productos en el carrito
        foreach ($this->carrito as $item) {
            $producto = Producto::find($item['producto_id']);
            if (!$producto || !$producto->activo) {
                session()->flash('error', "El producto '{$item['nombre']}' ya no está disponible.");
                return;
            }
            if ($item['cantidad'] > $producto->stock) {
                session()->flash('error', "Stock insuficiente para '{$item['nombre']}'. Disponible: {$producto->stock}.");
                return;
            }
        }

        try {
            DB::transaction(function () use ($usuario) {
                // 1. Obtener o crear de forma reactiva el registro de Cliente para el Usuario autenticado
                $cliente = Cliente::where('correo', $usuario->correo)->first();
                if (!$cliente) {
                    $cliente = Cliente::create([
                        'nombre' => $usuario->nombre,
                        'apellido' => $usuario->apellido,
                        'correo' => $usuario->correo,
                        'telefono' => $usuario->telefono,
                        'canal' => 'web', // Origen: Portal Web Autogestionado
                    ]);
                }

                // 2. Generar número de pedido único con el estándar de la tienda (máx 20 caracteres para PostgreSQL)
                $numeroPedido = 'PED-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -8));

                $subtotal = $this->getCarritoSubtotal();
                $descuentoReal = 0.00;

                // Validar y descontar cupón si está aplicado
                if ($this->cupon_aplicado) {
                    $cuponDb = \App\Models\Cupon::find($this->cupon_aplicado->id);
                    if ($cuponDb) {
                        $validacion = $cuponDb->esValidoPara($subtotal);
                        if ($validacion['valido']) {
                            $descuentoReal = $cuponDb->calcularDescuentoPara($subtotal);
                            $cuponDb->increment('veces_usado');
                        }
                    }
                }

                $totalConDescuento = max($subtotal - $descuentoReal, 0);

                // 3. Crear el Pedido (inicia en estado 'pendiente' para revisión del admin)
                $pedido = Pedido::create([
                    'cliente_id' => $cliente->id,
                    'usuario_id' => $usuario->id,
                    'cupon_id' => $this->cupon_aplicado ? $this->cupon_aplicado->id : null,
                    'numero_pedido' => $numeroPedido,
                    'estado' => 'pendiente',
                    'total' => $totalConDescuento,
                    'descuento' => $descuentoReal,
                    'monto_pagado' => 0.00,
                    'fecha_entrega' => $this->fecha_entrega ?: null,
                    'creado_en' => now(),
                    'actualizado_en' => now(),
                ]);

                // 4. Crear los Detalles del Pedido y descontar stock
                foreach ($this->carrito as $item) {
                    $producto = Producto::find($item['producto_id']);
                    
                    DetallePedido::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal'],
                        'personalizacion' => $item['personalizacion'] ?: null,
                    ]);

                    // Descontar stock y registrar el movimiento
                    $producto->actualizarStock(
                        $item['cantidad'],
                        'salida',
                        $usuario->id,
                        $pedido->id,
                        "Descuento de inventario por compra online de cliente"
                    );
                }
            });

            session()->flash('success', '¡Tu pedido ha sido registrado con éxito! Un administrador lo revisará pronto.');
            
            // Limpiar carrito y cupones
            $this->carrito = [];
            $this->cupon_aplicado = null;
            $this->descuento_calculado = 0.00;
            $this->codigo_cupon = '';
            
            // Redireccionar al historial
            $this->active_tab = 'pedidos';

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al procesar tu pedido: ' . $e->getMessage());
        }
    }

    public function updateProfile()
    {
        $usuarioId = Auth::id();
        if (!$usuarioId) return;

        $this->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario,' . $usuarioId,
            'correo' => 'required|email|max:150|unique:usuarios,correo,' . $usuarioId,
            'telefono' => 'nullable|string|max:20',
        ], [
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'correo.email' => 'Por favor, ingresa un correo válido.'
        ]);

        $usuario = Auth::user();
        $oldEmail = $usuario->correo;

        $usuario->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_usuario' => $this->nombre_usuario,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'actualizado_en' => now()
        ]);

        // Sincronizar reactivamente con el registro en la tabla de clientes si corresponde (buscando por el correo antiguo)
        $cliente = Cliente::where('correo', $oldEmail)->first();
        if ($cliente) {
            $cliente->update([
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'correo' => $this->correo,
                'telefono' => $this->telefono,
            ]);
        }

        session()->flash('success_perfil', 'Datos personales actualizados correctamente.');
    }

    public function updatePassword()
    {
        $usuario = Auth::user();
        if (!$usuario) return;

        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ]);

        if (!Hash::check($this->current_password, $usuario->clave)) {
            $this->addError('current_password', 'La contraseña actual no es correcta.');
            return;
        }

        $usuario->update([
            'clave' => $this->password,
            'actualizado_en' => now()
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('success_password', 'Contraseña cambiada con éxito.');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $usuario = Auth::user();
        
        // 1. Cargar Categorías para filtros
        $categorias = CategoriaProducto::orderBy('orden')->get();

        // 2. Buscar Productos Activos
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $query = Producto::where('activo', true)->with('categoria');

        if (!empty($this->search)) {
            $query->where(function ($q) use ($likeOperator) {
                $q->where('nombre', $likeOperator, '%' . $this->search . '%')
                  ->orWhere('sku', $likeOperator, '%' . $this->search . '%')
                  ->orWhere('descripcion', $likeOperator, '%' . $this->search . '%');
            });
        }

        if (!empty($this->categoria_filtro)) {
            $query->where('categoria_id', $this->categoria_filtro);
        }

        $productos = $query->orderBy('nombre')->get();

        // 3. Obtener el historial de Pedidos si el cliente tiene un registro
        $pedidos = collect();
        $resumen = [
            'total_pedidos' => 0,
            'total_invertido' => 0,
            'saldo_pendiente' => 0
        ];

        if ($usuario) {
            $cliente = Cliente::where('correo', $usuario->correo)->first();
            if ($cliente) {
                $pedidos = Pedido::where('cliente_id', $cliente->id)
                    ->orderByDesc('creado_en')
                    ->get();

                // Calcular resumen financiero simple del cliente
                $resumen['total_pedidos'] = $pedidos->count();
                $resumen['total_invertido'] = $pedidos->where('estado', '!=', 'cancelado')->sum('total');
                $resumen['saldo_pendiente'] = $pedidos->where('estado', '!=', 'cancelado')->sum(function($p) {
                    return $p->total - $p->monto_pagado;
                });
            }
        }

        // 4. Cargar producto para el modal de pedido si está abierto
        $productoSeleccionado = $this->producto_seleccionado_id ? Producto::find($this->producto_seleccionado_id) : null;

        // 5. Cargar pedido para el modal de detalle si está abierto
        $pedidoDetalle = $this->ver_pedido_id ? Pedido::with(['detalles.producto', 'cupon'])->find($this->ver_pedido_id) : null;

        // Mapear traducciones de estados para la vista
        $estados_traduccion = [
            'cotizacion' => 'Cotización',
            'pendiente' => 'Pendiente',
            'en_diseno' => 'En Diseño',
            'aprobado' => 'Aprobado',
            'en_produccion' => 'En Producción',
            'listo' => 'Listo para Retiro',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        return view('livewire.cliente.cliente-dashboard', [
            'categorias' => $categorias,
            'productos' => $productos,
            'pedidos' => $pedidos,
            'resumen' => $resumen,
            'productoSeleccionado' => $productoSeleccionado,
            'pedidoDetalle' => $pedidoDetalle,
            'estados_traduccion' => $estados_traduccion,
        ]);
    }
}
