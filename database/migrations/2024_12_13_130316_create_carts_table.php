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
        Schema::create('carts', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->comment('사용자 외래키');
            $table->unsignedBigInteger('guest_id')->nullable()->comment('비회원 아이디');
            $table->foreignId('product_id')->constrained()->onDelete('cascade')->comment('상품 외래키');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cart_product_options', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('cart_id')->constrained()->onDelete('cascade')->comment('카트 외래키');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->comment('사용자 외래키');
            $table->unsignedBigInteger('guest_id')->nullable()->comment('비회원 아이디');

            $table->foreignId('product_option_id')->constrained()->onDelete('cascade')->comment('상품 옵션 외래키');
            $table->unsignedInteger('price')->comment('가격');
            $table->unsignedInteger('quantity')->default(1)->comment('수량');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_product_options');
        Schema::dropIfExists('carts');
    }
};
