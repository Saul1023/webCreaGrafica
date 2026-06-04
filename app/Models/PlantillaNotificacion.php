<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaNotificacion extends Model
{
    protected $table = 'plantillas_notificacion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'evento_disparo',
        'asunto',
        'cuerpo',
        'variables',
        'activo'
    ];

    protected $casts = [
        'variables' => 'array',
        'activo' => 'boolean'
    ];

    // Methods
    public function renderizar($datos)
    {
        $cuerpo = $this->cuerpo;
        foreach ($datos as $key => $value) {
            $cuerpo = str_replace("{{$key}}", $value, $cuerpo);
        }
        return $cuerpo;
    }
}