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
        Schema::create('codes', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->unsignedInteger('parent_id')->nullable()->default(null);
            $table->unsignedSmallInteger('left_id')->default(0);
            $table->unsignedSmallInteger('right_id')->default(0);
            $table->unsignedTinyInteger('order')->default(0);
            $table->string('name', 100)->default('');
            $table->boolean('is_use')->default(true);
            $table->boolean('is_display')->default(true);
            //$table->text('description')->nullable();
            $table->timestamps(); // 생성 및 업데이트 시간
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
