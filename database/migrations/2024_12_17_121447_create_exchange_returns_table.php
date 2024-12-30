<?php

use App\Enums\ExchangeReturnStatus;
use App\Models\ExchangeReturn;
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
        Schema::create('exchange_returns', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->index()->comment('사용자 외래키');
            $table->string('guest_id')->nullable()->comment('비회원 아이디');

            $table->foreignId('order_id')->index()->comment('주문 기본키');
            $table->foreignId('order_product_id')->index()->comment('주문 상품 기본키');
            //$table->unique('order_id', 'order_product_id'); // 유니크 인덱스 이름 지정
            $table->enum('type', ExchangeReturn::TYPES)->comment('요청 유형: exchange(교환) 또는 return(반품)');

            $table->string('problem')->nullable()->comment('단순변심, 배송문제, 상품문제');
            /*$table->string('change_of_mind')->nullable()->comment('단순변심 ex) 상품이 마음에 들지 않음, 더 저렴한 상품을 발견함');
            $table->string('delivery_issue')->nullable()->comment('배송문제 ex) 다른 상품이 배송됨, 배송된 장소에 박스가 분실됨, 다른 주소로 배송됨');
            $table->string('product_issue')->nullable()->comment('상품문제 ex) 상품의 구성품/부속품이 들어있지 않음, 상품이 설명과 다름, 상품이 파손되어 배송됨, 상품 결함/기능에 이상이 있음');*/

            $table->text('description')->nullable()->comment('상세설명');
            $table->enum('status', ExchangeReturnStatus::values())->default(ExchangeReturnStatus::RECEIVED->value)->comment("처리상태");
            $table->text('admin_notes')->nullable()->comment('관리자 메모');

            $table->timestamp('processed_at')->nullable()->comment('처리 완료 시간');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_returns');
    }
};
