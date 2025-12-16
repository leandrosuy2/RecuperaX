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
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->string('picpay_reference_id')->nullable()->unique()->after('numero_transacao')->comment('ID de referência do pagamento no PicPay');
            $table->string('picpay_authorization_id')->nullable()->after('picpay_reference_id')->comment('ID de autorização do pagamento no PicPay');
            $table->text('picpay_payment_url')->nullable()->after('picpay_authorization_id')->comment('URL de pagamento gerada pelo PicPay');
            $table->text('picpay_qrcode_base64')->nullable()->after('picpay_payment_url')->comment('QR Code em base64 do PicPay');
            $table->json('picpay_response')->nullable()->after('picpay_qrcode_base64')->comment('Resposta completa da API do PicPay');
            $table->timestamp('picpay_expires_at')->nullable()->after('picpay_response')->comment('Data de expiração do pagamento PicPay');
            $table->index('picpay_reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropIndex(['picpay_reference_id']);
            $table->dropColumn([
                'picpay_reference_id',
                'picpay_authorization_id',
                'picpay_payment_url',
                'picpay_qrcode_base64',
                'picpay_response',
                'picpay_expires_at',
            ]);
        });
    }
};
