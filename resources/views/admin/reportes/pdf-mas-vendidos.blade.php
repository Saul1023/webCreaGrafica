@extends('admin.reportes.pdf-layout')

@section('title', 'Productos Más Vendidos')
@section('report_title', 'Ranking de Productos Más Vendidos')

@section('content')
    <!-- Resumen KPIs -->
    <table class="kpis" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 32%; padding-right: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Productos en Ranking</div>
                    <div class="kpi-value">{{ count($productos_mas_vendidos) }}</div>
                </div>
            </td>
            <td style="width: 32%; padding-right: 15px; padding-left: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Unidades Totales Vendidas</div>
                    <div class="kpi-value text-primary">{{ $productos_mas_vendidos->sum('total_cantidad') }}</div>
                </div>
            </td>
            <td style="width: 32%; padding-left: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Recaudación Acumulada</div>
                    <div class="kpi-value text-success">Bs. {{ number_format($productos_mas_vendidos->sum('total_monto'), 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de datos -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">Pos.</th>
                <th style="width: 44%;">Nombre del Producto</th>
                <th style="width: 16%;">SKU</th>
                <th style="width: 12%; text-align: center;">Unidades</th>
                <th style="width: 12%; text-align: right;">Total Ventas</th>
                <th style="width: 8%; text-align: right;">% Contrib.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos_mas_vendidos as $idx => $prod)
            <tr>
                <td class="text-center font-bold" style="color: #64748b; font-size: 10px;">{{ $idx + 1 }}</td>
                <td class="font-bold">{{ $prod->nombre }}</td>
                <td style="font-family: monospace;">{{ $prod->sku }}</td>
                <td class="text-center font-bold text-primary">{{ $prod->total_cantidad }}</td>
                <td class="text-right font-bold text-success">Bs. {{ number_format($prod->total_monto, 2) }}</td>
                <td class="text-right" style="font-family: monospace; color: #475569;">
                    {{ $ventas_totales > 0 ? round(($prod->total_monto / $ventas_totales) * 100, 1) : 0 }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center font-bold" style="padding: 20px; color: #64748b;">No existen registros de ventas en este período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
