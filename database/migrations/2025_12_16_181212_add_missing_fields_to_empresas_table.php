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
        Schema::table('core_empresa', function (Blueprint $table) {
            $table->string('agencia', 20)->nullable()->after('banco');
            $table->string('conta', 50)->nullable()->after('agencia');
            $table->string('chave_pix', 255)->nullable()->after('nome_favorecido_pix');
            $table->integer('qtd_parcelas')->nullable()->after('chave_pix');
            $table->decimal('desconto_total_avista', 5, 2)->nullable()->after('qtd_parcelas');
            $table->decimal('desconto_total_aprazo', 5, 2)->nullable()->after('desconto_total_avista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_empresa', function (Blueprint $table) {
            $table->dropColumn([
                'agencia',
                'conta',
                'chave_pix',
                'qtd_parcelas',
                'desconto_total_avista',
                'desconto_total_aprazo'
            ]);
        });
    }
};
