<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - CREAGRAFICA</title>
    <style>
        @page {
            margin: 1.2cm 1.2cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333333;
            font-size: 10px;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #334155;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header td {
            border: none;
            padding: 0;
        }
        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .subtitle {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 2px;
        }
        .meta-info {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 15px;
            text-transform: uppercase;
            text-align: center;
        }
        .kpis {
            width: 100%;
            margin-bottom: 20px;
        }
        .kpi-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background-color: #f8fafc;
        }
        .kpi-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
        }
        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 8.5px;
            color: #334155;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-success {
            color: #16a34a;
        }
        .text-primary {
            color: #2563eb;
        }
        .text-warning {
            color: #d97706;
        }
        .text-danger {
            color: #dc2626;
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8px;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-info { background-color: #dbeafe; color: #1d4ed8; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .badge-secondary { background-color: #f1f5f9; color: #475569; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left; vertical-align: top;">
                    <div class="logo">CREAGRAFICA</div>
                    <div class="subtitle">Reportes del Sistema Administrativo</div>
                </td>
                <td class="meta-info" style="text-align: right; vertical-align: top;">
                    <p style="margin: 0; padding-bottom: 2px;"><strong>Generado por:</strong> {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</p>
                    <p style="margin: 0; padding-bottom: 2px;"><strong>Fecha Impresión:</strong> {{ date('d/m/Y H:i') }}</p>
                    <p style="margin: 0;"><strong>Rango de Datos:</strong> {{ date('d/m/Y', strtotime($fecha_inicio)) }} al {{ date('d/m/Y', strtotime($fecha_fin)) }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="title">
        @yield('report_title')
    </div>

    @yield('content')

    <div class="footer">
        CREAGRAFICA &copy; {{ date('Y') }} - Reporte Confidencial de Uso Interno - Generado Automáticamente
    </div>
</body>
</html>
