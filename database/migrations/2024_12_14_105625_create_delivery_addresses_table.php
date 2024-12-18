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
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->comment('사용자 외래키');

            $table->string('name')->comment('배송지명');
            $table->string('recipient_name')->comment('배송받을 사람 이름');
            $table->string('phone', 20)->comment('연락처');
            $table->string('postal_code', 10)->comment('우편번호');
            $table->string('address')->comment('주소');
            $table->string('address_detail')->nullable()->comment('상세주소');
            $table->text('delivery_request')->nullable()->comment('배송 요청 사항');

            $table->boolean('is_default')->default(false)->comment('기본 배송지 여부');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
