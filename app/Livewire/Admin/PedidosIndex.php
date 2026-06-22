<?php

namespace App\Livewire\Admin;

use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;

class PedidosIndex extends Component
{
 use WithPagination;

    // Filtros
    public $search = '';
    public $estado_filtro = '';
    public $perPage = 10;

    // Modal
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showDetalleModal = false;
    public $pedido_id;
    public $pedido_id_eliminar;
    public $pedido_numero_eliminar;

    // Formulario
    public $cliente_id;
    public $cliente_nombre = '';
    public $usuario_id;
    public $numero_pedido;
    public $estado;
    public $monto_pagado = 0;
    public $fecha_entrega;

    // Abonos / Pagos
    public $isOpenAbonosModal = false;
    public $monto_abono;
    public $metodo_pago_abono = 'efectivo';
    public $referencia_abono;
    public $abonosHistoricos = [];

    // Búsqueda de clientes
    public $cliente_search = '';
    public $clientes_buscados = [];

    // Registro de nuevo cliente
    public $mostrar_formulario_cliente = false;
    public $nuevo_cliente_nombre = '';
    public $nuevo_cliente_apellido = '';
    public $nuevo_cliente_correo = '';
    public $nuevo_cliente_telefono = '';
    public $nuevo_cliente_whatsapp = '';
    public $nuevo_cliente_nit_ci = '';
    public $nuevo_cliente_empresa = '';
    public $nuevo_cliente_canal = 'presencial';

    // Productos del pedido
    public $producto_actual_id;
    public $producto_actual_cantidad = 1;
    public $producto_actual_personalizacion = '';
    public $detalles = [];

    // Búsqueda de productos
    public $producto_search = '';
    public $productos_buscados = [];
    public $producto_actual_nombre = '';

    // Totales
    public $total = 0;

