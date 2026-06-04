<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo3dProducto extends Model
{
     protected $table = 'modelos_3d_producto';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'ruta_modelo',
        'ruta_miniatura',
        'color_base_hex',
        'zona_impresion_json',
        'es_activo'
    ];

    protected $casts = [
        'zona_impresion_json' => 'array',
        'es_activo' => 'boolean',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}