<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model
{
    protected $table = 'imagenes_producto';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'ruta',
        'texto_alt',
        'orden',
        'es_principal'
    ];

    protected $casts = [
        'orden' => 'integer',
        'es_principal' => 'boolean',
        'creado_en' => 'datetime'
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Events
    protected static function booted()
    {
        static::saving(function ($imagen) {
            if ($imagen->es_principal) {
                static::where('producto_id', $imagen->producto_id)
                    ->where('id', '!=', $imagen->id)
                    ->update(['es_principal' => false]);
            }
        });
    }
}