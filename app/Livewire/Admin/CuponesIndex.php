<?php

namespace App\Livewire\Admin;

use App\Models\Cupon;
use Livewire\Component;
use Livewire\WithPagination;

class CuponesIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Form attributes
    public $cupon_id, $codigo, $tipo = 'fijo', $valor, $limite_uso, $compra_minima = 0, $activo = true, $fecha_expiracion;

    public $isOpen = false;

    protected function rules()
    {
        $id = $this->cupon_id ?: 'NULL';

        return [
            'codigo' => 'required|max:50|alpha_num|unique:cupones,codigo,' . $id,
            'tipo' => 'required|in:fijo,porcentaje',
            'valor' => 'required|numeric|min:0.01',
            'limite_uso' => 'nullable|integer|min:1',
            'compra_minima' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
            'fecha_expiracion' => 'nullable|date|after_or_equal:today',
        ];
    }

    protected $messages = [
        'codigo.required' => 'El código del cupón es obligatorio.',
        'codigo.unique' => 'Este código de cupón ya existe.',
        'codigo.alpha_num' => 'El código solo puede contener letras y números.',
        'valor.required' => 'El valor es obligatorio.',
        'valor.numeric' => 'El valor debe ser un número.',
        'valor.min' => 'El valor debe ser mayor a 0.',
        'compra_minima.required' => 'La compra mínima es obligatoria.',
        'fecha_expiracion.after_or_equal' => 'La fecha de expiración debe ser hoy o una fecha futura.',
    ];

    public function render()
    {
        $cupones = Cupon::when($this->search, function ($query) {
            $query->where('codigo', 'like', '%' . $this->search . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate($this->perPage);

        return view('livewire.admin.cupones-index', [
            'cupones' => $cupones
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $cupon = Cupon::findOrFail($id);
        $this->cupon_id = $id;
        $this->codigo = $cupon->codigo;
        $this->tipo = $cupon->tipo;
        $this->valor = $cupon->valor;
        $this->limite_uso = $cupon->limite_uso;
        $this->compra_minima = $cupon->compra_minima;
        $this->activo = $cupon->activo;
        $this->fecha_expiracion = $cupon->fecha_expiracion ? $cupon->fecha_expiracion->format('Y-m-d') : null;

        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'codigo' => strtoupper($this->codigo),
            'tipo' => $this->tipo,
            'valor' => $this->valor,
            'limite_uso' => $this->limite_uso ?: null,
            'compra_minima' => $this->compra_minima,
            'activo' => $this->activo,
            'fecha_expiracion' => $this->fecha_expiracion ?: null,
            'actualizado_en' => now()
        ];

        if ($this->cupon_id) {
            $cupon = Cupon::findOrFail($this->cupon_id);
            $cupon->update($data);
            session()->flash('message', 'Cupón actualizado correctamente.');
        } else {
            $data['veces_usado'] = 0;
            $data['creado_en'] = now();
            Cupon::create($data);
            session()->flash('message', 'Cupón creado correctamente.');
        }

        $this->closeModal();
    }

    public function toggleActivo($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->activo = !$cupon->activo;
        $cupon->save();
        session()->flash('message', 'Estado del cupón actualizado.');
    }

    public function delete($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->delete();
        session()->flash('message', 'Cupón eliminado correctamente.');
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->cupon_id = null;
        $this->codigo = '';
        $this->tipo = 'fijo';
        $this->valor = '';
        $this->limite_uso = '';
        $this->compra_minima = 0;
        $this->activo = true;
        $this->fecha_expiracion = '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
