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
        Schema::create('points', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('사용자 외래키');
            //$table->morphs('pointable')->nullable();//nullable 실행 안됨
            $table->string('pointable_type')->nullable();
            $table->unsignedBigInteger('pointable_id')->nullable();
            $table->unsignedInteger('deposit')->default(0)->comment('적립액');//입금
            $table->unsignedInteger('withdrawal')->default(0)->comment('사용액');//출금
            $table->unsignedInteger('balance')->default(0)->comment('잔액');//잔액
            $table->text('description')->nullable()->comment('설명');
            $table->timestamp('expired_at')->nullable()->comment('소멸일시');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
