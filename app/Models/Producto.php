<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'categoria_id',
        'nombre',
        'sku',
        'precio',
        'stock',
        'stock_minimo',
        'tiene_3d',
        'activo',       // <-- AGREGAR ESTO
        'avatar_ruta',  // <-- AGREGAR ESTO
        'descripcion'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'tiene_3d' => 'boolean',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'producto_id');
    }

    public function modelo3d()
    {
        return $this->hasOne(Modelo3dProducto::class, 'producto_id')->where('es_activo', true);
    }

    public function detallesPedido()
    {
        return $this->hasMany(DetallePedido::class, 'producto_id');
    }

    public function movimientosStock()
    {
        return $this->hasMany(MovimientoStock::class, 'producto_id');
    }

    // Accessors
    public function getImagenPrincipalAttribute()
    {
        return $this->imagenes()->where('es_principal', true)->first();
    }

    public function getStockBajoAttribute()
    {
        return $this->stock <= $this->stock_minimo;
    }

    // Scopes
    public function scopeConStockBajo($query)
    {
        return $query->whereRaw('stock <= stock_minimo');
    }

    public function scopeDisponibles($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Methods
    public function actualizarStock($cantidad, $tipo, $usuarioId, $pedidoId = null, $motivo = null)
    {
        $stockAnterior = $this->stock;

        switch ($tipo) {
            case 'entrada':
                $this->stock += $cantidad;
                break;
            case 'salida':
                $this->stock -= $cantidad;
                break;
            case 'ajuste':
            case 'devolucion':
                $this->stock = $cantidad;
                break;
        }

        $this->save();

        return MovimientoStock::create([
            'producto_id' => $this->id,
            'usuario_id' => $usuarioId,
            'pedido_id' => $pedidoId,
            'tipo' => $tipo,
            'cantidad' => abs($cantidad),
            'stock_final' => $this->stock,
            'motivo' => $motivo ?? "Movimiento de {$tipo}"
        ]);
    }
}
