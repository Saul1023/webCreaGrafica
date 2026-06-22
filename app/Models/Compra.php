<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'proveedor_id',
        'usuario_id',
        'numero_factura',
        'total',
        'creado_en',
        'actualizado_en'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }
}
