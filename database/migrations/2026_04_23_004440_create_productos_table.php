<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_producto')->nullOnDelete();
            $table->string('nombre', 150);
            $table->string('sku', 60)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->boolean('tiene_3d')->default(false);
            $table->boolean('activo')->default(true);  // ← CAMPO ACTIVO AGREGADO
            $table->string('avatar_ruta', 500)->nullable();  // Imagen principal
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();

            // Índices para mejorar rendimiento
            $table->index('activo');
            $table->index('sku');
            $table->index('precio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
