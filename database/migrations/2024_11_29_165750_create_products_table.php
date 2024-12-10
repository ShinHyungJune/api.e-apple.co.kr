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
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('기본키');

            $table->string('name')->comment('상품명');
            $table->text('description')->nullable()->comment('상품설명');
            $table->unsignedInteger('view_count')->default(0)->comment('조회수');

            $table->unsignedInteger('price')->comment('가격');
            $table->unsignedInteger('original_price')->nullable()->comment('원래 가격');
            $table->unsignedInteger('delivery_fee')->default(0)->comment('배송비 (기본값: 0)');
            $table->unsignedInteger('stock_quantity')->comment('재고 수량');

            $table->json('categories')->nullable()->comment('카테고리');
            $table->boolean('is_md_suggestion_gift')->default(false)->comment('MD 추천 선물');
            $table->json('tags')->nullable()->comment('태그(ex: 실시간 인기, 클래식 과일, 어른을 위한 픽, 추가 증정)');

            $table->string('food_type')->nullable()->comment('식품의 유형');
            $table->string('fruit_size')->nullable()->comment('과일 크기');
            $table->string('sugar_content')->nullable()->comment('당도');

            $table->string('shipping_origin')->nullable()->comment('출고지');
            $table->string('manufacturer_and_location')->nullable()->comment('생산자 및 소재지');
            $table->string('importer')->nullable()->comment('수입자');
            $table->string('origin')->nullable()->comment('원산지');
            $table->text('ingredients_and_composition')->nullable()->comment('원재료 및 합량');
            $table->string('storage_and_handling')->nullable()->comment('보관/취급방법');

            $table->date('manufacture_date')->nullable()->comment('제조연원일');
            $table->date('expiration_date')->nullable()->comment('유통기한');

            $table->string('gmo_desc')->nullable()->comment('유전자변형 농산물 여부');
            $table->string('customer_service_contact')->nullable()->comment('소비자 상담 문의');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
