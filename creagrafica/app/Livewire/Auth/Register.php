<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth; // <--- AQUÍ ESTABA EL ERROR
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $nombre_completo = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $telefono = '';

    protected $rules = [
        'nombre_completo' => 'required|min:3|max:100',
        'email' => 'required|email|unique:usuarios,email',
        'password' => 'required|min:6|confirmed',
        'telefono' => 'nullable|string|max:20',
    ];

    protected $messages = [
        'nombre_completo.required' => 'El nombre completo es obligatorio.',
        'nombre_completo.min' => 'El nombre debe tener al menos 3 caracteres.',
        'nombre_completo.max' => 'El nombre no debe exceder los 100 caracteres.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
    ];

    public function mount()
    {
        // Si ya está autenticado, redirigir al home
        if (Auth::check()) {
            return redirect()->route('home');
        }
    }

    public function register()
    {
        $this->validate();

        // Crear usuario en la tabla usuarios
        $usuario = Usuario::create([
            'id_rol' => 2, // Rol de usuario normal (2), admin sería 1
            'nombre_usuario' => strtolower(str_replace(' ', '', $this->nombre_completo)),
            'nombre_completo' => $this->nombre_completo,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'telefono' => $this->telefono,
            'estado' => 'activo',
            'fecha_creacion' => now(),
            'fecha_actualizacion' => now(),
        ]);

        // Autenticar al usuario
        Auth::login($usuario);

        // Actualizar último acceso
        // Asegúrate de que este método exista en tu modelo Usuario, si no, usa $usuario->save() directo
        if(method_exists($usuario, 'updateUltimoAcceso')) {
             $usuario->updateUltimoAcceso();
        }

        session()->flash('success', '¡Registro exitoso! Bienvenido a CREAGRAFICA.');

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.app', ['title' => 'Registrarse']);
    }
}
