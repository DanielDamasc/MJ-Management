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
        Schema::table('air_conditioners', function (Blueprint $table) {
            $table->decimal('area_climatizada')->nullable();
            $table->integer('numero_ocupantes')->nullable();
            $table->enum('estado_conservacao', ['ruim', 'regular', 'bom'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_conditioners', function (Blueprint $table) {
            $table->dropColumn(['area_climatizada', 'numero_ocupantes', 'estado_conservacao']);
        });
    }
};
