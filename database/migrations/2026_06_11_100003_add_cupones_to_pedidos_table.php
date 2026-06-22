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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('cupon_id')->nullable()->after('usuario_id')->constrained('cupones')->nullOnDelete();
            $table->decimal('descuento', 10, 2)->default(0.00)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cupon_id');
            $table->dropColumn('descuento');
        });
    }
};
