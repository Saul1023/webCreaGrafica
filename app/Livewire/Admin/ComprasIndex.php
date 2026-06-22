<?php

namespace App\Livewire\Admin;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\MovimientoStock;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComprasIndex extends Component
{
    use WithPagination;

    // Filtros de historial
    public $search_factura = '';
    public $filtro_proveedor = '';
    
    // Atributos del formulario de compra
    public $proveedor_id;
    public $numero_factura = '';
    public $detalles = []; // Items de la compra actual
    public $total = 0;

    // Buscador de productos
    public $producto_search = '';
    public $productos_buscados = [];

    // Control de modales
    public $isOpen = false;
    public $ver_compra_id = null; // Para ver detalle de compra existente

    protected $queryString = [
        'search_factura' => ['except' => ''],
        'filtro_proveedor' => ['except' => '']
    ];

    public function mount()
    {
        // Limpiar
    }

    public function updatedSearchFactura()
    {
        $this->resetPage();
    }

    public function updatedFiltroProveedor()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetErrorBag();
        $this->resetInputFields();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->proveedor_id = null;
        $this->numero_factura = '';
        $this->detalles = [];
        $this->total = 0;
        $this->producto_search = '';
        $this->productos_buscados = [];
    }

    public function create()
    {
        $this->openModal();
    }

    // Buscador interactivo de productos
    public function updatedProductoSearch()
    {
        if (strlen($this->producto_search) < 1) {
            $this->productos_buscados = [];
            return;
        }

        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $this->productos_buscados = Producto::where('activo', true)
            ->where(function($q) use ($likeOperator) {
                $q->where('nombre', $likeOperator, '%' . $this->producto_search . '%')
                  ->orWhere('sku', $likeOperator, '%' . $this->producto_search . '%');
            })
            ->limit(10)
            ->get();
    }

    public function seleccionarProducto($id)
    {
        $producto = Producto::find($id);
        if (!$producto) return;

        // Verificar si ya está en los detalles
        foreach ($this->detalles as $det) {
            if ($det['producto_id'] == $id) {
                session()->flash('warning', 'El producto ya está en la lista de compras.');
                $this->producto_search = '';
                $this->productos_buscados = [];
                return;
            }
        }

        $this->detalles[] = [
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 1,
            'costo_unitario' => 0.00,
            'subtotal' => 0.00
        ];

        $this->producto_search = '';
        $this->productos_buscados = [];
        $this->calcularTotal();
    }

    public function quitarProducto($index)
    {
        unset($this->detalles[$index]);
        $this->detalles = array_values($this->detalles); // Reindexar
        $this->calcularTotal();
    }

    public function updatedDetalles($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = intval($parts[0]);
            $field = $parts[1];

            if ($field === 'cantidad' || $field === 'costo_unitario') {
                $cantidad = max(1, intval($this->detalles[$index]['cantidad'] ?? 1));
                $costo = max(0.00, floatval($this->detalles[$index]['costo_unitario'] ?? 0));

                $this->detalles[$index]['cantidad'] = $cantidad;
                $this->detalles[$index]['costo_unitario'] = $costo;
                $this->detalles[$index]['subtotal'] = round($cantidad * $costo, 2);

                $this->calcularTotal();
            }
        }
    }

    // Actualiza cantidades o costos en tiempo real
    public function actualizarFila($index, $cantidad, $costo_unitario)
    {
        $cantidad = max(1, intval($cantidad));
        $costo_unitario = max(0.00, floatval($costo_unitario));

        $this->detalles[$index]['cantidad'] = $cantidad;
        $this->detalles[$index]['costo_unitario'] = $costo_unitario;
        $this->detalles[$index]['subtotal'] = round($cantidad * $costo_unitario, 2);

        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $this->total = 0;
        foreach ($this->detalles as $det) {
            $this->total += $det['subtotal'];
        }
        $this->total = round($this->total, 2);
    }

    public function store()
    {
        $this->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero_factura' => 'nullable|string|max:50',
        ], [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no es válido.',
            'numero_factura.max' => 'El número de factura no debe superar los 50 caracteres.'
        ]);

        if (empty($this->detalles)) {
            $this->addError('detalles', 'Debe agregar al menos un producto a la compra.');
            return;
        }

        // Validar filas individuales
        foreach ($this->detalles as $index => $det) {
            if ($det['cantidad'] <= 0) {
                $this->addError("detalles.{$index}.cantidad", 'La cantidad debe ser mayor que 0.');
                return;
            }
            if ($det['costo_unitario'] < 0) {
                $this->addError("detalles.{$index}.costo_unitario", 'El costo unitario no puede ser negativo.');
                return;
            }
        }

        try {
            DB::transaction(function() {
                // 1. Crear compra
                $compra = Compra::create([
                    'proveedor_id' => $this->proveedor_id,
                    'usuario_id' => Auth::id(),
                    'numero_factura' => $this->numero_factura ?: null,
                    'total' => $this->total,
                    'creado_en' => now(),
                    'actualizado_en' => now()
                ]);

                // 2. Crear detalles y actualizar stock
                foreach ($this->detalles as $det) {
                    DetalleCompra::create([
                        'compra_id' => $compra->id,
                        'producto_id' => $det['producto_id'],
                        'cantidad' => $det['cantidad'],
                        'costo_unitario' => $det['costo_unitario'],
                        'subtotal' => $det['subtotal'],
                        'creado_en' => now(),
                        'actualizado_en' => now()
                    ]);

                    // Incrementar el stock del producto
                    $producto = Producto::find($det['producto_id']);
                    if ($producto) {
                        $producto->actualizarStock(
                            $det['cantidad'],
                            'entrada',
                            Auth::id(),
                            null, // Sin pedido asociado
                            "Ingreso por reabastecimiento (Compra #{$compra->id})"
                        );
                    }
                }
            });

            session()->flash('message', 'Compra registrada con éxito. El inventario ha sido reabastecido.');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al registrar la compra: ' . $e->getMessage());
        }
    }

    public function verDetallesCompra($id)
    {
        $this->ver_compra_id = $id;
    }

    public function cerrarVerDetalles()
    {
        $this->ver_compra_id = null;
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        // Proveedores activos para el select
        $proveedores_select = Proveedor::activo()->orderBy('nombre')->get();

        // Buscar compras
        $query = Compra::with(['proveedor', 'usuario', 'detalles.producto']);

        if (strlen($this->search_factura) > 0) {
            $query->where('numero_factura', $likeOperator, '%' . $this->search_factura . '%');
        }

        if ($this->filtro_proveedor !== '') {
            $query->where('proveedor_id', $this->filtro_proveedor);
        }

        $compras = $query->orderByDesc('creado_en')->paginate(10);

        // Compra específica a ver detalles
        $compra_detalle = $this->ver_compra_id ? Compra::with(['proveedor', 'usuario', 'detalles.producto'])->find($this->ver_compra_id) : null;

        return view('livewire.admin.compras-index', [
            'compras' => $compras,
            'proveedores_select' => $proveedores_select,
            'compra_detalle' => $compra_detalle
        ]);
    }
}
