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
        Schema::create('md_product_packages', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('title')->comment('패키지 제목');
            $table->text('description')->comment('패키지 내용');
            //$table->unsignedInteger('price')->comment('패키지 가격');
            $table->timestamps();
        });
        Schema::create('md_product_package_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('md_product_package_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1); // 패키지 내 상품 수량
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('md_product_package_product');
        Schema::dropIfExists('md_product_packages');
    }
};
