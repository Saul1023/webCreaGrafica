<?php

namespace App\Livewire\Admin;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Usuario;
use Livewire\Component;
use Livewire\Attributes\Layout; // <-- 1. Importamos el atributo oficial de Livewire 3
use Illuminate\Support\Facades\DB;

class DashboardContent extends Component
{
    public function cambiarVista($vista)
    {
        $this->dispatch('cambiarVista', vista: $vista);
    }

    // <-- 2. Añadimos el atributo aquí. Así Intelephense ya no marcará error en rojo.
    #[Layout('layouts.admin')]
    public function render()
    {
        // Totales básicos
        $totalUsuarios = Usuario::count();
        $totalProductos = Producto::count();

        // Ventas - usando 'creado_en' y 'entregado'
        try {
            $totalVentas = Pedido::where('estado', 'entregado')->sum('total') ?? 0;
            $pedidosHoy = Pedido::whereDate('creado_en', now())->count() ?? 0;
        } catch (\Exception $e) {
            $totalVentas = 0;
            $pedidosHoy = 0;
        }

        // Stock crítico
        try {
            $stockCritico = Producto::whereRaw('stock <= stock_minimo')->count() ?? 0;
        } catch (\Exception $e) {
            $stockCritico = 0;
        }

        // Top productos (más vendidos)
        try {
            $topProductos = Producto::withCount('detallesPedido')
                ->orderBy('detalles_pedido_count', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $topProductos = collect();
        }

        // 3. Limpiamos el return quitando el ->layout(...) final
        return view('livewire.admin.dashboard-content', [
            'totalUsuarios' => $totalUsuarios,
            'totalProductos' => $totalProductos,
            'totalVentas' => $totalVentas,
            'pedidosHoy' => $pedidosHoy,
            'stockCritico' => $stockCritico,
            'topProductos' => $topProductos,
        ]);
    }
}
