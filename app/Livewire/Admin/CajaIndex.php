<?php

namespace App\Livewire\Admin;

use App\Models\Caja;
use App\Models\TransaccionCaja;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CajaIndex extends Component
{
    use WithPagination;

    // Apertura Form
    public $monto_apertura;

    // Manual Transaction Form
    public $tipo_movimiento = 'egreso'; // 'ingreso' or 'egreso'
    public $concepto;
    public $monto_movimiento;
    public $metodo_pago_movimiento = 'efectivo';
    public $referencia_movimiento;

    // Cierre Form
    public $monto_real_efectivo;
    public $observaciones_cierre;

    // Navigation and Auditing
    public $tab = 'actual'; // 'actual' or 'historial'
    public $caja_auditar_id = null; // ID de caja histórica para auditoría de movimientos

    public $isOpenMovimientoModal = false;
    public $isOpenCierreModal = false;
    public $isOpenAperturaModal = false;

    protected $queryString = [
        'tab' => ['except' => 'actual'],
    ];

    public function render()
    {
        // 1. Obtener caja abierta activa
        $cajaActiva = Caja::with(['usuarioApertura'])->abierta()->first();

        // 2. Transacciones de la caja que se está visualizando
        $cajaVisualizadaId = $this->tab === 'actual' 
            ? ($cajaActiva ? $cajaActiva->id : null) 
            : $this->caja_auditar_id;

        $transacciones = collect();
        if ($cajaVisualizadaId) {
            $transacciones = TransaccionCaja::with(['usuario', 'pedido'])
                ->where('caja_id', $cajaVisualizadaId)
                ->orderBy('id', 'desc')
                ->get();
        }

        // 3. Cajas históricas cerradas
        $historialCajas = Caja::with(['usuarioApertura', 'usuarioCierre'])
            ->where('estado', 'cerrada')
            ->orderBy('fecha_cierre', 'desc')
            ->paginate(10, ['*'], 'cajasPage');

        // 4. Caja seleccionada para auditar
        $cajaAuditar = $this->caja_auditar_id ? Caja::with(['usuarioApertura', 'usuarioCierre'])->find($this->caja_auditar_id) : null;

        return view('livewire.admin.caja-index', [
            'cajaActiva' => $cajaActiva,
            'transacciones' => $transacciones,
            'historialCajas' => $historialCajas,
            'cajaAuditar' => $cajaAuditar
        ])->layout('layouts.admin');
    }

    public function selectTab($selectedTab)
    {
        $this->tab = $selectedTab;
        if ($selectedTab === 'actual') {
            $this->caja_auditar_id = null;
        }
        $this->resetPage();
    }

    public function auditarCaja($cajaId)
    {
        $this->caja_auditar_id = $cajaId;
        $this->tab = 'historial';
    }

    public function volverAlHistorial()
    {
        $this->caja_auditar_id = null;
    }

    // --- ACCIONES DE CAJA ---

    public function abrirModalApertura()
    {
        $this->monto_apertura = '';
        $this->resetErrorBag();
        $this->isOpenAperturaModal = true;
    }

    public function abrirCaja()
    {
        $this->validate([
            'monto_apertura' => 'required|numeric|min:0'
        ], [
            'monto_apertura.required' => 'El monto de apertura es obligatorio.',
            'monto_apertura.numeric' => 'Debe ingresar un valor numérico.',
            'monto_apertura.min' => 'El monto mínimo de apertura es Bs. 0.'
        ]);

        // Verificar si ya existe una abierta
        if (Caja::abierta()->exists()) {
            session()->flash('error', 'Ya existe una caja abierta en el sistema.');
            $this->isOpenAperturaModal = false;
            return;
        }

        DB::transaction(function() {
            $caja = Caja::create([
                'usuario_apertura_id' => auth()->id(),
                'fecha_apertura' => now(),
                'monto_apertura' => $this->monto_apertura,
                'estado' => 'abierta',
                'creado_en' => now(),
                'actualizado_en' => now()
            ]);

            // Crear una transacción inicial de apertura en efectivo
            TransaccionCaja::create([
                'caja_id' => $caja->id,
                'usuario_id' => auth()->id(),
                'tipo' => 'ingreso',
                'concepto' => 'Monto inicial de apertura de caja',
                'monto' => $this->monto_apertura,
                'metodo_pago' => 'efectivo',
                'creado_en' => now(),
                'actualizado_en' => now()
            ]);
        });

        session()->flash('message', 'Caja abierta correctamente.');
        $this->isOpenAperturaModal = false;
        $this->monto_apertura = '';
    }

    public function abrirModalMovimiento()
    {
        $this->tipo_movimiento = 'egreso';
        $this->concepto = '';
        $this->monto_movimiento = '';
        $this->metodo_pago_movimiento = 'efectivo';
        $this->referencia_movimiento = '';
        $this->resetErrorBag();
        $this->isOpenMovimientoModal = true;
    }

    public function registrarMovimiento()
    {
        $caja = Caja::abierta()->first();
        if (!$caja) {
            session()->flash('error', 'No hay ninguna caja abierta activa.');
            $this->isOpenMovimientoModal = false;
            return;
        }

        $this->validate([
            'concepto' => 'required|min:3|max:255',
            'monto_movimiento' => 'required|numeric|min:0.01',
            'tipo_movimiento' => 'required|in:ingreso,egreso',
            'metodo_pago_movimiento' => 'required|in:efectivo,qr,transferencia,otro',
            'referencia_movimiento' => 'nullable|max:100'
        ], [
            'concepto.required' => 'El concepto o descripción es obligatorio.',
            'concepto.min' => 'El concepto debe tener al menos 3 caracteres.',
            'monto_movimiento.required' => 'El monto es obligatorio.',
            'monto_movimiento.numeric' => 'Debe ingresar un monto numérico.',
            'monto_movimiento.min' => 'El monto debe ser mayor a Bs. 0.00.'
        ]);

        // Si es un egreso en efectivo, verificar que haya suficiente dinero en la caja
        if ($this->tipo_movimiento === 'egreso' && $this->metodo_pago_movimiento === 'efectivo') {
            if ($this->monto_movimiento > $caja->monto_esperado_efectivo) {
                $this->addError('monto_movimiento', 'No hay suficiente efectivo disponible en caja (Efectivo disponible: Bs. ' . number_format($caja->monto_esperado_efectivo, 2) . ').');
                return;
            }
        }

        TransaccionCaja::create([
            'caja_id' => $caja->id,
            'usuario_id' => auth()->id(),
            'tipo' => $this->tipo_movimiento,
            'concepto' => trim($this->concepto),
            'monto' => $this->monto_movimiento,
            'metodo_pago' => $this->metodo_pago_movimiento,
            'referencia' => $this->referencia_movimiento ? trim($this->referencia_movimiento) : null,
            'creado_en' => now(),
            'actualizado_en' => now()
        ]);

        session()->flash('message', 'Movimiento de caja registrado correctamente.');
        $this->isOpenMovimientoModal = false;
    }

    public function abrirModalCierre()
    {
        $caja = Caja::abierta()->first();
        if (!$caja) {
            session()->flash('error', 'No hay ninguna caja abierta activa.');
            return;
        }

        // Sugerir el monto esperado de efectivo
        $this->monto_real_efectivo = $caja->monto_esperado_efectivo;
        $this->observaciones_cierre = '';
        $this->resetErrorBag();
        $this->isOpenCierreModal = true;
    }

    public function cerrarCaja()
    {
        $caja = Caja::abierta()->first();
        if (!$caja) {
            session()->flash('error', 'No hay ninguna caja abierta activa.');
            $this->isOpenCierreModal = false;
            return;
        }

        $this->validate([
            'monto_real_efectivo' => 'required|numeric|min:0',
            'observaciones_cierre' => 'nullable|max:500'
        ], [
            'monto_real_efectivo.required' => 'Debe ingresar el arqueo de efectivo físico contado.',
            'monto_real_efectivo.numeric' => 'Debe ingresar un valor numérico.',
            'monto_real_efectivo.min' => 'El arqueo físico no puede ser negativo.'
        ]);

        $montoEsperado = $caja->monto_esperado_efectivo;
        $diferencia = $this->monto_real_efectivo - $montoEsperado;

        $caja->update([
            'usuario_cierre_id' => auth()->id(),
            'fecha_cierre' => now(),
            'monto_cierre' => $montoEsperado,
            'monto_real_efectivo' => $this->monto_real_efectivo,
            'diferencia' => $diferencia,
            'estado' => 'cerrada',
            'observaciones' => $this->observaciones_cierre ? trim($this->observaciones_cierre) : null,
            'actualizado_en' => now()
        ]);

        session()->flash('message', 'Caja cerrada correctamente con arqueo registrado.');
        $this->isOpenCierreModal = false;
        
        // Redireccionar al historial para auditar el cierre recién hecho
        $this->auditarCaja($caja->id);
    }

    public function resetErrors()
    {
        $this->resetErrorBag();
    }
}
