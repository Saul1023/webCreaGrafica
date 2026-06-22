<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalles_compra';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'costo_unitario',
        'subtotal',
        'creado_en',
        'actualizado_en'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
