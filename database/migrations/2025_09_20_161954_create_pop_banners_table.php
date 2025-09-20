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
        Schema::create('pop_banners', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('title')->comment('제목');
            $table->string('url')->nullable()->comment('링크 URL');
            $table->timestamp('started_at')->comment('노출 시작일');
            $table->timestamp('finished_at')->comment('노출 종료일');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->integer('sort_order')->default(0)->comment('정렬 순서');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pop_banners');
    }
};
