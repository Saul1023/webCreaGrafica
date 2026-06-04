<?php

namespace App\Livewire\Admin;

use App\Models\Usuario; // Asegúrate de que apunte a tu modelo de usuarios o User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PerfilIndex extends Component
{
    // Propiedades para Datos Personales
    public $nombre;
    public $apellido;
    public $nombre_usuario;
    public $correo;

    // Propiedades para Cambio de Contraseña
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        // Cargamos los datos del usuario logueado al iniciar
        $usuario = Auth::user();
        $this->nombre = $usuario->nombre;
        $this->apellido = $usuario->apellido;
        $this->nombre_usuario = $usuario->nombre_usuario;
        $this->correo = $usuario->correo;
    }

    public function updateProfile()
    {
        $usuarioId = Auth::id();

        // Validaciones para datos personales
        $this->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'nombre_usuario' => 'required|string|max:50|unique:usuarios,nombre_usuario,' . $usuarioId,
            'correo' => 'required|email|max:150|unique:usuarios,correo,' . $usuarioId,
        ], [
            'nombre_usuario.unique' => 'Este nombre de usuario ya está en uso.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'correo.email' => 'Por favor, ingresa un correo válido.'
        ]);

        $usuario = Auth::user();
        $usuario->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_usuario' => $this->nombre_usuario,
            'correo' => $this->correo,
            'actualizado_en' => now()
        ]);

        session()->flash('message', 'Datos personales actualizados correctamente.');
    }

    public function updatePassword()
    {
        // Validaciones para el cambio de contraseña
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed', // confirmed exige que exista password_confirmation
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.'
        ]);

        $usuario = Auth::user();

        // Validar que la contraseña actual sea correcta
        if (!Hash::check($this->current_password, $usuario->password)) {
            $this->addError('current_password', 'La contraseña actual no es correcta.');
            return;
        }

        // Actualizar contraseña encriptada
        $usuario->update([
            'password' => Hash::make($this->password),
            'actualizado_en' => now()
        ]);

        // Limpiar campos del formulario de contraseña
        $this->reset(['current_password', 'password', 'password_confirmation']);

        session()->flash('password_message', 'Contraseña cambiada con éxito.');
    }

    public function render()
    {
        return view('livewire.admin.perfil-index')->layout('layouts.admin');
    }
}
