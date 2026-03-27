<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public $productId;
    public $name;
    public $description;
    public $price;
    public $image;
    public $category;
    public $featured = false;
    public $active = true;
    public $stock = 0;
    public $existingImage;

    public $isEditing = false;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'required|min:10',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|max:2048',
        'category' => 'required|in:parejas,niños,graduacion,personalizado,otros',
        'stock' => 'required|integer|min:0',
        'featured' => 'boolean',
        'active' => 'boolean'
    ];

    protected $messages = [
        'name.required' => 'El nombre del producto es obligatorio.',
        'name.min' => 'El nombre debe tener al menos 3 caracteres.',
        'description.required' => 'La descripción es obligatoria.',
        'description.min' => 'La descripción debe tener al menos 10 caracteres.',
        'price.required' => 'El precio es obligatorio.',
        'price.numeric' => 'El precio debe ser un número.',
        'price.min' => 'El precio no puede ser negativo.',
        'image.image' => 'El archivo debe ser una imagen.',
        'image.max' => 'La imagen no debe pesar más de 2MB.',
        'category.required' => 'La categoría es obligatoria.',
        'category.in' => 'Selecciona una categoría válida.',
        'stock.required' => 'El stock es obligatorio.',
        'stock.integer' => 'El stock debe ser un número entero.',
        'stock.min' => 'El stock no puede ser negativo.'
    ];

    public function mount($productId = null)
    {
        if ($productId) {
            $this->productId = $productId;
            $this->loadProduct();
            $this->isEditing = true;
        }
    }

    public function loadProduct()
    {
        $product = Product::findOrFail($this->productId);

        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->category = $product->category;
        $this->featured = $product->featured;
        $this->active = $product->active;
        $this->stock = $product->stock;
        $this->existingImage = $product->image;
    }

    public function save()
    {
        $this->validate();

        $imagePath = $this->existingImage;

        // Procesar nueva imagen si se subió
        if ($this->image) {
            // Eliminar imagen anterior si existe
            if ($this->existingImage && Storage::exists('public/products/' . $this->existingImage)) {
                Storage::delete('public/products/' . $this->existingImage);
            }

            // Guardar nueva imagen
            $imagePath = $this->image->store('products', 'public');
            $imagePath = basename($imagePath);
        }

        if ($this->isEditing) {
            // Actualizar producto existente
            $product = Product::findOrFail($this->productId);
            $product->update([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'image' => $imagePath,
                'category' => $this->category,
                'featured' => $this->featured,
                'active' => $this->active,
                'stock' => $this->stock
            ]);

            session()->flash('message', '✅ Producto actualizado correctamente!');
        } else {
            // Crear nuevo producto
            Product::create([
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'image' => $imagePath,
                'category' => $this->category,
                'featured' => $this->featured,
                'active' => $this->active,
                'stock' => $this->stock
            ]);

            session()->flash('message', '✅ Producto creado correctamente!');
        }

        $this->resetForm();
        $this->dispatch('productSaved');
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'description', 'price', 'image', 'category',
            'featured', 'active', 'stock', 'productId', 'isEditing'
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.product.product-form');
    }
}
