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
        // pivot table.
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();

            // Chaves estrangeiras.
            $table->foreignId('plan_id')->constrained('pmoc_plans')->onDelete('cascade');
            $table->foreignId('task_id')->constrained('pmoc_tasks')->onDelete('cascade');

            // Atributos adicionais.
            $table->tinyInteger('periodicidade'); // enum.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
