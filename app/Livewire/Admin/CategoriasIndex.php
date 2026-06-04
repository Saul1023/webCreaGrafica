<?php

namespace App\Livewire\Admin;

use App\Models\CategoriaProducto;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
class CategoriasIndex extends Component
{
 use WithPagination;

    // Filtros
    public $search = '';
    public $perPage = 10;

    // Modal
    public $isOpen = false;
    public $showDeleteModal = false;
    public $categoria_id;
    public $categoria_id_eliminar;
    public $categoria_nombre_eliminar;

    // Formulario
    public $nombre;
    public $slug;
    public $orden = 0;

    protected $rules = [
        'nombre' => 'required|min:3|max:100',
        'slug' => 'required|min:3|max:100|unique:categorias_producto,slug',
        'orden' => 'nullable|integer|min:0',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        'slug.required' => 'El slug es obligatorio.',
        'slug.unique' => 'Este slug ya está en uso.',
        'orden.integer' => 'El orden debe ser un número entero.',
    ];

    public function render()
    {
        $categorias = CategoriaProducto::query()
            ->when($this->search, function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->orderBy('orden', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.categorias-index', [
                'categorias' => $categorias,
                'categorias' => $categorias,
            ])->layout('layouts.admin');
    }

    public function updatedNombre($value)
    {
        $this->slug = Str::slug($value);
    }

    public function create()
    {
        $this->resetForm();
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $categoria = CategoriaProducto::findOrFail($id);
        $this->categoria_id = $id;
        $this->nombre = $categoria->nombre;
        $this->slug = $categoria->slug;
        $this->orden = $categoria->orden;
        $this->isOpen = true;
    }

    public function store()
    {
        $rules = $this->rules;
        if ($this->categoria_id) {
            $rules['slug'] = 'required|min:3|max:100|unique:categorias_producto,slug,' . $this->categoria_id;
        }

        $this->validate($rules);

        $data = [
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'orden' => $this->orden ?: 0,
        ];

        if ($this->categoria_id) {
            CategoriaProducto::where('id', $this->categoria_id)->update($data);
            session()->flash('message', 'Categoría actualizada correctamente.');
        } else {
            CategoriaProducto::create($data);
            session()->flash('message', 'Categoría creada correctamente.');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete($id, $nombre)
    {
        $this->categoria_id_eliminar = $id;
        $this->categoria_nombre_eliminar = $nombre;
        $this->showDeleteModal = true;
    }

    public function deleteCategoria()
    {
        $categoria = CategoriaProducto::find($this->categoria_id_eliminar);

        if ($categoria && $categoria->productos()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        } elseif ($categoria) {
            $categoria->delete();
            session()->flash('message', 'Categoría eliminada correctamente.');
        }

        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->categoria_id = null;
        $this->nombre = '';
        $this->slug = '';
        $this->orden = 0;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
