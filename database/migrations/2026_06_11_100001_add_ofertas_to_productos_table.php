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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_oferta', 10, 2)->nullable()->after('precio');
            $table->boolean('en_oferta')->default(false)->after('precio_oferta');
            
            $table->index('en_oferta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropIndex(['en_oferta']);
            $table->dropColumn(['precio_oferta', 'en_oferta']);
        });
    }
};
