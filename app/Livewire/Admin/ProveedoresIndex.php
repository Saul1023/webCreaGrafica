<?php

namespace App\Livewire\Admin;

use App\Models\Proveedor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ProveedoresIndex extends Component
{
    use WithPagination;

    // Filtros de búsqueda
    public $search = '';
    public $filtro_activo = ''; // '', '1', '0'

    // Atributos del formulario
    public $proveedor_id;
    public $nombre = '';
    public $contacto_nombre = '';
    public $telefono = '';
    public $correo = '';
    public $direccion = '';
    public $nit = '';
    public $activo = true;

    // Control del Modal
    public $isOpen = false;
    public $isDeleteOpen = false;
    public $proveedor_para_eliminar_id;
    public $proveedor_para_eliminar_nombre;

    protected $queryString = [
        'search' => ['except' => ''],
        'filtro_activo' => ['except' => '']
    ];

    protected $rules = [
        'nombre' => 'required|string|max:150',
        'contacto_nombre' => 'nullable|string|max:150',
        'telefono' => 'nullable|string|max:20',
        'correo' => 'nullable|email|max:120',
        'direccion' => 'nullable|string',
        'nit' => 'nullable|string|max:30',
        'activo' => 'boolean'
    ];

    protected $messages = [
        'nombre.required' => 'El nombre del proveedor es obligatorio.',
        'nombre.max' => 'El nombre no debe superar los 150 caracteres.',
        'contacto_nombre.max' => 'El nombre de contacto no debe superar los 150 caracteres.',
        'telefono.max' => 'El teléfono no debe superar los 20 caracteres.',
        'correo.email' => 'El correo electrónico debe ser una dirección válida.',
        'correo.max' => 'El correo no debe superar los 120 caracteres.',
        'nit.max' => 'El NIT/CI no debe superar los 30 caracteres.'
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroActivo()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->proveedor_id = null;
        $this->nombre = '';
        $this->contacto_nombre = '';
        $this->telefono = '';
        $this->correo = '';
        $this->direccion = '';
        $this->nit = '';
        $this->activo = true;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $this->proveedor_id = $id;
        $this->nombre = $proveedor->nombre;
        $this->contacto_nombre = $proveedor->contacto_nombre;
        $this->telefono = $proveedor->telefono;
        $this->correo = $proveedor->correo;
        $this->direccion = $proveedor->direccion;
        $this->nit = $proveedor->nit;
        $this->activo = $proveedor->activo;

        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'contacto_nombre' => $this->contacto_nombre ?: null,
            'telefono' => $this->telefono ?: null,
            'correo' => $this->correo ?: null,
            'direccion' => $this->direccion ?: null,
            'nit' => $this->nit ?: null,
            'activo' => $this->activo,
            'actualizado_en' => now()
        ];

        if ($this->proveedor_id) {
            $proveedor = Proveedor::findOrFail($this->proveedor_id);
            $proveedor->update($data);
            session()->flash('message', 'Proveedor actualizado correctamente.');
        } else {
            $data['creado_en'] = now();
            Proveedor::create($data);
            session()->flash('message', 'Proveedor registrado correctamente.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id, $nombre)
    {
        $this->proveedor_para_eliminar_id = $id;
        $this->proveedor_para_eliminar_nombre = $nombre;
        $this->isDeleteOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->isDeleteOpen = false;
        $this->proveedor_para_eliminar_id = null;
        $this->proveedor_para_eliminar_nombre = null;
    }

    public function deleteProveedor()
    {
        if ($this->proveedor_para_eliminar_id) {
            $proveedor = Proveedor::findOrFail($this->proveedor_para_eliminar_id);
            
            // Si el proveedor ya tiene compras, no se puede eliminar físicamente para conservar la integridad
            if ($proveedor->compras()->exists()) {
                // Se desactiva automáticamente
                $proveedor->activo = false;
                $proveedor->save();
                session()->flash('error', 'El proveedor no se puede eliminar físicamente porque tiene compras asociadas. Ha sido desactivado.');
            } else {
                $proveedor->delete();
                session()->flash('message', 'Proveedor eliminado correctamente.');
            }
        }

        $this->closeDeleteModal();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $driver = \DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

        $query = Proveedor::query();

        if (strlen($this->search) > 0) {
            $query->where(function($q) use ($likeOperator) {
                $q->where('nombre', $likeOperator, '%' . $this->search . '%')
                  ->orWhere('contacto_nombre', $likeOperator, '%' . $this->search . '%')
                  ->orWhere('nit', $likeOperator, '%' . $this->search . '%')
                  ->orWhere('correo', $likeOperator, '%' . $this->search . '%');
            });
        }

        if ($this->filtro_activo !== '') {
            $query->where('activo', (bool)$this->filtro_activo);
        }

        $proveedores = $query->orderBy('nombre')
            ->paginate(10);

        return view('livewire.admin.proveedores-index', [
            'proveedores' => $proveedores
        ]);
    }
}
