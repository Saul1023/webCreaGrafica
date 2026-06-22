@extends('admin.reportes.pdf-layout')

@section('title', 'Reporte de Productos')
@section('report_title', 'Reporte General de Inventario y Catálogo de Productos')

@section('content')
    <!-- Resumen KPIs -->
    <table class="kpis" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 23%; padding-right: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Productos Registrados</div>
                    <div class="kpi-value">{{ count($todos_productos) }}</div>
                </div>
            </td>
            <td style="width: 23%; padding-right: 10px; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Stock Total Unidades</div>
                    <div class="kpi-value text-primary">{{ $todos_productos->sum('stock') }}</div>
                </div>
            </td>
            <td style="width: 31%; padding-right: 10px; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Valoración de Stock</div>
                    <div class="kpi-value text-success">Bs. {{ number_format($todos_productos->sum(fn($p) => $p->precio * $p->stock), 2) }}</div>
                </div>
            </td>
            <td style="width: 23%; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Stock Crítico</div>
                    <div class="kpi-value text-danger">{{ $todos_productos->where('stock_bajo', true)->count() }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de datos -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">SKU</th>
                <th style="width: 33%;">Nombre del Producto</th>
                <th style="width: 18%;">Categoría</th>
                <th style="width: 12%; text-align: right;">Precio Unit.</th>
                <th style="width: 9%; text-align: center;">Stock</th>
                <th style="width: 9%; text-align: center;">Mínimo</th>
                <th style="width: 7%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todos_productos as $prod)
            <tr>
                <td style="font-family: monospace;">{{ $prod->sku }}</td>
                <td class="font-bold">{{ $prod->nombre }}</td>
                <td>{{ $prod->categoria->nombre ?? 'Sin categoría' }}</td>
                <td class="text-right font-bold">Bs. {{ number_format($prod->precio, 2) }}</td>
                <td class="text-center font-bold @if($prod->stock_bajo) text-danger @endif" style="@if($prod->stock_bajo) background-color: #fef2f2; @endif">
                    {{ $prod->stock }}
                </td>
                <td class="text-center" style="color: #64748b;">{{ $prod->stock_minimo }}</td>
                <td class="text-center">
                    @if($prod->activo ?? true)
                        <span class="badge badge-success">Activo</span>
                    @else
                        <span class="badge badge-danger">Inactivo</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
