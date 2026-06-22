<?php

namespace App\Livewire\Admin;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Usuario;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class DashboardContent extends Component
{
    // Filtros de fecha para el Dashboard
    public $fecha_inicio;
    public $fecha_fin;
    public $periodo = 'mes_actual'; // mes_actual, hoy, semana, ano, personalizado

    protected $queryString = [
        'fecha_inicio' => ['except' => ''],
        'fecha_fin' => ['except' => ''],
        'periodo' => ['except' => 'mes_actual']
    ];

    public function mount()
    {
        $this->actualizarFechasPorPeriodo();
    }

    public function updatedPeriodo()
    {
        $this->actualizarFechasPorPeriodo();
    }

    public function actualizarFechasPorPeriodo()
    {
        switch ($this->periodo) {
            case 'hoy':
                $this->fecha_inicio = date('Y-m-d');
                $this->fecha_fin = date('Y-m-d');
                break;
            case 'semana':
                $this->fecha_inicio = date('Y-m-d', strtotime('-6 days'));
                $this->fecha_fin = date('Y-m-d');
                break;
            case 'mes_actual':
                $this->fecha_inicio = date('Y-m-01');
                $this->fecha_fin = date('Y-m-d');
                break;
            case 'ano':
                $this->fecha_inicio = date('Y-01-01');
                $this->fecha_fin = date('Y-m-d');
                break;
            case 'personalizado':
                // Mantener las fechas ingresadas manualmente
                break;
        }
    }

    public function cambiarVista($vista)
    {
        $this->dispatch('cambiarVista', vista: $vista);
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        // Sanitizar fechas nulas o vacías para evitar errores de sintaxis en PostgreSQL
        if (empty($this->fecha_inicio)) {
            $this->fecha_inicio = date('Y-m-01');
        }
        if (empty($this->fecha_fin)) {
            $this->fecha_fin = date('Y-m-d');
        }

        $start = $this->fecha_inicio . ' 00:00:00';
        $end = $this->fecha_fin . ' 23:59:59';

        // 1. Totales Básicos
        $totalUsuarios = Usuario::count();
        $totalProductos = Producto::count();
        $stockCritico = Producto::whereRaw('stock <= stock_minimo')->count();

        // 2. Resumen de Métricas (KPIs del Período)
        $totalVentas = Pedido::whereBetween('creado_en', [$start, $end])
            ->where('estado', '!=', 'cancelado')
            ->sum('total') ?? 0;

        $pedidosHoy = Pedido::whereBetween('creado_en', [$start, $end])
            ->count() ?? 0;

        $cobrado = Pedido::whereBetween('creado_en', [$start, $end])
            ->where('estado', '!=', 'cancelado')
            ->sum('monto_pagado') ?? 0;

        $pendiente = Pedido::whereBetween('creado_en', [$start, $end])
            ->where('estado', '!=', 'cancelado')
            ->sum(DB::raw('total - monto_pagado')) ?? 0;

        // 3. Gráfico de Ventas Diarias (Agrupado por Fecha)
        $ventas_diarias = Pedido::selectRaw("DATE(creado_en) as fecha, SUM(total) as total_dia")
            ->whereBetween('creado_en', [$start, $end])
            ->where('estado', '!=', 'cancelado')
            ->groupBy(DB::raw("DATE(creado_en)"))
            ->orderBy("fecha")
            ->get();

        // 4. Distribución por Estado de Pedido
        $pedidos_por_estado = Pedido::selectRaw("estado, COUNT(*) as cantidad, SUM(total) as total_monto")
            ->whereBetween('creado_en', [$start, $end])
            ->groupBy("estado")
            ->get();

        // 5. Productos Más Vendidos (Top 5 para el widget del Dashboard)
        $productos_mas_vendidos = DB::table('detalles_pedido')
            ->join('productos', 'detalles_pedido.producto_id', '=', 'productos.id')
            ->join('pedidos', 'detalles_pedido.pedido_id', '=', 'pedidos.id')
            ->selectRaw('productos.nombre, productos.sku, SUM(detalles_pedido.cantidad) as total_cantidad, SUM(detalles_pedido.subtotal) as total_monto')
            ->whereBetween('pedidos.creado_en', [$start, $end])
            ->where('pedidos.estado', '!=', 'cancelado')
            ->groupBy('productos.id', 'productos.nombre', 'productos.sku')
            ->orderByDesc('total_monto')
            ->limit(5)
            ->get();

        // 6. Clientes VIP (Los que más compran)
        $clientes_vip = DB::table('pedidos')
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->selectRaw("clientes.nombre, clientes.apellido, clientes.nit_ci, clientes.correo, COUNT(pedidos.id) as total_pedidos, SUM(pedidos.total) as total_monto")
            ->whereBetween('pedidos.creado_en', [$start, $end])
            ->where('pedidos.estado', '!=', 'cancelado')
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.apellido', 'clientes.nit_ci', 'clientes.correo')
            ->orderByDesc('total_monto')
            ->limit(5)
            ->get();

        // 7. Canales de venta preferidos
        $canales_preferidos = DB::table('pedidos')
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->selectRaw("clientes.canal, COUNT(pedidos.id) as cantidad_pedidos, SUM(pedidos.total) as total_ventas")
            ->whereBetween('pedidos.creado_en', [$start, $end])
            ->where('pedidos.estado', '!=', 'cancelado')
            ->groupBy('clientes.canal')
            ->orderByDesc('total_ventas')
            ->get();

        $estados_traduccion = [
            'cotizacion' => 'Cotización',
            'pendiente' => 'Pendiente',
            'en_diseno' => 'En Diseño',
            'aprobado' => 'Aprobado',
            'en_produccion' => 'En Producción',
            'listo' => 'Listo',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        // Disparar datos de los gráficos para que se actualicen reactivamente en Chart.js
        $this->dispatch('updateDashboardChartsData', [
            'ventasLabels' => $ventas_diarias->pluck('fecha')->map(fn($f) => date('d/m', strtotime($f)))->toArray(),
            'ventasData' => $ventas_diarias->pluck('total_dia')->map(fn($v) => (float)$v)->toArray(),
            'estadosLabels' => $pedidos_por_estado->pluck('estado')->map(fn($e) => $estados_traduccion[$e] ?? $e)->toArray(),
            'estadosData' => $pedidos_por_estado->pluck('cantidad')->toArray(),
        ]);

        return view('livewire.admin.dashboard-content', [
            'totalUsuarios' => $totalUsuarios,
            'totalProductos' => $totalProductos,
            'stockCritico' => $stockCritico,
            'totalVentas' => $totalVentas,
            'pedidosHoy' => $pedidosHoy,
            'cobrado' => $cobrado,
            'pendiente' => $pendiente,
            'ventas_diarias' => $ventas_diarias,
            'pedidos_por_estado' => $pedidos_por_estado,
            'productos_mas_vendidos' => $productos_mas_vendidos,
            'clientes_vip' => $clientes_vip,
            'canales_preferidos' => $canales_preferidos,
            'estados_traduccion' => $estados_traduccion,
        ]);
    }
}
