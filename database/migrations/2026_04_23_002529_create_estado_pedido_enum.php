<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $typeExists = DB::select("SELECT 1 FROM pg_type WHERE typname = 'estado_pedido'");
        if (empty($typeExists)) {
            DB::statement("CREATE TYPE estado_pedido AS ENUM ('cotizacion', 'pendiente', 'en_diseno', 'aprobado', 'en_produccion', 'listo', 'entregado', 'cancelado')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE IF EXISTS estado_pedido");
    }
};
