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
        $typeExists = DB::select("SELECT 1 FROM pg_type WHERE typname = 'tipo_movimiento_stock'");
        if (empty($typeExists)) {
            DB::statement("CREATE TYPE tipo_movimiento_stock AS ENUM ('entrada','salida','ajuste','devolucion')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE IF EXISTS tipo_movimiento_stock");
    }
};
