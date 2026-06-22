<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $primaryKey = 'id';

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'limite_uso',
        'veces_usado',
        'compra_minima',
        'activo',
        'fecha_expiracion'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'limite_uso' => 'integer',
        'veces_usado' => 'integer',
        'compra_minima' => 'decimal:2',
        'activo' => 'boolean',
        'fecha_expiracion' => 'date',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    /**
     * Relación con los Pedidos.
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cupon_id');
    }

    /**
     * Valida si el cupón se puede aplicar a una compra de cierto monto.
     *
     * @param float $montoTotal
     * @return array
     */
    public function esValidoPara($montoTotal)
    {
        if (!$this->activo) {
            return [
                'valido' => false,
                'mensaje' => 'El cupón no está activo.'
            ];
        }

        if ($this->fecha_expiracion && $this->fecha_expiracion->isPast() && !$this->fecha_expiracion->isToday()) {
            return [
                'valido' => false,
                'mensaje' => 'El cupón ha expirado.'
            ];
        }

        if ($this->veces_usado >= 1) {
            return [
                'valido' => false,
                'mensaje' => 'Este cupón ya ha sido utilizado.'
            ];
        }

        if ($this->limite_uso !== null && $this->veces_usado >= $this->limite_uso) {
            return [
                'valido' => false,
                'mensaje' => 'El cupón ha alcanzado su límite de usos.'
            ];
        }

        if ($montoTotal < $this->compra_minima) {
            return [
                'valido' => false,
                'mensaje' => "El monto mínimo de compra para usar este cupón es Bs. " . number_format($this->compra_minima, 2)
            ];
        }

        return [
            'valido' => true,
            'mensaje' => 'Cupón aplicado con éxito.'
        ];
    }

    /**
     * Calcula la rebaja correspondiente al monto especificado.
     *
     * @param float $montoTotal
     * @return float
     */
    public function calcularDescuentoPara($montoTotal)
    {
        if ($this->tipo === 'porcentaje') {
            $descuento = ($montoTotal * $this->valor) / 100;
            return min($descuento, $montoTotal);
        }

        // Fijo
        return min($this->valor, $montoTotal);
    }
}
