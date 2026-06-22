@extends('admin.reportes.pdf-layout')

@section('title', 'Reporte de Usuarios')
@section('report_title', 'Reporte General de Usuarios Registrados')

@section('content')
    <!-- Resumen KPIs -->
    <table class="kpis" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 32%; padding-right: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Usuarios</div>
                    <div class="kpi-value">{{ count($todos_usuarios) }}</div>
                </div>
            </td>
            <td style="width: 32%; padding-right: 15px; padding-left: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Usuarios Activos</div>
                    <div class="kpi-value text-success">{{ $todos_usuarios->where('activo', true)->count() }}</div>
                </div>
            </td>
            <td style="width: 32%; padding-left: 15px;">
                <div class="kpi-card">
                    <div class="kpi-title">Usuarios Inactivos</div>
                    <div class="kpi-value text-danger">{{ $todos_usuarios->where('activo', false)->count() }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de datos -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 25%;">Nombre Completo</th>
                <th style="width: 18%;">Nombre de Usuario</th>
                <th style="width: 22%;">Correo Electrónico</th>
                <th style="width: 12%;">Teléfono</th>
                <th style="width: 10%;">Rol</th>
                <th style="width: 8%; text-align: center;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todos_usuarios as $idx => $user)
            <tr>
                <td class="text-center font-bold" style="color: #64748b;">{{ $idx + 1 }}</td>
                <td class="font-bold">{{ $user->nombre }} {{ $user->apellido }}</td>
                <td style="font-family: monospace;">{{ $user->nombre_usuario }}</td>
                <td>{{ $user->correo }}</td>
                <td>{{ $user->telefono ?? 'N/A' }}</td>
                <td class="font-bold">{{ $user->rol->nombre ?? 'N/A' }}</td>
                <td class="text-center">
                    @if($user->activo)
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
