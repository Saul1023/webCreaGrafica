<?php

namespace App\Livewire\Admin;

use App\Models\Usuario;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UsersIndex extends Component
{
    use WithPagination;

    // Variables de búsqueda y modal
    public $search = '';
    public $isOpen = false;

    // Variables del formulario de creación
    public $nombre_completo, $email, $password, $telefono, $id_rol, $estado;

    protected function rules()
    {
        return [
            'nombre_completo' => 'required|min:3',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6',
            'id_rol' => 'required|in:1,2', // 1: Admin, 2: Cliente
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    public function render()
    {
        $users = Usuario::query()
            ->where('nombre_completo', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('id_usuario', 'desc')
            ->paginate(10);

        return view('livewire.admin.users-index', [
            'users' => $users
        ])
        ->layout('components.layouts.admin', ['title' => 'Gestión de Usuarios']);
    }

    // Funciones del Modal
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->nombre_completo = '';
        $this->email = '';
        $this->password = '';
        $this->telefono = '';
        $this->id_rol = 2; // Por defecto Cliente
        $this->estado = 'activo';
        $this->resetValidation();
    }

    // Guardar nuevo usuario
    public function store()
    {
        $this->validate();

        // Generar nombre de usuario automáticamente
        $nombre_usuario = strtolower(str_replace(' ', '', $this->nombre_completo)) . rand(100, 999);

        Usuario::create([
            'nombre_completo' => $this->nombre_completo,
            'nombre_usuario' => $nombre_usuario,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'telefono' => $this->telefono,
            'id_rol' => $this->id_rol,
            'estado' => $this->estado,
            'fecha_creacion' => now(),
            'fecha_actualizacion' => now(),
        ]);

        session()->flash('message', 'Usuario creado exitosamente.');

        $this->closeModal();
    }

    // Resetear paginación al buscar
    public function updatedSearch()
    {
        $this->resetPage();
    }
}
