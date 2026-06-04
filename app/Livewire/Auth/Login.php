<?php
// app/Livewire/Auth/Login.php
namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Livewire\Component;

class Login extends Component
{
    public $login_input = '';  // ← Cambiado de 'nombre_usuario' a 'login_input'
    public $clave = '';
    public $remember = false;

    protected $rules = [
        'login_input' => 'required|string',  // ← Cambiado
        'clave' => 'required|min:6',
    ];

    protected $messages = [
        'login_input.required' => 'Debes ingresar tu correo o nombre de usuario.',
        'login_input.string' => 'Ingresa un texto válido.',
        'clave.required' => 'La contraseña es obligatoria.',
        'clave.min' => 'La contraseña debe tener al menos 6 caracteres.',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
    }

    public function login()
    {
        $this->validate();

        // Determinar si es email o nombre de usuario
        $field = filter_var($this->login_input, FILTER_VALIDATE_EMAIL) ? 'correo' : 'nombre_usuario';

        $credentials = [
            $field => $this->login_input,
            'password' => $this->clave,  // ← Debe ser 'password'
            'activo' => true
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            $usuario = Auth::user();
            $usuario->ultimo_login = now();
            $usuario->save();

            return $this->redirectBasedOnRole();
        }

        $this->addError('login_input', 'Las credenciales no coinciden con nuestros registros.');
    }

    private function redirectBasedOnRole()
    {
        $usuario = Auth::user();

        if ($usuario->rol_id == 1) {  // ← Verificar rol_id directamente
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.app', ['title' => 'Iniciar Sesión']);
    }
}