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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)->comment('포인트 ');
            $table->enum('level', UserLevel::values())->default(UserLevel::GENERAL->value)->comment('등급');
            $table->boolean('is_admin')->default(false)->after('points')->comment('관리자 여부 필드');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
