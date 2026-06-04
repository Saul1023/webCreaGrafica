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
        Schema::create('segmentos_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('color_hex', 7)->nullable();
            $table->timestampTz('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segmentos_cliente');
    }
};