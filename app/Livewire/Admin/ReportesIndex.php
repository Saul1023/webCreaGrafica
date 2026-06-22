<?php

namespace App\Livewire\Admin;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Usuario;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ReportesIndex extends Component
{
    // Filtros de fecha para la descarga de PDFs
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

    public function render()
    {
        // Sanitizar fechas nulas o vacías para evitar errores de sintaxis en PostgreSQL
        if (empty($this->fecha_inicio)) {
            $this->fecha_inicio = date('Y-m-01');
        }
        if (empty($this->fecha_fin)) {
            $this->fecha_fin = date('Y-m-d');
        }

        return view('livewire.admin.reportes-index')->layout('layouts.admin');
    }

    public function descargarPdf($modulo)
    {
        // Sanitizar fechas nulas o vacías
        if (empty($this->fecha_inicio)) {
            $this->fecha_inicio = date('Y-m-01');
        }
        if (empty($this->fecha_fin)) {
            $this->fecha_fin = date('Y-m-d');
        }

        $start = $this->fecha_inicio . ' 00:00:00';
        $end = $this->fecha_fin . ' 23:59:59';

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

        $data = [
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estados_traduccion' => $estados_traduccion,
        ];

        switch ($modulo) {
            case 'usuarios':
                $data['todos_usuarios'] = Usuario::with('rol')->orderBy('nombre')->get();
                $pdfView = 'admin.reportes.pdf-usuarios';
                $nombreArchivo = 'reporte_usuarios_' . date('Y-m-d') . '.pdf';
                break;

            case 'productos':
                $data['todos_productos'] = Producto::with('categoria')->orderBy('nombre')->get();
                $pdfView = 'admin.reportes.pdf-productos';
                $nombreArchivo = 'reporte_productos_' . date('Y-m-d') . '.pdf';
                break;

            case 'ventas':
                $data['historial_ventas'] = Pedido::with(['cliente', 'usuario'])
                    ->whereBetween('creado_en', [$start, $end])
                    ->orderByDesc('creado_en')
                    ->get();
                $data['ventas_totales'] = Pedido::whereBetween('creado_en', [$start, $end])
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total');
                $data['cobrado'] = Pedido::whereBetween('creado_en', [$start, $end])
                    ->where('estado', '!=', 'cancelado')
                    ->sum('monto_pagado');
                $data['pendiente'] = Pedido::whereBetween('creado_en', [$start, $end])
                    ->where('estado', '!=', 'cancelado')
                    ->sum(DB::raw('total - monto_pagado'));
                $pdfView = 'admin.reportes.pdf-ventas';
                $nombreArchivo = 'reporte_ventas_' . date('Y-m-d') . '.pdf';
                break;

            case 'mas-vendidos':
                $data['productos_mas_vendidos'] = DB::table('detalles_pedido')
                    ->join('productos', 'detalles_pedido.producto_id', '=', 'productos.id')
                    ->join('pedidos', 'detalles_pedido.pedido_id', '=', 'pedidos.id')
                    ->selectRaw('productos.nombre, productos.sku, SUM(detalles_pedido.cantidad) as total_cantidad, SUM(detalles_pedido.subtotal) as total_monto')
                    ->whereBetween('pedidos.creado_en', [$start, $end])
                    ->where('pedidos.estado', '!=', 'cancelado')
                    ->groupBy('productos.id', 'productos.nombre', 'productos.sku')
                    ->orderByDesc('total_monto')
                    ->limit(50)
                    ->get();
                $data['ventas_totales'] = Pedido::whereBetween('creado_en', [$start, $end])
                    ->where('estado', '!=', 'cancelado')
                    ->sum('total');
                $pdfView = 'admin.reportes.pdf-mas-vendidos';
                $nombreArchivo = 'reporte_productos_mas_vendidos_' . date('Y-m-d') . '.pdf';
                break;

            default:
                return;
        }

        // Generar PDF usando Facade de DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($pdfView, $data);
        
        // Retornar descarga directa en stream
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $nombreArchivo);
    }
}
