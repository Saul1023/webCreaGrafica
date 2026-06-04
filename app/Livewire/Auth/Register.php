<?php
// app/Livewire/Auth/Register.php
namespace App\Livewire\Auth;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public $nombre = '';           // ← Cambiado
    public $apellido = '';         // ← Nuevo campo
    public $nombre_usuario = '';   // ← Nuevo campo
    public $correo = '';           // ← Cambiado de 'email' a 'correo'
    public $password = '';
    public $password_confirmation = '';
    public $telefono = '';

    protected $rules = [
        'nombre' => 'required|min:3|max:120',
        'apellido' => 'nullable|max:80',
        'nombre_usuario' => 'nullable|min:3|max:60|unique:usuarios,nombre_usuario|regex:/^[a-zA-Z0-9_]+$/',
        'correo' => 'required|email|unique:usuarios,correo',  // ← Cambiado
        'password' => 'required|min:6|confirmed',
        'telefono' => 'nullable|max:20',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'apellido.max' => 'El apellido no debe exceder los 80 caracteres.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está ocupado.',
        'nombre_usuario.regex' => 'Solo letras, números y guión bajo.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'Ingresa un correo electrónico válido.',
        'correo.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
    }

    public function register()
    {
        $this->validate();

        // Generar nombre_usuario automáticamente si está vacío
        if (empty($this->nombre_usuario)) {
            $base = strtolower(preg_replace('/[^a-z0-9]/', '', $this->nombre));
            $this->nombre_usuario = $base;

            $counter = 1;
            while (Usuario::where('nombre_usuario', $this->nombre_usuario)->exists()) {
                $this->nombre_usuario = $base . $counter;
                $counter++;
            }
        }

        // Crear usuario con la estructura correcta
        $usuario = Usuario::create([
            'rol_id' => 2,                              // Rol cliente
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_usuario' => $this->nombre_usuario,
            'correo' => $this->correo,                  // ← Usar 'correo'
            'clave' => $this->password,                 // ← Usar 'clave'
            'telefono' => $this->telefono,
            'activo' => true,
            'creado_en' => now(),
            'actualizado_en' => now(),
        ]);

        Auth::login($usuario);

        $usuario->ultimo_login = now();
        $usuario->save();

        session()->flash('success', '¡Registro exitoso! Bienvenido a CREAGRAFICA.');

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app', ['title' => 'Registrarse']);
    }
}