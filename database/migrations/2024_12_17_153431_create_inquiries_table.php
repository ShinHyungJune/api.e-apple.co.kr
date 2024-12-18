<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->index()->constrained()->onDelete('cascade')->comment('사용자 외래키');
            $table->string('purchase_related_inquiry')->nullable()->comment('구매관련문의 ex) 배송문의, 주문문의, 취소문의, 교환문의, 환불문의, 입금문의');
            $table->string('general_consultation_inquiry')->nullable()->comment('일반상담문의 ex) 회원정보, 결제문의, 상품문의, 쿠폰/마일리지, 기타');
            $table->text('content')->comment('문의 내용');
            $table->text('answer')->nullable()->comment('관리자 답변');
            $table->timestamp('answered_at')->nullable()->comment('답변 일시');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
