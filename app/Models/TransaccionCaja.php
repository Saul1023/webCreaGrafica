<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaccionCaja extends Model
{
    protected $table = 'transacciones_caja';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'pedido_id',
        'tipo',
        'concepto',
        'monto',
        'metodo_pago',
        'referencia'
    ];

    protected $casts = [
        'caja_id' => 'integer',
        'usuario_id' => 'integer',
        'pedido_id' => 'integer',
        'monto' => 'decimal:2',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
