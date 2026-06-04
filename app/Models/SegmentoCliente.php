<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentoCliente extends Model
{
    protected $table = 'segmentos_cliente';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'color_hex'
    ];

    protected $casts = [
        'creado_en' => 'datetime'
    ];

    // Relaciones
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'segmento_id');
    }
}