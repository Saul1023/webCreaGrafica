<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'segmento_id',
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'whatsapp',
        'nit_ci',
        'empresa',
        'canal'
    ];

    protected $casts = [
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function segmento()
    {
        return $this->belongsTo(SegmentoCliente::class, 'segmento_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    // Scopes
    public function scopePorCanal($query, $canal)
    {
        return $query->where('canal', $canal);
    }
}