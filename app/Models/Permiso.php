<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'modulo',
        'descripcion'
    ];

    protected $casts = [
        'creado_en' => 'datetime'
    ];
}