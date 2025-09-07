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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->comment('취소사유')->after('memo');
        });
        
        Schema::table('order_products', function (Blueprint $table) {
            $table->text('cancel_reason')->nullable()->comment('취소사유')->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cancel_reason');
        });
        
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('cancel_reason');
        });
    }
};
