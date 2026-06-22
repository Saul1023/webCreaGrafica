<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'usuario_apertura_id',
        'usuario_cierre_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'monto_cierre',
        'monto_real_efectivo',
        'diferencia',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'usuario_apertura_id' => 'integer',
        'usuario_cierre_id' => 'integer',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
        'monto_real_efectivo' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function usuarioApertura()
    {
        return $this->belongsTo(Usuario::class, 'usuario_apertura_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(Usuario::class, 'usuario_cierre_id');
    }

    public function transacciones()
    {
        return $this->hasMany(TransaccionCaja::class, 'caja_id');
    }

    // Scopes
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    // Accessors para totales
    public function getMontoEsperadoEfectivoAttribute()
    {
        $ingresosEfectivo = $this->transacciones()
            ->where('tipo', 'ingreso')
            ->where('metodo_pago', 'efectivo')
            ->where('concepto', '!=', 'Monto inicial de apertura de caja')
            ->sum('monto');

        $egresosEfectivo = $this->transacciones()
            ->where('tipo', 'egreso')
            ->where('metodo_pago', 'efectivo')
            ->sum('monto');

        return $this->monto_apertura + $ingresosEfectivo - $egresosEfectivo;
    }

    public function getMontoVentasQRAttribute()
    {
        return $this->transacciones()->where('tipo', 'ingreso')->where('metodo_pago', 'qr')->sum('monto');
    }

    public function getMontoVentasTransferenciaAttribute()
    {
        return $this->transacciones()->where('tipo', 'ingreso')->where('metodo_pago', 'transferencia')->sum('monto');
    }

    public function getMontoOtrosIngresosEfectivoAttribute()
    {
        return $this->transacciones()->where('tipo', 'ingreso')->where('metodo_pago', 'efectivo')->whereNull('pedido_id')->sum('monto');
    }

    public function getMontoEgresosEfectivoAttribute()
    {
        return $this->transacciones()->where('tipo', 'egreso')->where('metodo_pago', 'efectivo')->sum('monto');
    }
}
