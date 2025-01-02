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
        Schema::create('sweetnesses', function (Blueprint $table) {
            $table->id()->comment('기본키');
            $table->string('fruit_name')->comment('과일 이름');
            $table->unsignedInteger('sweetness')->comment('당도');
            $table->unsignedInteger('standard_sweetness')->comment('기준당도');
            $table->boolean('is_display')->comment('노출여부');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sweetnesses');
    }
};