    // Detalle del pedido (para ver)
    public $detalle_pedido_actual;

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'fecha_entrega' => 'nullable|date',
        'estado' => 'required|string|in:cotizacion,pendiente,en_diseno,aprobado,en_produccion,listo,entregado,cancelado',
        'monto_pagado' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'cliente_id.required' => 'Debe seleccionar un cliente.',
        'fecha_entrega.date' => 'La fecha de entrega debe ser una fecha válida.',
        'monto_pagado.required' => 'El monto pagado es obligatorio.',
        'monto_pagado.numeric' => 'El monto pagado debe ser un número.',
        'monto_pagado.min' => 'El monto pagado no puede ser negativo.',
    ];

    public function render()
    {
        $driver = \DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $pedidos = Pedido::with(['cliente', 'usuario'])
            ->when($this->search, function($query) use ($likeOperator) {
                $query->where(function($q) use ($likeOperator) {
                    $q->where('numero_pedido', $likeOperator, '%' . $this->search . '%')
                      ->orWhereHas('cliente', function($subQ) use ($likeOperator) {
                          $subQ->where('nombre', $likeOperator, '%' . $this->search . '%')
                               ->orWhere('apellido', $likeOperator, '%' . $this->search . '%')
                               ->orWhere('correo', $likeOperator, '%' . $this->search . '%')
                               ->orWhere('nit_ci', $likeOperator, '%' . $this->search . '%')
                               ->orWhere(\DB::raw("nombre || ' ' || apellido"), $likeOperator, '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->estado_filtro, function($query) {
                $query->where('estado', $this->estado_filtro);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $estados = [
            'cotizacion' => 'Cotización',
            'pendiente' => 'Pendiente',
            'en_diseno' => 'En Diseño',
            'aprobado' => 'Aprobado',
            'en_produccion' => 'En Producción',
            'listo' => 'Listo',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        return view('livewire.admin.pedidos-index', [
            'pedidos' => $pedidos,
            'estados' => $estados,
            'productos' => Producto::where('activo', true)->orderBy('nombre')->get(),
        ])->layout('layouts.admin');
    }

    public function updatedClienteSearch()
    {
        $driver = \DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        // 1. Buscar en la tabla de clientes
        $clientesQuery = Cliente::query();
        if (strlen($this->cliente_search) >= 1) {
            $clientesQuery->where(function($query) use ($likeOperator) {
                $query->where('nombre', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('apellido', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('correo', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('nit_ci', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere(\DB::raw("nombre || ' ' || apellido"), $likeOperator, '%' . $this->cliente_search . '%');
            });
        }
        $clientes = $clientesQuery->limit(20)
            ->get()
            ->map(function($item) {
                $item->is_usuario = false;
                return $item;
            });

        // 2. Buscar en la tabla de usuarios con rol_id = 2 (Clientes de la tienda)
        $usuariosQuery = Usuario::where('rol_id', 2);
        if (strlen($this->cliente_search) >= 1) {
            $usuariosQuery->where(function($query) use ($likeOperator) {
                $query->where('nombre', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('apellido', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('correo', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere('telefono', $likeOperator, '%' . $this->cliente_search . '%')
                      ->orWhere(\DB::raw("nombre || ' ' || apellido"), $likeOperator, '%' . $this->cliente_search . '%');
            });
        }
        $usuarios = $usuariosQuery->limit(20)
            ->get()
            ->map(function($item) {
                $item->is_usuario = true;
                $item->nombre_completo = $item->nombre . ' ' . $item->apellido;
                $item->nit_ci = null;
                return $item;
            });

        // Combinar colecciones evitando duplicados por correo
        $combined = collect();
        
        foreach ($clientes as $c) {
            $combined->push($c);
        }

        foreach ($usuarios as $u) {
            $exists = $combined->contains(function($value) use ($u) {
                return $value->correo && $u->correo && strtolower($value->correo) === strtolower($u->correo);
            });
            if (!$exists) {
                $combined->push($u);
            }
        }

        $this->clientes_buscados = $combined->take(20)->all();
    }

    public function toggleClientesDropdown()
    {
        if (empty($this->clientes_buscados)) {
            $this->updatedClienteSearch();
        } else {
            $this->clientes_buscados = [];
        }
    }

    public function buscarClientes()
    {
        $this->updatedClienteSearch();
    }

    public function registrarCliente()
    {
        $this->validate([
            'nuevo_cliente_nombre' => 'required|string|max:80',
            'nuevo_cliente_apellido' => 'required|string|max:80',
            'nuevo_cliente_correo' => 'nullable|email|max:150',
            'nuevo_cliente_telefono' => 'nullable|string|max:20',
            'nuevo_cliente_whatsapp' => 'nullable|string|max:20',
            'nuevo_cliente_nit_ci' => 'nullable|string|max:20',
            'nuevo_cliente_empresa' => 'nullable|string|max:150',
            'nuevo_cliente_canal' => 'required|string|in:presencial,web,whatsapp,redes_sociales,referido',
        ], [
            'nuevo_cliente_nombre.required' => 'El nombre es obligatorio.',
            'nuevo_cliente_nombre.max' => 'El nombre no puede tener más de 80 caracteres.',
            'nuevo_cliente_apellido.required' => 'El apellido es obligatorio.',
            'nuevo_cliente_apellido.max' => 'El apellido no puede tener más de 80 caracteres.',
            'nuevo_cliente_correo.email' => 'El correo debe ser una dirección válida.',
            'nuevo_cliente_correo.max' => 'El correo no puede tener más de 150 caracteres.',
            'nuevo_cliente_telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'nuevo_cliente_whatsapp.max' => 'El WhatsApp no puede tener más de 20 caracteres.',
            'nuevo_cliente_nit_ci.max' => 'El NIT/CI no puede tener más de 20 caracteres.',
            'nuevo_cliente_empresa.max' => 'La empresa no puede tener más de 150 caracteres.',
        ]);

        $cliente = Cliente::create([
            'nombre' => $this->nuevo_cliente_nombre,
            'apellido' => $this->nuevo_cliente_apellido,
            'correo' => $this->nuevo_cliente_correo ?: null,
            'telefono' => $this->nuevo_cliente_telefono ?: null,
            'whatsapp' => $this->nuevo_cliente_whatsapp ?: null,
            'nit_ci' => $this->nuevo_cliente_nit_ci ?: null,
            'empresa' => $this->nuevo_cliente_empresa ?: null,
            'canal' => $this->nuevo_cliente_canal,
        ]);

        $this->cliente_id = $cliente->id;
        $this->cliente_nombre = $cliente->nombre_completo . ($cliente->nit_ci ? ' (CI: ' . $cliente->nit_ci . ')' : '') . ' - ' . ($cliente->correo ?? 'Sin correo');
        
        $this->resetNuevoClienteForm();
        
        session()->flash('message', 'Cliente registrado y seleccionado correctamente.');
    }

    public function resetNuevoClienteForm()
    {
        $this->nuevo_cliente_nombre = '';
        $this->nuevo_cliente_apellido = '';
        $this->nuevo_cliente_correo = '';
        $this->nuevo_cliente_telefono = '';
        $this->nuevo_cliente_whatsapp = '';
        $this->nuevo_cliente_nit_ci = '';
        $this->nuevo_cliente_empresa = '';
        $this->nuevo_cliente_canal = 'presencial';
        $this->mostrar_formulario_cliente = false;
    }

    public function seleccionarCliente($id, $isUsuario = false)
    {
        if ($isUsuario) {
            $usuario = Usuario::find($id);
            if ($usuario) {
                $cliente = Cliente::where('correo', $usuario->correo)->first();
                if (!$cliente) {
                    $cliente = Cliente::create([
                        'nombre' => $usuario->nombre,
                        'apellido' => $usuario->apellido,
                        'correo' => $usuario->correo,
                        'telefono' => $usuario->telefono,
                        'canal' => 'web',
                    ]);
                }
                $id = $cliente->id;
            }
        }

        $this->cliente_id = $id;
        $cliente = Cliente::find($id);
        $this->cliente_nombre = $cliente ? $cliente->nombre_completo . ($cliente->nit_ci ? ' (CI: ' . $cliente->nit_ci . ')' : '') . ' - ' . ($cliente->correo ?? 'Sin correo') : '';
        $this->cliente_search = '';
        $this->clientes_buscados = [];
    }

    public function updatedProductoSearch()
    {
        $driver = \DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $query = Producto::where('activo', true);

        if (strlen($this->producto_search) >= 1) {
            $query->where(function($q) use ($likeOperator) {
                $q->where('nombre', $likeOperator, '%' . $this->producto_search . '%')
                  ->orWhere('sku', $likeOperator, '%' . $this->producto_search . '%')
                  ->orWhere('descripcion', $likeOperator, '%' . $this->producto_search . '%');
            });
        }

        $this->productos_buscados = $query->orderBy('nombre')->limit(20)->get();
    }

    public function toggleProductosDropdown()
    {
        if (empty($this->productos_buscados)) {
            $this->updatedProductoSearch();
        } else {
            $this->productos_buscados = [];
        }
    }

    public function buscarProductos()
    {
        $this->updatedProductoSearch();
    }

    public function seleccionarProducto($id)
    {
        $this->producto_actual_id = $id;
        $producto = Producto::find($id);
        $this->producto_actual_nombre = $producto ? $producto->nombre . ' (Bs. ' . number_format($producto->precio, 2) . ') - Stock: ' . $producto->stock : '';
        $this->producto_search = '';
        $this->productos_buscados = [];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->numero_pedido = 'PED-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -8));
        $this->usuario_id = auth()->id();
        $this->estado = 'cotizacion';
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $pedido = Pedido::with(['detalles.producto', 'cliente'])->findOrFail($id);
        $this->pedido_id = $id;
        $this->cliente_id = $pedido->cliente_id;
        $this->cliente_nombre = $pedido->cliente ? $pedido->cliente->nombre_completo . ($pedido->cliente->nit_ci ? ' (CI: ' . $pedido->cliente->nit_ci . ')' : '') . ' - ' . ($pedido->cliente->correo ?? 'Sin correo') : '';
        $this->numero_pedido = $pedido->numero_pedido;
        $this->estado = $pedido->estado;
        $this->monto_pagado = $pedido->monto_pagado;
        $this->fecha_entrega = $pedido->fecha_entrega ? $pedido->fecha_entrega->format('Y-m-d') : null;
        $this->total = $pedido->total;

        // Cargar detalles
        $this->detalles = [];
        foreach ($pedido->detalles as $detalle) {
            $this->detalles[] = [
                'producto_id' => $detalle->producto_id,
                'producto_nombre' => $detalle->producto->nombre,
                'producto_precio' => $detalle->precio_unitario,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal,
                'personalizacion' => $detalle->personalizacion,
            ];
        }

        $this->isOpen = true;
    }

    public function verDetalle($id)
    {
        $this->detalle_pedido_actual = Pedido::with(['cliente', 'usuario', 'detalles.producto'])
            ->findOrFail($id);
        $this->showDetalleModal = true;
    }

    public function closeDetalleModal()
    {
        $this->showDetalleModal = false;
        $this->detalle_pedido_actual = null;
    }

    public function getDisponibleStock($productoId)
    {
        $producto = Producto::find($productoId);
        if (!$producto) return 0;

        $disponible = $producto->stock;

        // Si estamos editando un pedido existente
        if ($this->pedido_id) {
            $pedidoOriginal = Pedido::find($this->pedido_id);
            // Solo si el estado original del pedido no era 'cancelado' (ya que el stock no se había liberado)
            if ($pedidoOriginal && $pedidoOriginal->estado !== 'cancelado') {
                $originalDetalle = DetallePedido::where('pedido_id', $this->pedido_id)
                    ->where('producto_id', $productoId)
                    ->first();
                if ($originalDetalle) {
                    $disponible += $originalDetalle->cantidad;
                }
            }
        }

        return $disponible;
    }

    public function agregarProducto()
    {
        $this->validate([
            'producto_actual_id' => 'required|exists:productos,id',
            'producto_actual_cantidad' => 'required|integer|min:1',
        ]);

        $producto = Producto::find($this->producto_actual_id);
        $disponible = $this->getDisponibleStock($producto->id);

        // Calcular la cantidad total de este producto en el formulario (incluyendo lo que ya se agregó)
        $cantidadExistenteEnFormulario = 0;
        foreach ($this->detalles as $item) {
            if ($item['producto_id'] == $producto->id) {
                $cantidadExistenteEnFormulario = $item['cantidad'];
                break;
            }
        }
        $nuevaCantidadTotal = $cantidadExistenteEnFormulario + $this->producto_actual_cantidad;

        // Verificar stock si no está cancelado
        if ($this->estado !== 'cancelado' && $nuevaCantidadTotal > $disponible) {
            $this->addError('producto_actual_cantidad', 'Stock insuficiente. Disponible total: ' . $disponible . ' (Ya agregados en formulario: ' . $cantidadExistenteEnFormulario . ')');
            return;
        }

        $detalle = [
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_precio' => $producto->precio,
            'cantidad' => $this->producto_actual_cantidad,
            'precio_unitario' => $producto->precio,
            'subtotal' => $producto->precio * $this->producto_actual_cantidad,
            'personalizacion' => $this->producto_actual_personalizacion,
        ];

        // Verificar si el producto ya está en la lista
        $existe = false;
        foreach ($this->detalles as $index => $item) {
            if ($item['producto_id'] == $producto->id) {
                $this->detalles[$index]['cantidad'] += $this->producto_actual_cantidad;
                $this->detalles[$index]['subtotal'] = $this->detalles[$index]['cantidad'] * $this->detalles[$index]['precio_unitario'];
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $this->detalles[] = $detalle;
        }

        $this->calcularTotal();

        // Resetear campos
        $this->producto_actual_id = null;
        $this->producto_actual_nombre = '';
        $this->producto_search = '';
        $this->productos_buscados = [];
        $this->producto_actual_cantidad = 1;
        $this->producto_actual_personalizacion = '';
        $this->resetValidation();
    }

    public function quitarProducto($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles);
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = array_sum(array_column($this->detalles, 'subtotal'));
    }

    public function store()
    {
        $this->usuario_id = auth()->id();
        $this->validate();

        // Validaciones lógicas adicionales
        if (empty($this->detalles)) {
            $this->addError('productos', 'Debe agregar al menos un producto al pedido.');
            return;
        }

        if ($this->monto_pagado > $this->total) {
            $this->addError('monto_pagado', 'El monto pagado (Bs. ' . number_format($this->monto_pagado, 2) . ') no puede superar el total del pedido (Bs. ' . number_format($this->total, 2) . ').');
            return;
        }

        // Validar stock de todos los productos en el formulario
        if ($this->estado !== 'cancelado') {
            foreach ($this->detalles as $detalle) {
                $disponible = $this->getDisponibleStock($detalle['producto_id']);
                if ($detalle['cantidad'] > $disponible) {
                    $this->addError('productos', 'Stock insuficiente para el producto: ' . $detalle['producto_nombre'] . '. Disponible total: ' . $disponible);
                    return;
                }
            }
        }

        $data = [
            'cliente_id' => $this->cliente_id,
            'usuario_id' => $this->usuario_id,
            'numero_pedido' => $this->numero_pedido,
            'estado' => $this->estado,
            'total' => $this->total,
            'monto_pagado' => $this->monto_pagado,
            'fecha_entrega' => $this->fecha_entrega ?: null,
            'actualizado_en' => now(),
        ];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
                if ($this->pedido_id) {
                    // Actualizar pedido
                    $pedido = Pedido::findOrFail($this->pedido_id);

                    // Restaurar stock de productos anteriores (solo si no estaba cancelado)
                    if ($pedido->estado !== 'cancelado') {
                        foreach ($pedido->detalles as $detalle) {
                            $producto = Producto::find($detalle->producto_id);
                            if ($producto) {
                                $producto->actualizarStock(
                                    $detalle->cantidad, 
                                    'entrada', 
                                    auth()->id(), 
                                    $pedido->id, 
                                    "Restauración de stock por actualización de pedido"
                                );
                            }
                        }
                    }

                    $pedido->update($data);

                    // Eliminar detalles antiguos
                    DetallePedido::where('pedido_id', $this->pedido_id)->delete();
                } else {
                    $data['creado_en'] = now();
                    $pedido = Pedido::create($data);
                }

                // Crear nuevos detalles (sin disparar el evento boot de actualizarTotal para evitar redundancia de queries)
                DetallePedido::withoutEvents(function () use ($pedido) {
                    foreach ($this->detalles as $detalle) {
                        DetallePedido::create([
                            'pedido_id' => $pedido->id,
                            'producto_id' => $detalle['producto_id'],
                            'cantidad' => $detalle['cantidad'],
                            'precio_unitario' => $detalle['precio_unitario'],
                            'subtotal' => $detalle['subtotal'],
                            'personalizacion' => $detalle['personalizacion'] ?? null,
                        ]);
                    }
                });

                // Actualizar stock de nuevos detalles (solo si el pedido no está cancelado)
                if ($this->estado !== 'cancelado') {
                    foreach ($this->detalles as $detalle) {
                        $producto = Producto::find($detalle['producto_id']);
                        if ($producto) {
                            $producto->actualizarStock(
                                $detalle['cantidad'], 
                                'salida', 
                                auth()->id(), 
                                $pedido->id, 
                                "Descuento de stock por pedido"
                            );
                        }
                    }
                }
            });

            session()->flash('message', $this->pedido_id ? 'Pedido actualizado correctamente.' : 'Pedido creado correctamente.');
            $this->closeModal();
            $this->resetPage();

        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el pedido: ' . $e->getMessage());
        }
    }

    public function cambiarEstado($id, $estado)
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($id, $estado) {
                $pedido = Pedido::findOrFail($id);
                $estadoAnterior = $pedido->estado;

                if ($estadoAnterior === $estado) {
                    return;
                }

                $pedido->estado = $estado;
                $pedido->save();

                // Si se cancela, restaurar stock (solo si no estaba ya cancelado)
                if ($estado === 'cancelado' && $estadoAnterior !== 'cancelado') {
                    foreach ($pedido->detalles as $detalle) {
                        $producto = Producto::find($detalle->producto_id);
                        if ($producto) {
                            $producto->actualizarStock(
                                $detalle->cantidad, 
                                'entrada', 
                                auth()->id(), 
                                $pedido->id, 
                                "Restauración de stock por cancelación de pedido"
                            );
                        }
                    }
                }

                // Si se reactiva desde cancelado, descontar stock nuevamente
                if ($estadoAnterior === 'cancelado' && $estado !== 'cancelado') {
                    // Validar primero si hay stock suficiente para reactivar
                    foreach ($pedido->detalles as $detalle) {
                        $producto = Producto::find($detalle->producto_id);
                        if ($producto && $producto->stock < $detalle->cantidad) {
                            throw new \Exception('No hay suficiente stock disponible para reactivar el pedido. Producto: ' . $producto->nombre . ' (Requerido: ' . $detalle->cantidad . ', Disponible: ' . $producto->stock . ')');
                        }
                    }

                    foreach ($pedido->detalles as $detalle) {
                        $producto = Producto::find($detalle->producto_id);
                        if ($producto) {
                            $producto->actualizarStock(
                                $detalle->cantidad, 
                                'salida', 
                                auth()->id(), 
                                $pedido->id, 
                                "Descuento de stock por reactivación de pedido"
                            );
                        }
                    }
                }
            });

            session()->flash('message', 'Estado del pedido actualizado.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->resetPage();
    }

    public function confirmDelete($id, $numero)
    {
        $this->pedido_id_eliminar = $id;
        $this->pedido_numero_eliminar = $numero;
        $this->showDeleteModal = true;
    }

    public function deletePedido()
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                $pedido = Pedido::findOrFail($this->pedido_id_eliminar);

                // Restaurar stock (solo si no estaba ya cancelado)
                if ($pedido->estado !== 'cancelado') {
                    foreach ($pedido->detalles as $detalle) {
                        $producto = Producto::find($detalle->producto_id);
                        if ($producto) {
                            $producto->actualizarStock(
                                $detalle->cantidad, 
                                'entrada', 
                                auth()->id(), 
                                $pedido->id, 
                                "Restauración de stock por eliminación de pedido"
                            );
                        }
                    }
                }

                $pedido->delete();
            });

            session()->flash('message', 'Pedido eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }

        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->pedido_id = null;
        $this->cliente_id = null;
        $this->cliente_nombre = '';
        $this->numero_pedido = '';
        $this->estado = 'cotizacion';
        $this->monto_pagado = 0;
        $this->fecha_entrega = null;
        $this->detalles = [];
        $this->total = 0;
        $this->cliente_search = '';
        $this->clientes_buscados = [];
        $this->producto_actual_id = null;
        $this->producto_actual_cantidad = 1;
        $this->producto_actual_personalizacion = '';
        $this->producto_search = '';
        $this->productos_buscados = [];
        $this->producto_actual_nombre = '';
        
        $this->nuevo_cliente_nombre = '';
        $this->nuevo_cliente_apellido = '';
        $this->nuevo_cliente_correo = '';
        $this->nuevo_cliente_telefono = '';
        $this->nuevo_cliente_whatsapp = '';
        $this->nuevo_cliente_nit_ci = '';
        $this->nuevo_cliente_empresa = '';
        $this->nuevo_cliente_canal = 'presencial';
        $this->mostrar_formulario_cliente = false;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEstadoFiltro()
    {
        $this->resetPage();
    }

    // --- MÉTODOS DE ABONOS ---

    public function abrirModalAbonos($pedidoId)
    {
        $pedido = Pedido::with('transaccionesCaja.usuario')->findOrFail($pedidoId);
        $this->pedido_id = $pedidoId;
        $this->monto_abono = $pedido->saldo_pendiente; // Sugerir el saldo pendiente por defecto
        $this->metodo_pago_abono = 'efectivo';
        $this->referencia_abono = '';
        $this->abonosHistoricos = $pedido->transaccionesCaja;
        $this->isOpenAbonosModal = true;
    }

    public function registrarAbono()
    {
        $pedido = Pedido::findOrFail($this->pedido_id);

        if ($pedido->estado === 'cancelado') {
            session()->flash('error_abono', 'No se pueden registrar pagos en un pedido cancelado.');
            return;
        }

        $this->validate([
            'monto_abono' => 'required|numeric|min:0.01|max:' . $pedido->saldo_pendiente,
            'metodo_pago_abono' => 'required|in:efectivo,qr,transferencia,otro',
            'referencia_abono' => 'nullable|max:100'
        ], [
            'monto_abono.required' => 'El monto del abono es obligatorio.',
            'monto_abono.numeric' => 'Debe ingresar un valor numérico.',
            'monto_abono.min' => 'El abono debe ser mayor a Bs. 0.00.',
            'monto_abono.max' => 'El abono no puede superar el saldo pendiente del pedido (Bs. ' . number_format($pedido->saldo_pendiente, 2) . ').'
        ]);

        // REGLA: Exigir caja física abierta para registrar pagos en el panel administrativo
        $caja = \App\Models\Caja::abierta()->first();
        if (!$caja) {
            $this->addError('monto_abono', 'No hay ninguna caja física abierta. Por favor, realice la apertura de caja primero.');
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function() use ($pedido, $caja) {
                // 1. Crear transacción de caja
                \App\Models\TransaccionCaja::create([
                    'caja_id' => $caja->id,
                    'usuario_id' => auth()->id(),
                    'pedido_id' => $pedido->id,
                    'tipo' => 'ingreso',
                    'concepto' => 'Abono de pedido ' . $pedido->numero_pedido,
                    'monto' => $this->monto_abono,
                    'metodo_pago' => $this->metodo_pago_abono,
                    'referencia' => $this->referencia_abono ? trim($this->referencia_abono) : null,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ]);

                // 2. Incrementar monto pagado en el pedido
                $pedido->registrarPago($this->monto_abono);
            });

            session()->flash('message', 'Abono registrado correctamente.');
            $this->cerrarModalAbonos();
            $this->resetPage();

        } catch (\Exception $e) {
            session()->flash('error_abono', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function cerrarModalAbonos()
    {
        $this->isOpenAbonosModal = false;
        $this->pedido_id = null;
        $this->monto_abono = null;
        $this->metodo_pago_abono = 'efectivo';
        $this->referencia_abono = '';
        $this->abonosHistoricos = [];
        $this->resetValidation();
    }
}