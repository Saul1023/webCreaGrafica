<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;  // ← AGREGAR ESTA LÍNEA

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $typeExists = DB::select("SELECT 1 FROM pg_type WHERE typname = 'canal_cliente'");
        if (empty($typeExists)) {
            DB::statement("CREATE TYPE canal_cliente AS ENUM ('presencial','whatsapp','correo','instagram','facebook','referido','otro')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TYPE IF EXISTS canal_cliente");
    }
};
