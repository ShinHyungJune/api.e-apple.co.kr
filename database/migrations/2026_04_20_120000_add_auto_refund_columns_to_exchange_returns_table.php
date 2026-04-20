<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exchange_returns', function (Blueprint $table) {
            $table->timestamp('auto_refund_succeeded_at')->nullable()->after('processed_at')
                ->comment('아임포트 자동환불 성공 시간');
            $table->text('auto_refund_error')->nullable()->after('auto_refund_succeeded_at')
                ->comment('아임포트 자동환불 실패 메시지(있으면 수동 계좌이체 폴백)');
        });
    }

    public function down(): void
    {
        Schema::table('exchange_returns', function (Blueprint $table) {
            $table->dropColumn(['auto_refund_succeeded_at', 'auto_refund_error']);
        });
    }
};
