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
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('사용자 외래키');
            $table->foreignId('coupon_id')/*->constrained()->onDelete('cascade')*/->comment('쿠폰 외래키');

            $table->timestamp('issued_at')->comment('발급일시');
            $table->timestamp('expired_at')->nullable()->comment('만료일시');

            $table->foreignId('order_id')/*->constrained()->onDelete('cascade')*/ ->nullable()->comment('주문 외래키');
            $table->timestamp('used_at')->nullable()->comment('쿠폰 사용일시');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};
