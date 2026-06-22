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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('contacto_nombre', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 120)->nullable();
            $table->text('direccion')->nullable();
            $table->string('nit', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();

            // Índices para búsquedas
            $table->index('nombre');
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
