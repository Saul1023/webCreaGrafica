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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_apertura_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('usuarios')->onDelete('restrict');
            $table->timestampTz('fecha_apertura');
            $table->timestampTz('fecha_cierre')->nullable();
            $table->decimal('monto_apertura', 12, 2);
            $table->decimal('monto_cierre', 12, 2)->nullable();
            $table->decimal('monto_real_efectivo', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->string('estado', 20)->default('abierta'); // 'abierta', 'cerrada'
            $table->text('observaciones')->nullable();
            
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();

            $table->index('estado');
            $table->index('fecha_apertura');
        });

        Schema::create('transacciones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('cascade');
            $table->string('tipo', 10); // 'ingreso', 'egreso'
            $table->string('concepto', 255);
            $table->decimal('monto', 12, 2);
            $table->string('metodo_pago', 20); // 'efectivo', 'qr', 'transferencia', 'otro'
            $table->string('referencia', 100)->nullable();
            
            $table->timestampTz('creado_en')->useCurrent();
            $table->timestampTz('actualizado_en')->useCurrent();

            $table->index('tipo');
            $table->index('metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_caja');
        Schema::dropIfExists('cajas');
    }
};
