<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $search = '';
    public $isOpen = false;

    // Campos exactos de tu modelo Product
    public $name, $description, $price, $stock, $category, $image;

    // Booleanos (inicializados en false o true)
    public $active = 1;
    public $featured = 0;

    protected function rules()
    {
        return [
            'name'        => 'required|min:3',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category'    => 'required|string|max:50',
            'active'      => 'boolean',
            'featured'    => 'boolean',
            'image'       => 'nullable|image|max:2048', // 2MB Max
        ];
    }

    public function render()
    {
        $products = Product::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('category', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.product-index', [
            'products' => $products
        ])
        ->layout('components.layouts.admin', ['title' => 'Gestión de Productos']);
    }

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
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->category = '';
        $this->active = 1;
        $this->featured = 0;
        $this->image = null;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        // Manejo de la imagen
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('products', 'public');
        }

        Product::create([
            'name'        => $this->name,
            'description' => $this->description,
            'price'       => $this->price,
            'stock'       => $this->stock,
            'category'    => $this->category,
            'active'      => $this->active,
            'featured'    => $this->featured,
            'image'       => $imagePath,
        ]);

        session()->flash('message', 'Producto creado exitosamente.');
        $this->closeModal();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
