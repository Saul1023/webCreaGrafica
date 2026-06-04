<?php

namespace App\Livewire\Admin;

use App\Models\Usuario;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UsersIndex extends Component
{
    use WithPagination;

    // Filtros
    public $search = '';
    public $rol_filtro = '';
    public $perPage = 10;

    protected $paginationTheme = 'tailwind';

    // Modal crear/editar
    public $isOpen = false;
    public $user_id = null;

    // Modal eliminar
    public $showDeleteModal = false;
    public $user_id_eliminar = null;
    public $user_nombre_eliminar = '';

    // Formulario
    public $nombre = '';
    public $apellido = '';
    public $nombre_usuario = '';
    public $correo = '';
    public $clave = '';
    public $clave_confirmation = '';
    public $telefono = '';
    public $rol_id = 2;
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|min:3|max:120',
        'apellido' => 'nullable|max:80',
        'nombre_usuario' => 'nullable|min:3|max:60|unique:usuarios,nombre_usuario',
        'correo' => 'required|email|unique:usuarios,correo',
        'clave' => 'required|min:6',
        'telefono' => 'nullable|max:20',
        'rol_id' => 'required|exists:roles,id',
        'activo' => 'boolean',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        'correo.required' => 'El correo electrónico es obligatorio.',
        'correo.email' => 'Ingrese un correo electrónico válido.',
        'correo.unique' => 'Este correo ya está registrado.',
        'nombre_usuario.unique' => 'Este nombre de usuario ya está ocupado.',
        'clave.required' => 'La contraseña es obligatoria.',
        'clave.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'rol_id.required' => 'Debe seleccionar un rol.',
    ];

    public function render()
    {
        $usuarios = Usuario::with('rol')
            ->when($this->search, function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('apellido', 'like', '%' . $this->search . '%')
                    ->orWhere('nombre_usuario', 'like', '%' . $this->search . '%')
                    ->orWhere('correo', 'like', '%' . $this->search . '%');
            })
            ->when($this->rol_filtro, function($query) {
                $query->where('rol_id', $this->rol_filtro);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $roles = Role::where('activo', true)->get();

        return view('livewire.admin.users-index', [
                'usuarios' => $usuarios,
                'roles' => $roles
            ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields(); // <--- CORRECTO: Así se llama tu función abajo
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $user = Usuario::findOrFail($id);

        $this->user_id = $id;
        $this->nombre = $user->nombre;
        $this->apellido = $user->apellido;
        $this->nombre_usuario = $user->nombre_usuario;
        $this->correo = $user->correo;
        $this->telefono = $user->telefono;
        $this->rol_id = $user->rol_id;
        $this->activo = $user->activo;
        $this->clave = '';
        $this->clave_confirmation = '';
        $this->isOpen = true;
        $this->resetErrorBag();
    }

    public function store()
    {
        if ($this->user_id) {
            $rules = [
                'nombre' => 'required|min:3|max:120',
                'apellido' => 'nullable|max:80',
                'nombre_usuario' => 'nullable|min:3|max:60|unique:usuarios,nombre_usuario,' . $this->user_id,
                'correo' => 'required|email|unique:usuarios,correo,' . $this->user_id,
                'clave' => 'nullable|min:6',
                'telefono' => 'nullable|max:20',
                'rol_id' => 'required|exists:roles,id',
                'activo' => 'boolean',
            ];
        } else {
            $rules = $this->rules;
        }

        $this->validate($rules);

        // Verificar confirmación de contraseña
        if (!$this->user_id && $this->clave !== $this->clave_confirmation) {
            $this->addError('clave_confirmation', 'Las contraseñas no coinciden.');
            return;
        }

        if ($this->user_id && !empty($this->clave) && $this->clave !== $this->clave_confirmation) {
            $this->addError('clave_confirmation', 'Las contraseñas no coinciden.');
            return;
        }

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

        $data = [
            'rol_id' => $this->rol_id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_usuario' => $this->nombre_usuario,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'activo' => $this->activo,
            'actualizado_en' => now(),
        ];

        if ($this->clave) {
            $data['clave'] = $this->clave; // El mutador del modelo se encargará del hasheo seguro
        }

        if ($this->user_id) {
            $user = Usuario::findOrFail($this->user_id);
            $user->update($data);
            session()->flash('message', 'Usuario actualizado exitosamente.');
        } else {
            $data['creado_en'] = now();
            Usuario::create($data);
            session()->flash('message', 'Usuario creado exitosamente.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete($id, $nombre)
    {
        $this->user_id_eliminar = $id;
        $this->user_nombre_eliminar = $nombre;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        $user = Usuario::find($this->user_id_eliminar);
        if ($user && $user->id != auth()->id()) {
            $user->delete();
            session()->flash('message', 'Usuario eliminado correctamente.');
        } else {
            session()->flash('error', 'No puedes eliminar tu propio usuario.');
        }
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function toggleActivo($id)
    {
        $user = Usuario::find($id);
        if ($user && $user->id != auth()->id()) {
            $user->activo = !$user->activo;
            $user->save();
            session()->flash('message', 'Estado del usuario actualizado.');
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetErrorBag();
    }

    public function resetInputFields()
    {
        $this->user_id = null;
        $this->nombre = '';
        $this->apellido = '';
        $this->nombre_usuario = '';
        $this->correo = '';
        $this->clave = '';
        $this->clave_confirmation = '';
        $this->telefono = '';
        $this->rol_id = 2;
        $this->activo = true;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRolFiltro()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}