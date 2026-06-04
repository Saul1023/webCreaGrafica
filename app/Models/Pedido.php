<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'cliente_id',
        'usuario_id',
        'numero_pedido',
        'estado',
        'total',
        'monto_pagado',
        'fecha_entrega'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_entrega' => 'date',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    // Accessors
    public function getEstaPagadoAttribute()
    {
        return $this->saldo_pendiente <= 0;
    }

    public function getPorcentajePagadoAttribute()
    {
        if ($this->total == 0) return 0;
        return round(($this->monto_pagado / $this->total) * 100, 2);
    }

    // Scopes
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePendientes($query)
    {
        return $query->whereNotIn('estado', ['entregado', 'cancelado']);
    }

    // Methods
    public function actualizarTotal()
    {
        $this->total = $this->detalles()->sum('subtotal');
        $this->save();
    }

    public function registrarPago($monto)
    {
        $this->monto_pagado += $monto;
        $this->save();
    }

    public function cambiarEstado($nuevoEstado)
    {
        $this->estado = $nuevoEstado;
        $this->save();
    }
}
