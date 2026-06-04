<?php

namespace App\Livewire\Admin;

use App\Models\Cliente;
use App\Models\DetallePedido;
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

    // Búsqueda de clientes
    public $cliente_search = '';
    public $clientes_buscados = [];

    // Productos del pedido
    public $producto_actual_id;
    public $producto_actual_cantidad = 1;
    public $producto_actual_personalizacion = '';
    public $detalles = [];

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
        'monto_pagado.required' => 'El monto pagado es obligatorio.',
        'monto_pagado.numeric' => 'El monto pagado debe ser un número.',
        'monto_pagado.min' => 'El monto pagado no puede ser negativo.',
    ];

    public function render()
    {
        $pedidos = Pedido::with(['cliente', 'usuario'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_pedido', 'like', '%' . $this->search . '%')
                      ->orWhereHas('cliente', function($subQ) {
                          $subQ->where('nombre', 'like', '%' . $this->search . '%')
                               ->orWhere('apellido', 'like', '%' . $this->search . '%')
                               ->orWhere('correo', 'like', '%' . $this->search . '%');
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
        if (strlen($this->cliente_search) >= 2) {
            $this->clientes_buscados = Cliente::where(function($query) {
                $query->where('nombre', 'like', '%' . $this->cliente_search . '%')
                      ->orWhere('apellido', 'like', '%' . $this->cliente_search . '%')
                      ->orWhere('correo', 'like', '%' . $this->cliente_search . '%');
            })
            ->limit(5)
            ->get();
        } else {
            $this->clientes_buscados = [];
        }
    }

    public function seleccionarCliente($id)
    {
        $this->cliente_id = $id;
        $cliente = Cliente::find($id);
        $this->cliente_nombre = $cliente ? $cliente->nombre_completo . ' (' . $cliente->correo . ')' : '';
        $this->cliente_search = '';
        $this->clientes_buscados = [];
    }

    public function create()
    {
        $this->resetInputFields();
        $this->numero_pedido = 'PED-' . date('Ymd') . '-' . strtoupper(uniqid());
        $this->usuario_id = auth()->id();
        $this->estado = 'cotizacion';
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $pedido = Pedido::with(['detalles.producto', 'cliente'])->findOrFail($id);
        $this->pedido_id = $id;
        $this->cliente_id = $pedido->cliente_id;
        $this->cliente_nombre = $pedido->cliente ? $pedido->cliente->nombre_completo . ' (' . $pedido->cliente->correo . ')' : '';
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
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEstadoFiltro()
    {
        $this->resetPage();
    }
}