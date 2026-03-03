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
        Schema::table('clients', function (Blueprint $table) {

            $table->dropUnique('clients_cnpj_unique'); // Para não falhar no sqlite.

            // Apaga o que era exclusivo de empresa.
            $table->dropColumn(['cnpj', 'razao_social']);

            // Adiciona campos unificados.
            $table->char('tipo_pessoa', 1)->nullable()->after('id');
            $table->string('documento', 20)->nullable()->after('tipo_pessoa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['tipo_pessoa', 'documento']);

            $table->string('cnpj', 14)->unique()->nullable();
            $table->unique('cnpj', 'clients_cnpj_unique');
            
            $table->string('razao_social')->nullable();
        });
    }
};
