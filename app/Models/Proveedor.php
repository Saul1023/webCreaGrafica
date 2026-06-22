<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'contacto_nombre',
        'telefono',
        'correo',
        'direccion',
        'nit',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
