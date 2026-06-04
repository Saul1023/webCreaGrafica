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
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->restrictOnDelete();
            $table->string('nombre', 120);
            $table->string('apellido', 80)->nullable();
            $table->string('nombre_usuario', 60)->unique()->nullable();
            $table->string('correo', 150)->unique();
            $table->string('clave', 255);
            $table->string('telefono', 20)->nullable();
            $table->string('avatar_ruta', 500)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestampTz('ultimo_login')->nullable();
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();
            $table->rememberToken();
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