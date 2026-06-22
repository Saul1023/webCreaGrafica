<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'nombre_usuario',
        'correo',
        'clave',
        'telefono',
        'avatar_ruta',
        'activo',
        'ultimo_login',
        'creado_en',
        'actualizado_en'
    ];

    protected $hidden = [
        'clave'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultimo_login' => 'datetime',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Mutators
    public function setClaveAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['clave'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'usuario_id');
    }

    public function cajasAbiertas()
    {
        return $this->hasMany(Caja::class, 'usuario_apertura_id');
    }

    public function cajasCerradas()
    {
        return $this->hasMany(Caja::class, 'usuario_cierre_id');
    }

    public function transaccionesCaja()
    {
        return $this->hasMany(TransaccionCaja::class, 'usuario_id');
    }

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    // Methods
    public function verificarPassword($password)
    {
        return Hash::check($password, $this->clave);
    }

    // ========== MÉTODOS NECESARIOS PARA AUTH ==========

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->clave;
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'clave';
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // Método para verificar si es administrador
    public function isAdmin()
    {
        return $this->rol_id == 1;
    }

    // Métodos para verificar roles (CheckRole middleware)
    public function hasRole($roleId)
    {
        return $this->rol_id == $roleId;
    }

    public function hasRoleByName($roleName)
    {
        return $this->rol && $this->rol->slug === $roleName;
    }

    // Método para actualizar último login
    public function updateUltimoAcceso()
    {
        $this->ultimo_login = now();
        $this->save();
    }
}
