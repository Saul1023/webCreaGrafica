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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');

            $table->foreignId('id_rol')
                  ->constrained('roles', 'id_rol')
                  ->onDelete('restrict');

            $table->string('nombre_usuario', 50)->unique();
            $table->string('password', 255);
            $table->string('nombre_completo', 100);
            $table->string('email', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->boolean('estado')->default(true);

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestampTz('fecha_actualizacion')->nullable();
            $table->timestamp('ultimo_acceso')->nullable();

            // Índices
            $table->index('id_rol');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
