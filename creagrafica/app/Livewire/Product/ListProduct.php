<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithPagination;

class ListProduct extends Component
{
    use WithPagination;

    public $selectedCategory = 'todos';
    public $search = '';
    public $perPage = 12;

    protected $queryString = [
        'selectedCategory' => ['except' => 'todos'],
        'search' => ['except' => '']
    ];

    public function mount()
    {
        $this->selectedCategory = request()->query('selectedCategory', 'todos');
        $this->search = request()->query('search', '');
    }

    public function loadMore()
    {
        $this->perPage += 12;
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if ($product && $product->stock > 0) {
            // Aquí iría tu lógica real de carrito (Shopping Cart)
            // Por ahora solo emitimos mensaje
            session()->flash('message', "{$product->name} añadido al carrito!");
            $this->dispatch('cartUpdated');
        }
    }

    public function render()
    {
        // Usamos el scopeActive del modelo para solo traer productos activos
        $query = Product::active();

        // Filtrar por categoría
        if ($this->selectedCategory && $this->selectedCategory !== 'todos') {
            $query->where('category', $this->selectedCategory);
        }

        // Filtrar por búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // ORDENAMIENTO: Primero los destacados, luego los más nuevos
        $products = $query->orderBy('featured', 'desc') // <--- IMPORTANTE
                          ->orderBy('created_at', 'desc')
                          ->paginate($this->perPage);

        // Obtener categorías dinámicas solo de productos activos
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category') // Evitar vacíos
            ->pluck('category');

        return view('livewire.product.list-product', [
            'products' => $products,
            'categories' => $categories
        ]);
    }
}
