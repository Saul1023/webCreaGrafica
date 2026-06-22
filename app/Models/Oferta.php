<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    protected $table = 'ofertas';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'nombre',
        'descuento',
        'fecha_inicio',
        'fecha_fin',
        'activo'
    ];

    protected $casts = [
        'descuento' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    /**
     * Relación de muchos a muchos con Productos.
     */
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'oferta_producto', 'oferta_id', 'producto_id');
    }

    /**
     * Scope para obtener sólo ofertas activas y en vigencia.
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }
}
