<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
        'featured',
        'active',
        'stock'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'active' => 'boolean'
    ];

    // Scope para productos activos
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Scope para productos por categoría
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope para productos destacados
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
