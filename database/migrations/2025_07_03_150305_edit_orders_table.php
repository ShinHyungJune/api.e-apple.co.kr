<?php

use App\Enums\OrderStatus as OrderStatusAlias;
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
            $table->string('common_entrance_password')
                ->nullable()
                ->after('common_entrance_method')
                ->comment('공동현관 출입 비밀번호');
            $table->enum('status', OrderStatusAlias::values())
                ->default(OrderStatusAlias::ORDER_PENDING->value)
                ->comment('주문 상태')
                ->change();
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->enum('status', OrderStatusAlias::values())
                ->default(OrderStatusAlias::ORDER_PENDING->value)
                ->comment('주문 상태')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
