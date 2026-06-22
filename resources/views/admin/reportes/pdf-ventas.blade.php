@extends('admin.reportes.pdf-layout')

@section('title', 'Reporte de Ventas')
@section('report_title', 'Reporte Detallado de Ventas e Ingresos')

@section('content')
    <!-- Resumen KPIs -->
    <table class="kpis" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 23%; padding-right: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Pedidos Totales</div>
                    <div class="kpi-value">{{ count($historial_ventas) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 10px; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Facturado</div>
                    <div class="kpi-value text-primary">Bs. {{ number_format($ventas_totales, 2) }}</div>
                </div>
            </td>
            <td style="width: 25%; padding-right: 10px; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Monto Cobrado</div>
                    <div class="kpi-value text-success">Bs. {{ number_format($cobrado, 2) }}</div>
                </div>
            </td>
            <td style="width: 27%; padding-left: 10px;">
                <div class="kpi-card">
                    <div class="kpi-title">Saldo Pendiente</div>
                    <div class="kpi-value text-warning">Bs. {{ number_format($pendiente, 2) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de datos -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">Nro.</th>
                <th style="width: 15%;">Fecha Reg.</th>
                <th style="width: 22%;">Cliente</th>
                <th style="width: 15%;">Vendedor</th>
                <th style="width: 10%; text-align: center;">Estado</th>
                <th style="width: 10%; text-align: right;">Cobrado</th>
                <th style="width: 10%; text-align: right;">Saldo</th>
                <th style="width: 10%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historial_ventas as $pedido)
            <tr>
                <td class="font-bold text-center" style="font-family: monospace;">#{{ $pedido->id }}</td>
                <td style="font-family: monospace;">{{ date('d/m/Y H:i', strtotime($pedido->creado_en)) }}</td>
                <td>
                    <div class="font-bold">{{ $pedido->cliente->nombre ?? 'N/A' }} {{ $pedido->cliente->apellido ?? '' }}</div>
                    <div style="font-size: 7.5px; color: #64748b; font-family: monospace;">CI/NIT: {{ $pedido->cliente->nit_ci ?? 'S/CI' }}</div>
                </td>
                <td>{{ $pedido->usuario->nombre ?? 'Sistema' }}</td>
                <td class="text-center font-bold">
                    @php
                        $badges = [
                            'cotizacion' => 'badge-secondary',
                            'pendiente' => 'badge-warning',
                            'en_diseno' => 'badge-info',
                            'aprobado' => 'badge-info',
                            'en_produccion' => 'badge-info',
                            'listo' => 'badge-success',
                            'entregado' => 'badge-success',
                            'cancelado' => 'badge-danger'
                        ];
                    @endphp
                    <span class="badge {{ $badges[$pedido->estado] ?? 'badge-secondary' }}">
                        {{ $estados_traduccion[$pedido->estado] ?? $pedido->estado }}
                    </span>
                </td>
                <td class="text-right font-bold text-success">Bs. {{ number_format($pedido->monto_pagado, 2) }}</td>
                <td class="text-right font-bold text-warning">Bs. {{ number_format($pedido->total - $pedido->monto_pagado, 2) }}</td>
                <td class="text-right font-bold" style="color: #0f172a;">Bs. {{ number_format($pedido->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center font-bold" style="padding: 20px; color: #64748b;">No existen registros de ventas en este período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
