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
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('tipo', 20); // 'fijo' o 'porcentaje'
            $table->decimal('valor', 10, 2);
            $table->integer('limite_uso')->nullable();
            $table->integer('veces_usado')->default(0);
            $table->decimal('compra_minima', 10, 2)->default(0.00);
            $table->boolean('activo')->default(true);
            $table->date('fecha_expiracion')->nullable();
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();
            
            $table->index('activo');
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
