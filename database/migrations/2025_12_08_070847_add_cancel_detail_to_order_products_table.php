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
        Schema::table('order_products', function (Blueprint $table) {
            $table->text('cancel_detail')->nullable()->comment('취소 상세 내역 (환불 계산 과정)');
            $table->integer('refund_amount')->default(0)->comment('개별 상품 환불 금액');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn(['cancel_detail', 'refund_amount']);
        });
    }
};
