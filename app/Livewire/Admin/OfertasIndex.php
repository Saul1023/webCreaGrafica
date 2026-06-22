<?php

namespace App\Livewire\Admin;

use App\Models\Oferta;
use App\Models\Producto;
use Livewire\Component;
use Livewire\WithPagination;

class OfertasIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Form attributes
    public $oferta_id, $nombre, $descuento, $fecha_inicio, $fecha_fin, $activo = true;
    public $productos_seleccionados = []; // Array de IDs de productos seleccionados

    public $isOpen = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|min:3|max:150',
            'descuento' => 'required|numeric|min:0.01|max:100',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required|after:fecha_inicio',
            'activo' => 'required|boolean',
            'productos_seleccionados' => 'nullable|array',
            'productos_seleccionados.*' => 'exists:productos,id',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre de la oferta es obligatorio.',
        'nombre.min' => 'El nombre debe tener al menos 3 caracteres.',
        'descuento.required' => 'El porcentaje de descuento es obligatorio.',
        'descuento.numeric' => 'El descuento debe ser un valor numérico.',
        'descuento.min' => 'El descuento mínimo es 0.01%.',
        'descuento.max' => 'El descuento máximo es 100%.',
        'fecha_inicio.required' => 'La fecha y hora de inicio es obligatoria.',
        'fecha_fin.required' => 'La fecha y hora de fin es obligatoria.',
        'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
    ];

    public function render()
    {
        $ofertas = Oferta::when($this->search, function ($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate($this->perPage);

        $todosProductos = Producto::where('activo', true)->orderBy('nombre')->get();

        return view('livewire.admin.ofertas-index', [
            'ofertas' => $ofertas,
            'todosProductos' => $todosProductos
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $oferta = Oferta::with('productos')->findOrFail($id);
        $this->oferta_id = $id;
        $this->nombre = $oferta->nombre;
        $this->descuento = $oferta->descuento;
        $this->fecha_inicio = $oferta->fecha_inicio ? $oferta->fecha_inicio->format('Y-m-d\TH:i') : null;
        $this->fecha_fin = $oferta->fecha_fin ? $oferta->fecha_fin->format('Y-m-d\TH:i') : null;
        $this->activo = $oferta->activo;
        $this->productos_seleccionados = $oferta->productos->pluck('id')->toArray();

        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'descuento' => $this->descuento,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'activo' => $this->activo,
            'actualizado_en' => now()
        ];

        if ($this->oferta_id) {
            $oferta = Oferta::findOrFail($this->oferta_id);
            $oferta->update($data);
            $oferta->productos()->sync($this->productos_seleccionados ?: []);
            session()->flash('message', 'Oferta actualizada correctamente.');
        } else {
            $data['creado_en'] = now();
            $oferta = Oferta::create($data);
            $oferta->productos()->sync($this->productos_seleccionados ?: []);
            session()->flash('message', 'Oferta creada correctamente.');
        }

        $this->closeModal();
    }

    public function toggleActivo($id)
    {
        $oferta = Oferta::findOrFail($id);
        $oferta->activo = !$oferta->activo;
        $oferta->save();
        session()->flash('message', 'Estado de la oferta actualizado.');
    }

    public function delete($id)
    {
        $oferta = Oferta::findOrFail($id);
        $oferta->delete(); // Elimina también el pivote por la FK cascadeOnDelete
        session()->flash('message', 'Oferta eliminada correctamente.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->oferta_id = null;
        $this->nombre = '';
        $this->descuento = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->activo = true;
        $this->productos_seleccionados = [];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
