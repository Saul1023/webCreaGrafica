<div x-data="{ 
    openApertura: $wire.entangle('isOpenAperturaModal'),
    openMovimiento: $wire.entangle('isOpenMovimientoModal'),
    openCierre: $wire.entangle('isOpenCierreModal') 
}" 
x-init="
    $watch('openApertura', value => { if(!value) $wire.resetErrors() });
    $watch('openMovimiento', value => { if(!value) $wire.resetErrors() });
    $watch('openCierre', value => { if(!value) $wire.resetErrors() });
">
    {{-- Notificaciones --}}
    @if (session()->has('message'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <p class="text-sm font-semibold">{{ session('message') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-750">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif
    
    @if (session()->has('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <p class="text-sm font-semibold">{{ session('error') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-750">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    {{-- Tabs de navegación superior --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cash-register text-blue-600"></i>
                <span>Control Diario de Caja</span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">Supervisión de ingresos, egresos de caja chica y cuadres de caja diarios.</p>
        </div>
        <div class="flex bg-gray-200 p-1 rounded-xl">
            <button wire:click="selectTab('actual')" 
                class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $tab === 'actual' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                Caja Activa
            </button>
            <button wire:click="selectTab('historial')" 
                class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all {{ $tab === 'historial' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                Historial de Arqueos
            </button>
        </div>
    </div>

    {{-- VISTA: CAJA ACTIVA --}}
    @if($tab === 'actual')
        @if(!$cajaActiva)
            {{-- Caja Cerrada - Pantalla de Apertura --}}
            <div class="bg-white rounded-2xl border border-gray-150 shadow-sm p-12 text-center max-w-xl mx-auto my-12">
                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <i class="fas fa-lock text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">La caja física se encuentra CERRADA</h3>
                <p class="text-gray-500 text-sm mb-8">Debes realizar la apertura de la caja del día especificando el fondo de sencillo (efectivo inicial) para poder registrar pagos y egresos operativos.</p>
                <button wire:click="abrirModalApertura"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg hover:shadow-blue-500/20 transition-all inline-flex items-center gap-2">
                    <i class="fas fa-key"></i>
                    <span>Abrir Caja de Hoy</span>
                </button>
            </div>
        @else
            {{-- Caja Abierta - Dashboard y Métricas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                {{-- Efectivo Disponible --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-5 flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 text-slate-100/40 text-7xl translate-x-3 translate-y-3 group-hover:scale-110 transition duration-300 pointer-events-none">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="relative">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest block mb-1">Efectivo Esperado</span>
                        <span class="text-2xl font-black text-slate-900 block leading-none">Bs. {{ number_format($cajaActiva->monto_esperado_efectivo, 2) }}</span>
                        <span class="text-[10px] text-gray-400 block mt-2">Apertura: Bs. {{ number_format($cajaActiva->monto_apertura, 2) }}</span>
                    </div>
                </div>

                {{-- Ventas QR --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-5 flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 text-slate-100/40 text-7xl translate-x-3 translate-y-3 group-hover:scale-110 transition duration-300 pointer-events-none">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div class="relative">
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-1">Ingresos por QR</span>
                        <span class="text-2xl font-black text-slate-900 block leading-none">Bs. {{ number_format($cajaActiva->monto_ventas_qr, 2) }}</span>
                        <span class="text-[10px] text-gray-400 block mt-2">Pagos electrónicos</span>
                    </div>
                </div>

                {{-- Ventas Transferencia --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-5 flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 text-slate-100/40 text-7xl translate-x-3 translate-y-3 group-hover:scale-110 transition duration-300 pointer-events-none">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="relative">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest block mb-1">Ingresos Transferencia</span>
                        <span class="text-2xl font-black text-slate-900 block leading-none">Bs. {{ number_format($cajaActiva->monto_ventas_transferencia, 2) }}</span>
                        <span class="text-[10px] text-gray-400 block mt-2">Cuentas Bancarias</span>
                    </div>
                </div>

                {{-- Egresos Menores --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-5 flex items-center justify-between relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 text-slate-100/40 text-7xl translate-x-3 translate-y-3 group-hover:scale-110 transition duration-300 pointer-events-none">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="relative">
                        <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest block mb-1">Gastos / Egresos</span>
                        <span class="text-2xl font-black text-slate-900 block leading-none">Bs. {{ number_format($cajaActiva->monto_egresos_efectivo, 2) }}</span>
                        <span class="text-[10px] text-gray-400 block mt-2">Caja chica</span>
                    </div>
                </div>
            </div>

            {{-- Ficha de Estado y Botones de Acción --}}
            <div class="bg-white rounded-xl border border-gray-150 shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <div>
                        <span class="text-sm font-bold text-gray-800">Caja Abierta por: {{ $cajaActiva->usuarioApertura->nombre }}</span>
                        <span class="text-[10px] text-gray-400 block">Apertura: {{ $cajaActiva->fecha_apertura->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="abrirModalMovimiento"
                        class="bg-gray-950 hover:bg-slate-900 text-white text-xs font-black uppercase tracking-wider px-4 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
                        <i class="fas fa-plus"></i>
                        <span>Ingreso / Egreso Manual</span>
                    </button>
                    <button wire:click="abrirModalCierre"
                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider px-4 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
                        <i class="fas fa-lock"></i>
                        <span>Cerrar Caja (Arqueo)</span>
                    </button>
                </div>
            </div>

            {{-- Tabla de Movimientos del Día --}}
            <div class="bg-white rounded-xl shadow border border-gray-150 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-150 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-800">Movimientos de la Caja Actual</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-150 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Referencia</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Método de Pago</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white">
                            @forelse($transacciones as $t)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                        {{ $t->creado_en->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-800">{{ $t->concepto }}</div>
                                        @if($t->pedido)
                                            <span class="inline-flex items-center text-[10px] text-blue-600 font-bold mt-0.5">
                                                <i class="fas fa-shopping-cart mr-1"></i> Pedido: {{ $t->pedido->numero_pedido }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                        {{ $t->referencia ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-gray-650 uppercase">
                                        <span class="flex items-center gap-1.5">
                                            @if($t->metodo_pago === 'efectivo')
                                                <i class="fas fa-coins text-emerald-500"></i> Efectivo
                                            @elseif($t->metodo_pago === 'qr')
                                                <i class="fas fa-qrcode text-blue-500"></i> Pago QR
                                            @elseif($t->metodo_pago === 'transferencia')
                                                <i class="fas fa-university text-indigo-500"></i> Transferencia
                                            @else
                                                <i class="fas fa-credit-card text-gray-500"></i> Otro
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $t->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $t->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-sm {{ $t->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $t->tipo === 'ingreso' ? '+' : '-' }} Bs. {{ number_format($t->monto, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                        No se han registrado movimientos todavía. ¡Registra cobros o gastos!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- VISTA: HISTORIAL DE ARQUEOS --}}
    @if($tab === 'historial')
        @if(!$cajaAuditar_id)
            {{-- Tabla con historial general de cierres --}}
            <div class="bg-white rounded-xl shadow border border-gray-150 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-150 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Cierre</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Operadores</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Apertura</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Efectivo Esperado</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Efectivo Real</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Discrepancia</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Detalles</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white">
                            @forelse($historialCajas as $hc)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ $hc->fecha_cierre->format('d/m/Y') }}</div>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $hc->fecha_apertura->format('H:i') }} a {{ $hc->fecha_cierre->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs leading-relaxed text-gray-600">
                                        <div><strong>Apertura:</strong> {{ $hc->usuarioApertura->nombre }}</div>
                                        <div><strong>Cierre:</strong> {{ $hc->usuarioCierre->nombre ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-600">
                                        Bs. {{ number_format($hc->monto_apertura, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-600">
                                        Bs. {{ number_format($hc->monto_cierre, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800">
                                        Bs. {{ number_format($hc->monto_real_efectivo, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold">
                                        @if($hc->diferencia == 0)
                                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Cuadrada</span>
                                        @elseif($hc->diferencia < 0)
                                            <span class="text-red-600"><i class="fas fa-minus-circle mr-1"></i>Faltante: Bs. {{ number_format(abs($hc->diferencia), 2) }}</span>
                                        @else
                                            <span class="text-blue-600"><i class="fas fa-plus-circle mr-1"></i>Sobrante: Bs. {{ number_format($hc->diferencia, 2) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button wire:click="auditarCaja({{ $hc->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-xs font-black uppercase tracking-wider inline-flex items-center gap-1">
                                            <i class="fas fa-eye"></i> Auditar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">
                                        No hay arqueos cerrados registrados en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-150">
                    {{ $historialCajas->links() }}
                </div>
            </div>
        @else
            {{-- Auditoría de una Caja Cerrada Específica --}}
            <div class="mb-6 flex items-center justify-between bg-white rounded-xl border p-4">
                <div class="flex items-center gap-3">
                    <button wire:click="volverAlHistorial" class="text-gray-500 hover:text-gray-900 transition">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </button>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Auditoría de Arqueo #{{ $cajaAuditar->id }}</h3>
                        <span class="text-xs text-gray-400">Caja cerrada el {{ $cajaAuditar->fecha_cierre->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                <div>
                    @if($cajaAuditar->diferencia == 0)
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                            Caja Cuadrada
                        </span>
                    @elseif($cajaAuditar->diferencia < 0)
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold border border-red-200">
                            Faltante: Bs. {{ number_format(abs($cajaAuditar->diferencia), 2) }}
                        </span>
                    @else
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">
                            Sobrante: Bs. {{ number_format($cajaAuditar->diferencia, 2) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Métricas de la Caja Auditada --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl border p-5">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Monto Apertura</span>
                    <span class="text-xl font-black text-slate-800">Bs. {{ number_format($cajaAuditar->monto_apertura, 2) }}</span>
                </div>
                <div class="bg-white rounded-xl border p-5">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Efectivo Esperado</span>
                    <span class="text-xl font-black text-slate-800">Bs. {{ number_format($cajaAuditar->monto_cierre, 2) }}</span>
                </div>
                <div class="bg-white rounded-xl border p-5">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Efectivo Contado</span>
                    <span class="text-xl font-black text-slate-800">Bs. {{ number_format($cajaAuditar->monto_real_efectivo, 2) }}</span>
                </div>
                <div class="bg-white rounded-xl border p-5">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Fecha Apertura</span>
                    <span class="text-sm font-semibold text-gray-700 block leading-tight">{{ $cajaAuditar->fecha_apertura->format('d/m/Y') }}</span>
                    <span class="text-[10px] text-gray-400 font-mono">{{ $cajaAuditar->fecha_apertura->format('H:i') }}</span>
                </div>
            </div>

            @if($cajaAuditar->observaciones)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                    <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1"><i class="fas fa-comment-alt mr-1"></i> Observaciones de Cierre</h4>
                    <p class="text-sm text-amber-900 italic font-medium">"{{ $cajaAuditar->observaciones }}"</p>
                </div>
            @endif

            {{-- Movimientos de la Caja Auditada --}}
            <div class="bg-white rounded-xl shadow border overflow-hidden">
                <div class="px-5 py-4 border-b bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-800">Movimientos de la Caja Auditada</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Referencia</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Método de Pago</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y bg-white">
                            @forelse($transacciones as $t)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 text-xs font-mono text-gray-400">
                                        {{ $t->creado_en->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-800">{{ $t->concepto }}</div>
                                        @if($t->pedido)
                                            <span class="inline-flex items-center text-[10px] text-blue-600 font-bold mt-0.5">
                                                <i class="fas fa-shopping-cart mr-1"></i> Pedido: {{ $t->pedido->numero_pedido }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                        {{ $t->referencia ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-gray-650 uppercase">
                                        <span class="flex items-center gap-1.5">
                                            @if($t->metodo_pago === 'efectivo')
                                                <i class="fas fa-coins text-emerald-500"></i> Efectivo
                                            @elseif($t->metodo_pago === 'qr')
                                                <i class="fas fa-qrcode text-blue-500"></i> QR
                                            @elseif($t->metodo_pago === 'transferencia')
                                                <i class="fas fa-university text-indigo-500"></i> Transferencia
                                            @else
                                                <i class="fas fa-credit-card text-gray-500"></i> Otro
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $t->tipo === 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $t->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-sm {{ $t->tipo === 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $t->tipo === 'ingreso' ? '+' : '-' }} Bs. {{ number_format($t->monto, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                        No hay transacciones registradas en este arqueo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- MODAL: APERTURA DE CAJA --}}
    <div x-show="openApertura" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openApertura = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white text-xl">
                                <i class="fas fa-lock-open"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">Apertura de Caja</h3>
                                <p class="text-xs text-blue-100">Ingrese el saldo inicial de la caja física</p>
                            </div>
                        </div>
                        <button type="button" @click="openApertura = false" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 text-left">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fondo de Sencillo Inicial (Bs.) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold">Bs.</span>
                            <input type="number" step="0.01" wire:model="monto_apertura" placeholder="Ej. 200"
                                class="w-full pl-10 border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-semibold">
                        </div>
                        @error('monto_apertura') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2 border-t">
                    <button type="button" @click="openApertura = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="abrirCaja" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <i class="fas fa-key" wire:loading.remove wire:target="abrirCaja"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="abrirCaja"></i>
                        <span>Abrir Caja</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: INGRESO / EGRESO MANUAL --}}
    <div x-show="openMovimiento" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openMovimiento = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-slate-900 px-6 py-4 rounded-t-xl text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Ingreso / Egreso Manual</h3>
                                <p class="text-xs text-slate-300">Movimientos de caja chica / otros cobros</p>
                            </div>
                        </div>
                        <button type="button" @click="openMovimiento = false" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 text-left space-y-4">
                    {{-- Tipo de Movimiento --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipo de Operación</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-gray-100 rounded-lg">
                            <label class="flex justify-center items-center py-2.5 rounded-md text-xs font-black uppercase cursor-pointer select-none transition {{ $tipo_movimiento === 'egreso' ? 'bg-red-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200' }}">
                                <input type="radio" wire:model.live="tipo_movimiento" value="egreso" class="sr-only">
                                <i class="fas fa-minus-circle mr-1"></i> Gasto / Egreso
                            </label>
                            <label class="flex justify-center items-center py-2.5 rounded-md text-xs font-black uppercase cursor-pointer select-none transition {{ $tipo_movimiento === 'ingreso' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-200' }}">
                                <input type="radio" wire:model.live="tipo_movimiento" value="ingreso" class="sr-only">
                                <i class="fas fa-plus-circle mr-1"></i> Otro Ingreso
                            </label>
                        </div>
                    </div>

                    {{-- Concepto --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Concepto o Descripción <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="concepto" placeholder="Ej. Compra de cintas, Abono directo cliente, etc."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('concepto') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Monto --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Monto (Bs.) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" wire:model="monto_movimiento" placeholder="0.00"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-semibold">
                            @error('monto_movimiento') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        {{-- Método de Pago --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Método de Pago <span class="text-red-500">*</span></label>
                            <select wire:model="metodo_pago_movimiento"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-semibold">
                                <option value="efectivo">Efectivo</option>
                                <option value="qr">Pago QR</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="otro">Otro</option>
                            </select>
                            @error('metodo_pago_movimiento') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Referencia --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Referencia / Código de Operación</label>
                        <input type="text" wire:model="referencia_movimiento" placeholder="Ej. ID trans, nro. transferencia (opcional)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        @error('referencia_movimiento') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2 border-t">
                    <button type="button" @click="openMovimiento = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="registrarMovimiento" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <i class="fas fa-save" wire:loading.remove wire:target="registrarMovimiento"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="registrarMovimiento"></i>
                        <span>Guardar Movimiento</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: CIERRE DE CAJA --}}
    <div x-show="openCierre" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openCierre = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-rose-600 px-6 py-4 rounded-t-xl text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 text-left">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-xl">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">Cierre de Caja (Arqueo)</h3>
                                <p class="text-xs text-rose-100">Cierre diario y control de efectivo</p>
                            </div>
                        </div>
                        <button type="button" @click="openCierre = false" class="text-white/80 hover:text-white transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 text-left space-y-4">
                    @if($cajaActiva)
                        {{-- Resumen Informativo --}}
                        <div class="bg-gray-50 p-4 border rounded-lg text-sm space-y-2.5">
                            <div class="flex justify-between text-gray-500">
                                <span>Fondo de Apertura:</span>
                                <span class="font-bold font-mono">Bs. {{ number_format($cajaActiva->monto_apertura, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span>Ingresos Totales (Efectivo):</span>
                                <span class="font-bold font-mono">+Bs. {{ number_format($cajaActiva->transacciones()->where('tipo', 'ingreso')->where('metodo_pago', 'efectivo')->sum('monto'), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                <span>Egresos Totales (Efectivo):</span>
                                <span class="font-bold font-mono">-Bs. {{ number_format($cajaActiva->transacciones()->where('tipo', 'egreso')->where('metodo_pago', 'efectivo')->sum('monto'), 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-t pt-2 font-bold text-gray-800 text-base">
                                <span>Efectivo Esperado:</span>
                                <span class="font-mono text-lg text-blue-600">Bs. {{ number_format($cajaActiva->monto_esperado_efectivo, 2) }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- Dinero Real Contado --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Efectivo Físico Contado en Caja (Bs.) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" wire:model.live="monto_real_efectivo" placeholder="0.00"
                            class="w-full border border-gray-300 rounded-lg p-2.5 text-base focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-black font-mono text-center">
                        @error('monto_real_efectivo') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Diferencia en vivo --}}
                    @if($cajaActiva && is_numeric($monto_real_efectivo))
                        @php
                            $diff = $monto_real_efectivo - $cajaActiva->monto_esperado_efectivo;
                        @endphp
                        <div class="text-center py-2 rounded-lg text-xs font-bold border {{ $diff == 0 ? 'bg-green-50 text-green-800 border-green-200' : ($diff < 0 ? 'bg-red-50 text-red-800 border-red-200' : 'bg-blue-50 text-blue-800 border-blue-200') }}">
                            @if($diff == 0)
                                <i class="fas fa-check-circle mr-1"></i> Caja Cuadrada
                            @elseif($diff < 0)
                                <i class="fas fa-exclamation-triangle mr-1"></i> Diferencia Faltante: -Bs. {{ number_format(abs($diff), 2) }}
                            @else
                                <i class="fas fa-info-circle mr-1"></i> Diferencia Sobrante: +Bs. {{ number_format($diff, 2) }}
                            @endif
                        </div>
                    @endif

                    {{-- Observaciones --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Observaciones del Cierre</label>
                        <textarea wire:model="observaciones_cierre" rows="3" placeholder="Si hay diferencia, explique la causa..."
                            class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
                        @error('observaciones_cierre') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 rounded-b-xl flex justify-end gap-2 border-t">
                    <button type="button" @click="openCierre = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="button" wire:click="cerrarCaja" wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-medium text-white bg-rose-600 rounded-lg hover:bg-rose-700 transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <i class="fas fa-lock" wire:loading.remove wire:target="cerrarCaja"></i>
                        <i class="fas fa-spinner fa-spin" wire:loading wire:target="cerrarCaja"></i>
                        <span>Cerrar Caja</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
