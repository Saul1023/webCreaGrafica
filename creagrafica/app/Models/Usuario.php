<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'nombre_usuario',
        'password',
        'nombre_completo',
        'email',
        'telefono',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'fecha_creacion' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'ultimo_acceso' => 'datetime',
    ];

    public function save(array $options = [])
    {
        if (!$this->exists) {
            $this->fecha_creacion = $this->fecha_creacion ?: now();
        }

        $this->fecha_actualizacion = now();
        return parent::save($options);
    }

    public function updateUltimoAcceso()
    {
        $this->ultimo_acceso = now();
        $this->fecha_actualizacion = now();
        return $this->save();
    }

    // Relación con Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    // Método para verificar si es administrador
    public function isAdmin()
    {
        return $this->id_rol == 1; // Suponiendo que 1 es admin
    }

    // Método para verificar si tiene un rol específico por ID
    public function hasRole($roleId)
    {
        return $this->id_rol == $roleId;
    }

    // Método para verificar si tiene un rol por nombre
    public function hasRoleByName($roleName)
    {
        if (!$this->relationLoaded('rol')) {
            $this->load('rol');
        }

        return $this->rol && strtolower($this->rol->nombre) === strtolower($roleName);
    }

    // Obtener nombre para mostrar
    public function getNameAttribute()
    {
        return $this->nombre_completo;
    }

    // Para compatibilidad con Laravel Auth
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function getAuthIdentifier()
    {
        return $this->id_usuario;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // Método para obtener el rol del usuario
    public function getRoleName()
    {
        if (!$this->relationLoaded('rol')) {
            $this->load('rol');
        }

        return $this->rol ? $this->rol->nombre : 'Sin rol';
    }
}
