<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
 use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fecha_creacion'
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    // Relación: un rol tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }

    // Método para crear roles por defecto
    public static function createDefaultRoles()
    {
        $roles = [
            [
                'id_rol' => 1,
                'nombre' => 'Administrador',
                'descripcion' => 'Administrador del sistema con todos los permisos',
                'estado' => 'activo',
                'fecha_creacion' => now(),
            ],
            [
                'id_rol' => 2,
                'nombre' => 'Usuario',
                'descripcion' => 'Usuario normal del sistema',
                'estado' => 'activo',
                'fecha_creacion' => now(),
            ],
        ];

        foreach ($roles as $role) {
            self::updateOrCreate(
                ['id_rol' => $role['id_rol']],
                $role
            );
        }
    }

}
