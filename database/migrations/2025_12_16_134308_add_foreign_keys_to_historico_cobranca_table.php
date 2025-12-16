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
        Schema::table('historico_cobranca', function (Blueprint $table) {
            $table->foreign('acordo_id')->references('id')->on('acordos')->onDelete('set null');
            $table->foreign('pagamento_id')->references('id')->on('pagamentos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historico_cobranca', function (Blueprint $table) {
            $table->dropForeign(['acordo_id']);
            $table->dropForeign(['pagamento_id']);
        });
    }
};
