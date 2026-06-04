<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
     protected $table = 'detalles_pedido';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'personalizacion'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relaciones
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Events
    protected static function booted()
    {
        static::saved(function ($detalle) {
            $detalle->pedido->actualizarTotal();
        });

        static::deleted(function ($detalle) {
            $detalle->pedido->actualizarTotal();
        });
    }
}