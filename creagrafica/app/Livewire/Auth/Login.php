<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Livewire\Component;

class Login extends Component
{
    public $nombre_usuario = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        // CORRECCIÓN: Cambié 'nombre_usuario' por 'string'
        'nombre_usuario' => 'required|string',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
        // CORRECCIÓN: Actualicé la clave del mensaje error
        'nombre_usuario.string' => 'El nombre de usuario debe ser un texto válido.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
    ];

    public function mount()
    {
        // Si ya está autenticado, redirigir según rol
        if (Auth::check()) {
            $this->redirectBasedOnRole();
        }
    }

    public function login()
    {
        $this->validate();

        $credentials = [
            'nombre_usuario' => $this->nombre_usuario,
            'password' => $this->password,
        ];

        // Intento de login
        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            // Actualizar último acceso
            $usuario = Auth::user();

            // Verificación extra por si el método no existe en el modelo
            if (method_exists($usuario, 'updateUltimoAcceso')) {
                $usuario->updateUltimoAcceso();
            } else {
                $usuario->ultimo_acceso = now();
                $usuario->save();
            }

            // Redirigir según el rol
            return $this->redirectBasedOnRole();
        }

        // Si falla, mostrar error
        $this->addError('nombre_usuario', 'Las credenciales no coinciden con nuestros registros.');
    }

    private function redirectBasedOnRole()
    {
        $usuario = Auth::user();

        if ($usuario->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.login')
             ->layout('components.layouts.app', ['title' => 'Iniciar Sesión']);
    }
}
