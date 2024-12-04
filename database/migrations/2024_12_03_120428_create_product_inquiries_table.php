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
        Schema::create('product_inquiries', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('product_id')
                //->constrained()->onDelete('cascade')
                ->comment('참조 상품 ID');
            $table->foreignId('user_id')
                //->constrained()->onDelete('cascade')
                ->comment('문의 작성 사용자 ID');
            $table->string('title')->comment('문의 제목'); // 문의 제목
            $table->text('content')->comment('문의 내용'); // 문의 내용
            $table->boolean('is_visible')->default(true)->comment('공개 여부 (0: 비공개, 1: 공개)'); // 공개 여부
            $table->text('answer')->nullable()->comment('관리자가 작성한 답변 내용'); // 답변 내용
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inquiries');
    }
};
