<?php

use App\Models\Coupon;
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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('name')->comment('쿠폰 이름');

            $table->enum('type', Coupon::TYPES)->comment('할인 타입 ex) amount: 할인액, rate: 할인율');

            $table->unsignedInteger('discount_amount')->nullable()->comment('할인 금액');
            $table->unsignedInteger('minimum_purchase_amount')->nullable()->comment('최소 결제액');

            $table->unsignedTinyInteger('discount_rate')->nullable()->comment('할인율');
            $table->unsignedInteger('usage_limit_amount')->nullable()->comment('사용한도 금액');

            $table->unsignedInteger('valid_days')->nullable()->comment('발급일로부터 유효한 일수');
            $table->timestamp('issued_until')->nullable()->comment('쿠폰 발급 종료 일시');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
