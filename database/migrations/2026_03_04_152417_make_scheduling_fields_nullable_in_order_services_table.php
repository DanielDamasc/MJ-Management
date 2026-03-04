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
        Schema::table('order_services', function (Blueprint $table) {
            $table->date('data_servico')->nullable()->change();
            $table->unsignedBigInteger('executor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_services', function (Blueprint $table) {
            $table->date('data_servico')->nullable(false)->change();
            $table->unsignedBigInteger('executor_id')->nullable(false)->change();
        });
    }
};
