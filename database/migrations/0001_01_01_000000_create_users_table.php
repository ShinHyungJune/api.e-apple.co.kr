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
            $table->string('name')->comment('성명');
            $table->string('email')->unique()->comment('이메일');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->comment('비밀번호');
            $table->rememberToken();
            $table->string('phone', 15)/*->unique()*/->comment('연락처');
            $table->string('nickname')->nullable()->comment('닉네임');
            $table->unsignedInteger('points')->default(0)->comment('포인트 ');
            $table->enum('level', UserLevel::values())->default(UserLevel::GENERAL->value)->comment('등급');
            $table->boolean('is_admin')->default(false)->comment('관리자 여부 필드');
            $table->timestamps();
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
