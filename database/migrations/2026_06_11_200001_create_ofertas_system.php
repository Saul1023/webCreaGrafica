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
        // 1. Quitar columnas viejas de productos
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['en_oferta']);
            $table->dropColumn(['precio_oferta', 'en_oferta']);
        });

        // 2. Crear tabla de ofertas
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->decimal('descuento', 5, 2); // Porcentaje de descuento (ej. 15.00 para 15%)
            $table->timestampTz('fecha_inicio');
            $table->timestampTz('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();
            
            $table->index('activo');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
        });

        // 3. Crear tabla pivote oferta_producto
        Schema::create('oferta_producto', function (Blueprint $table) {
            $table->foreignId('oferta_id')->constrained('ofertas')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->primary(['oferta_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oferta_producto');
        Schema::dropIfExists('ofertas');

        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_oferta', 10, 2)->nullable()->after('precio');
            $table->boolean('en_oferta')->default(false)->after('precio_oferta');
            $table->index('en_oferta');
        });
    }
};
