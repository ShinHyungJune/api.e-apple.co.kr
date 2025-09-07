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
        Schema::table('products', function (Blueprint $table) {
            $table->string('return_postal_code', 10)->default('50147')->after('delivery_fee');
            $table->string('return_address')->default('경남 거창군 거창읍 거함대로 3372')->after('return_postal_code');
            $table->string('return_address_detail')->default('서북부경남거점산지유통센터(APC)')->after('return_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['return_postal_code', 'return_address', 'return_address_detail']);
        });
    }
};
