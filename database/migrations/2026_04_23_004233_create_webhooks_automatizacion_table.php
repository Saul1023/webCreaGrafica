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
        Schema::create('webhooks_automatizacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('evento_disparo', 80);
            $table->string('url_webhook', 500);
            $table->boolean('activo')->default(true);
            $table->timestampTz('ultimo_disparo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks_automatizacion');
    }
};