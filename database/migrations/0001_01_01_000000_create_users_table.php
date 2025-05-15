<?php

use App\Enums\UserLevel;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('username')->unique()->comment('아이디');
            $table->string('name')->comment('성명');
            $table->string('email')/*->unique()*/->comment('이메일');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('비밀번호');
            $table->rememberToken();

            $table->string('phone', 15)/*->unique()*/->nullable()->comment('연락처');
            $table->string('postal_code', 10)->nullable()->comment('우편번호');
            $table->string('address')->nullable()->comment('주소');
            $table->string('address_detail')->nullable()->comment('상세주소');

            $table->string('nickname')->nullable()->comment('닉네임');
            $table->unsignedInteger('points')->default(0)->comment('포인트 ');
            $table->enum('level', UserLevel::values())->default(UserLevel::GENERAL->value)->comment('등급');
            $table->boolean('is_agree_promotion')->default(false)->comment('광고성 정보 수신 동의');

            $table->string('social_id')->nullable()->comment('소셜 로그인 ID');
            $table->string('social_platform')->nullable()->comment('소셜 로그인 플랫폼');

            $table->boolean('is_admin')->default(false)->comment('관리자 여부 필드');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
