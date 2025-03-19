<?php

namespace App\Console\Commands;

use App\Enums\IamportMethod;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class DestroyIncompletePaymentOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:destroy-incomplete-payment-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '결제미완료 주문 삭제';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //order vbank_date
        /**
         * 결제미완료 주문 삭제
         *  가상계좌 만료일이 있어 만료일 이후 주문건은 스케줄러에서 처리해야 함
         *  가상계좌 만료 self::ORDER_COMPLETE => '주문완료'건 리스트에서 빼야 하나?
         *  OrderStatus::ORDER_PENDING, OrderStatus::ORDER_COMPLETE 삭제 크론 필요??
         */
        $orders = Order::where('status', OrderStatus::ORDER_PENDING->value)
            ->orWhere(function ($query) {
                $query->where('pay_method_method', IamportMethod::VBANK->value)
                    ->where('status', OrderStatus::ORDER_COMPLETE)->where('vbank_date', '<', now());
            })
            ->orWhere(function ($query) {
                $query->where('pay_method_method', IamportMethod::CARD->value)
                    ->where('status', OrderStatus::ORDER_COMPLETE);
            })->get();

        //TODO LOGGING

        $orders->delete();
    }
}
