<div>
    {{-- Inyectar Chart.js para gráficos dinámicos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Encabezado de Bienvenida -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-800">Hola, {{ Auth::user()->nombre }} 👋</h2>
            <p class="text-sm text-gray-500 mt-1">Aquí tienes el resumen y analítica en tiempo real de CREAGRAFICA.</p>
        </div>
    </div>

    {{-- Filtros de Fecha para el Dashboard --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-8 border border-gray-100">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Período del Dashboard</label>
                <select wire:model.live="periodo" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="hoy">Hoy</option>
                    <option value="semana">Últimos 7 días</option>
                    <option value="mes_actual">Este Mes</option>
                    <option value="ano">Este Año</option>
                    <option value="personalizado">Personalizado</option>
                </select>
            </div>

            @if($periodo === 'personalizado')
            <div class="animate-fadeIn">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha Inicio</label>
                <input type="date" wire:model.live="fecha_inicio" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="animate-fadeIn">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Fecha Fin</label>
                <input type="date" wire:model.live="fecha_fin" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            @else
            <div class="col-span-2 text-xs text-gray-400 self-center pb-2.5 italic">
                Rango analizado: <strong>{{ date('d/m/Y', strtotime($fecha_inicio)) }}</strong> al <strong>{{ date('d/m/Y', strtotime($fecha_fin)) }}</strong>
            </div>
            @endif
        </div>
    </div>

    {{-- Rejilla de Tarjetas Analíticas (KPIs del Período) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Ventas Totales -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-6 text-white transform hover:scale-[1.02] transition duration-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 text-xs font-semibold uppercase tracking-wider">Ventas Totales</p>
                    <h3 class="text-3xl font-extrabold mt-1">Bs. {{ number_format($totalVentas, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-blue-100 mt-4 flex items-center gap-1.5">
                <i class="fas fa-shopping-basket"></i>
                <span>Pedidos no cancelados del período</span>
            </div>
        </div>

        <!-- Monto Cobrado -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-md p-6 text-white transform hover:scale-[1.02] transition duration-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wider">Monto Cobrado</p>
                    <h3 class="text-3xl font-extrabold mt-1">Bs. {{ number_format($cobrado, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-hand-holding-dollar text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-emerald-100 mt-4 flex items-center gap-1.5">
                <i class="fas fa-check-circle"></i>
                <span>Pagos y adelantos registrados</span>
            </div>
        </div>

        <!-- Saldo Pendiente -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-md p-6 text-white transform hover:scale-[1.02] transition duration-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-amber-100 text-xs font-semibold uppercase tracking-wider">Saldo Pendiente</p>
                    <h3 class="text-3xl font-extrabold mt-1">Bs. {{ number_format($pendiente, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-amber-100 mt-4 flex items-center gap-1.5">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Por cobrar de pedidos en período</span>
            </div>
        </div>

        <!-- Cantidad de Pedidos -->
        <div class="bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl shadow-md p-6 text-white transform hover:scale-[1.02] transition duration-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-purple-100 text-xs font-semibold uppercase tracking-wider">Cantidad Pedidos</p>
                    <h3 class="text-3xl font-extrabold mt-1">{{ $pedidosHoy }}</h3>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-purple-100 mt-4 flex items-center gap-1.5">
                <i class="fas fa-file-invoice"></i>
                <span>Pedidos ingresados en período</span>
            </div>
        </div>

    </div>

    {{-- Fila Adicional: Accesos Rápidos / Métricas Críticas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- Botón Usuarios (Control Directo) -->
        <button wire:click="cambiarVista('usuarios')"
            class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-indigo-500 hover:bg-indigo-50/10 hover:shadow transition duration-200 text-left flex justify-between items-center w-full">
            <div class="flex items-center gap-4">
                <div class="bg-indigo-50 p-3.5 rounded-lg text-indigo-600">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">Control de Usuarios</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Administrar cuentas y accesos del sistema</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-2xl font-extrabold text-gray-800">{{ $totalUsuarios }}</span>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Activos</span>
            </div>
        </button>

        <!-- Botón Stock Crítico (Control Directo) -->
        <button wire:click="cambiarVista('productos')"
            class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-rose-500 hover:bg-rose-50/10 hover:shadow transition duration-200 text-left flex justify-between items-center w-full">
            <div class="flex items-center gap-4">
                <div class="bg-rose-50 p-3.5 rounded-lg text-rose-600">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-lg">Stock Bajo / Crítico</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Productos que requieren reabastecimiento</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-2xl font-extrabold text-rose-600">{{ $stockCritico }}</span>
                <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Alerta</span>
            </div>
        </button>

    </div>

    {{-- Sección de Gráficos Analíticos --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Gráfico Ventas Diarias -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 lg:col-span-2">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span>
                <span>Tendencia de Ventas (Bs.) en el período</span>
            </h3>
            <div class="relative h-[320px]" wire:ignore>
                <canvas id="chartVentasDashboard"></canvas>
            </div>
        </div>

        <!-- Gráfico Distribución por Estado -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-purple-600 rounded-full"></span>
                <span>Distribución de Estados</span>
            </h3>
            <div class="relative h-[320px] flex items-center justify-center" wire:ignore>
                <canvas id="chartEstadosDashboard"></canvas>
            </div>
        </div>

    </div>

    {{-- Tablas de Detalle: Productos más Vendidos y Clientes VIP --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Top Productos Más Vendidos -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-trophy text-yellow-500 text-lg"></i>
                    <span>Productos Más Vendidos (Top 5)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b">
                            <th class="pb-3">Producto</th>
                            <th class="pb-3 text-center">Unidades</th>
                            <th class="pb-3 text-right">Monto Facturado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($productos_mas_vendidos as $prod)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5">
                                <div class="font-medium text-gray-800">{{ $prod->nombre }}</div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $prod->sku }}</div>
                            </td>
                            <td class="py-3.5 text-center font-semibold text-gray-700">{{ $prod->total_cantidad }}</td>
                            <td class="py-3.5 text-right font-bold text-green-600">Bs. {{ number_format($prod->total_monto, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400 italic">No hay productos vendidos en este período.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Clientes VIP -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-star text-amber-500 text-lg"></i>
                    <span>Clientes VIP (Mayor Gasto)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b">
                            <th class="pb-3">Cliente</th>
                            <th class="pb-3 text-center">Pedidos</th>
                            <th class="pb-3 text-right">Monto Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clientes_vip as $client)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3.5">
                                <div class="font-medium text-gray-800">{{ $client->nombre }} {{ $client->apellido }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ $client->correo }} @if($client->nit_ci) | CI: {{ $client->nit_ci }} @endif
                                </div>
                            </td>
                            <td class="py-3.5 text-center font-semibold text-gray-700">{{ $client->total_pedidos }}</td>
                            <td class="py-3.5 text-right font-bold text-blue-600">Bs. {{ number_format($client->total_monto, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400 italic">No hay pedidos registrados en este período.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Canales de Venta Preferidos --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-share-nodes text-blue-500 text-lg"></i>
            <span>Ventas por Canal de Registro</span>
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead>
                    <tr class="text-left text-gray-400 font-semibold uppercase tracking-wider text-xs border-b">
                        <th class="pb-3">Canal</th>
                        <th class="pb-3 text-center">Pedidos Registrados</th>
                        <th class="pb-3 text-right">Total Ventas</th>
                        <th class="pb-3 text-right">Participación (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $canales_traduccion = [
                            'presencial' => '🏢 Presencial / Tienda',
                            'web' => '💻 Sitio Web',
                            'whatsapp' => '💬 WhatsApp',
                            'redes_sociales' => '📱 Redes Sociales',
                            'referido' => '👥 Referido'
                        ];
                    @endphp
                    @forelse($canales_preferidos as $canal)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3.5 font-medium text-gray-800">
                            {{ $canales_traduccion[$canal->canal] ?? $canal->canal }}
                        </td>
                        <td class="py-3.5 text-center text-gray-700 font-semibold">{{ $canal->cantidad_pedidos }}</td>
                        <td class="py-3.5 text-right font-bold text-green-600">Bs. {{ number_format($canal->total_ventas, 2) }}</td>
                        <td class="py-3.5 text-right text-gray-500 font-mono">
                            {{ $totalVentas > 0 ? round(($canal->total_ventas / $totalVentas) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-400 italic">No hay estadísticas de canales en este período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Lógica de Inicialización e Interacción de Chart.js --}}
    <script>
        document.addEventListener('livewire:init', () => {
            let chartVentas = null;
            let chartEstados = null;

            const initCharts = () => {
                // Obtener datos inicializados desde Blade compilado por Laravel
                const ventasLabels = @js($ventas_diarias->pluck('fecha')->map(fn($f) => date('d/m', strtotime($f))));
                const ventasData = @js($ventas_diarias->pluck('total_dia'));
                const estadosLabels = @js($pedidos_por_estado->pluck('estado')->map(fn($e) => $estados_traduccion[$e] ?? $e));
                const estadosData = @js($pedidos_por_estado->pluck('cantidad'));

                // Elemento para gráfico de tendencia de ventas
                const ctxVentas = document.getElementById('chartVentasDashboard');
                if (ctxVentas) {
                    chartVentas = new Chart(ctxVentas.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: ventasLabels,
                            datasets: [{
                                label: 'Ventas (Bs.)',
                                data: ventasData,
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.25,
                                pointRadius: 4,
                                pointBackgroundColor: 'rgb(59, 130, 246)'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return ' Bs. ' + context.parsed.y.toLocaleString('es-BO', {minimumFractionDigits: 2});
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(0,0,0,0.04)' }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }

                // Elemento para gráfico de distribución por estados de pedidos
                const ctxEstados = document.getElementById('chartEstadosDashboard');
                if (ctxEstados) {
                    chartEstados = new Chart(ctxEstados.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: estadosLabels,
                            datasets: [{
                                data: estadosData,
                                backgroundColor: [
                                    'rgba(156, 163, 175, 0.85)', // cotizacion
                                    'rgba(245, 158, 11, 0.85)',  // pendiente
                                    'rgba(59, 130, 246, 0.85)',  // en_diseno
                                    'rgba(99, 102, 241, 0.85)',  // aprobado
                                    'rgba(139, 92, 246, 0.85)',  // en_produccion
                                    'rgba(20, 184, 166, 0.85)',  // listo
                                    'rgba(16, 185, 129, 0.85)',  // entregado
                                    'rgba(239, 68, 68, 0.85)'    // cancelado
                                ],
                                borderWidth: 1.5,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 12, padding: 15, font: { size: 11 } }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }
            };

            // Iniciar gráficos al cargar la página
            initCharts();

            // Escuchar eventos reactivos disparados por Livewire (actualizarFecha/actualizarPeriodo)
            window.addEventListener('updateDashboardChartsData', (event) => {
                const data = event.detail[0];
                
                if (chartVentas) {
                    chartVentas.data.labels = data.ventasLabels;
                    chartVentas.data.datasets[0].data = data.ventasData;
                    chartVentas.update();
                }

                if (chartEstados) {
                    chartEstados.data.labels = data.estadosLabels;
                    chartEstados.data.datasets[0].data = data.estadosData;
                    chartEstados.update();
                }
            });
        });
    </script>

    {{-- Animación para la fecha personalizada --}}
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.25s ease-out forwards;
        }
    </style>
</div>
