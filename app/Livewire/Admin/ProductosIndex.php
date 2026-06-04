<?php

namespace App\Livewire\Admin;

use App\Models\CategoriaProducto;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductosIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $categoria_id = '';
    public $perPage = 10;

    // Propiedades del formulario
    public $producto_id, $nombre, $sku, $precio, $stock;
    public $descripcion, $tiene_3d = false, $activo = true;
    public $imagen, $imagen_actual;

    // Soporte para archivos de Modelos 3D
    public $file_3d;
    public $model_3d_actual; // Almacena el registro actual de la relación de forma informativa

    public $isOpen = false;
    public $showDeleteModal = false;

    // Validación simplificada y adaptada para PostgreSQL y archivos 3D
    public function rules()
    {
        $id = $this->producto_id ?: 'NULL';

        $rules = [
            'nombre'      => 'required|min:3|max:150',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'categoria_id'=> 'required|exists:categorias_producto,id',
            'sku'         => 'required|max:60|unique:productos,sku,' . $id,

            // ✅ Subido de 10240 (10MB) a 51200 (50MB)
            'imagen'      => 'nullable|image|max:51200',
        ];

        if ($this->tiene_3d) {
            // ✅ Subido de 20480 (20MB) a 51200 (50MB)
            $rules['file_3d'] = $this->producto_id
                ? 'nullable|file|max:51200'
                : 'required|file|max:51200';
        }

        return $rules;
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'sku.required' => 'El SKU es obligatorio.',
        'sku.unique' => 'Este SKU ya existe.',
        'precio.required' => 'El precio es obligatorio.',
        'stock.required' => 'El stock es obligatorio.',
        'categoria_id.required' => 'Debe seleccionar una categoría.',
        'imagen.image' => 'El archivo debe ser una imagen.',
        'imagen.max' => 'La imagen no debe superar los 10MB.',
        'file_3d.required' => 'El archivo de modelo 3D es obligatorio cuando se activa la opción.',
        'file_3d.file' => 'Debe cargar un archivo válido para el modelo 3D.',
        'file_3d.max' => 'El archivo 3D no debe superar los 20MB.',
    ];

    public function render()
    {
        $productos = Producto::with(['categoria', 'modelo3d'])
            ->when($this->search, function($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoria_id, function($query) {
                $query->where('categoria_id', $this->categoria_id);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.productos-index', [
            'productos' => $productos,
            'categorias' => CategoriaProducto::all()
        ])->layout('layouts.admin');
    }

    public function testUpload()
    {
        \Log::info('Test de subida iniciado');
        \Log::info('Configuración temporal:', [
            'disk' => config('livewire.temporary_file_upload.disk'),
            'rules' => config('livewire.temporary_file_upload.rules'),
            'directory' => config('livewire.temporary_file_upload.directory'),
        ]);

        $disk = Storage::disk('public');
        \Log::info('Directorio public existe: ' . ($disk->exists('/') ? 'Sí' : 'No'));
    }

    public function create()
    {
        \Log::info('CLICK EN create, valor anterior de isOpen: ' . json_encode($this->isOpen));

        $this->resetInputFields();
        $this->sku = 'PROD-' . strtoupper(bin2hex(random_bytes(3)));
        $this->isOpen = true;

        \Log::info('DESPUÉS DE create, isOpen: ' . json_encode($this->isOpen));
    }

    public function edit($id)
    {
        $producto = Producto::with('modelo3d')->findOrFail($id);
        $this->producto_id = $id;
        $this->nombre = $producto->nombre;
        $this->sku = $producto->sku;
        $this->precio = $producto->precio;
        $this->stock = $producto->stock;
        $this->descripcion = $producto->descripcion;
        $this->categoria_id = $producto->categoria_id;
        $this->tiene_3d = $producto->tiene_3d;
        $this->activo = $producto->activo;
        $this->imagen_actual = $producto->avatar_ruta;

        // Cargar datos del modelo 3D actual si existe
        $this->model_3d_actual = $producto->modelo3d ? $producto->modelo3d->ruta_modelo : null;

        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'sku' => $this->sku,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'categoria_id' => $this->categoria_id,
            'descripcion' => $this->descripcion,
            'tiene_3d' => $this->tiene_3d,
            'activo' => $this->activo,
            'actualizado_en' => now(),
        ];

        // Manejo de la imagen principal
        if ($this->imagen && is_object($this->imagen)) {
            try {
                $nombreArchivo = time() . '_' . uniqid() . '.' . $this->imagen->getClientOriginalExtension();
                $path = $this->imagen->storeAs('productos', $nombreArchivo, 'public');
                $data['avatar_ruta'] = $path;

                if ($this->producto_id && $this->imagen_actual) {
                    Storage::disk('public')->delete($this->imagen_actual);
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Error al subir la imagen: ' . $e->getMessage());
            }
        } elseif ($this->producto_id && $this->imagen_actual) {
            $data['avatar_ruta'] = $this->imagen_actual;
        }

        // Operación de Base de Datos
        if ($this->producto_id) {
            $producto = Producto::findOrFail($this->producto_id);
            $producto->update($data);
            session()->flash('message', 'Producto actualizado correctamente.');
        } else {
            $data['creado_en'] = now();
            $producto = Producto::create($data);
            session()->flash('message', 'Producto creado correctamente.');
        }

        // MANEJO INTEGRADO DEL ARCHIVO DEL MODELO 3D (.glb / .gltf)
        if ($this->tiene_3d && $this->file_3d && is_object($this->file_3d)) {
            try {
                // Eliminar archivo 3D anterior si existe mediante la relación estructurada
                if ($producto->modelo3d) {
                    Storage::disk('public')->delete($producto->modelo3d->ruta_modelo);
                    $producto->modelo3d()->delete();
                }

                // Almacenar el nuevo modelo 3D en la carpeta pública
                $nombreArchivo3d = time() . '_3d_' . uniqid() . '.' . $this->file_3d->getClientOriginalExtension();
                $path3d = $this->file_3d->storeAs('modelos3d', $nombreArchivo3d, 'public');

                // Crear registro en la tabla dependiente a través del método hasOne de Eloquent
                $producto->modelo3d()->create([
                    'ruta_modelo' => $path3d,
                    'es_activo'   => true,
                    'creado_en'   => now(),
                ]);
            } catch (\Exception $e) {
                session()->flash('error', 'Error al procesar el archivo 3D: ' . $e->getMessage());
            }
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function delete($id)
    {
        $producto = Producto::with('modelo3d')->findOrFail($id);

        // Limpieza física del disco: Imagen
        if ($producto->avatar_ruta) {
            Storage::disk('public')->delete($producto->avatar_ruta);
        }

        // Limpieza física del disco: Archivo 3D
        if ($producto->modelo3d && $producto->modelo3d->ruta_modelo) {
            Storage::disk('public')->delete($producto->modelo3d->ruta_modelo);
        }

        $producto->delete(); // Eliminará en cascada o de forma regular según la FK
        session()->flash('message', 'Producto eliminado correctamente.');
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->producto_id = null;
        $this->nombre = '';
        $this->sku = '';
        $this->precio = '';
        $this->stock = '';
        $this->descripcion = '';
        $this->categoria_id = '';
        $this->tiene_3d = false;
        $this->activo = true;
        $this->imagen = null;
        $this->imagen_actual = null;
        $this->file_3d = null; // Limpieza obligatoria del buffer 3D
        $this->model_3d_actual = null;
        $this->resetErrorBag();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoriaId()
    {
        $this->resetPage();
    }
}
