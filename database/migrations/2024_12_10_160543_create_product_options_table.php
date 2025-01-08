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
        Schema::create('product_options', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('product_id')
                //->constrained()->onDelete('cascade')
                ->comment('참조 상품 ID');
            $table->string('name')->comment('옵션명');
            $table->unsignedInteger('price')->comment('가격');
            $table->unsignedInteger('original_price')->nullable()->comment('원래 가격');
            $table->unsignedInteger('stock_quantity')->comment('재고 수량');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};
