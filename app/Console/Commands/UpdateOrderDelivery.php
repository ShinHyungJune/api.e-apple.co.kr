<?php

namespace App\Console\Commands;

use App\Enums\DeliveryCompany;
use App\Enums\OrderStatus;
use App\Models\OrderProduct;
use Illuminate\Console\Command;

class UpdateOrderDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-order-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * 배송중인 상품
         */
        $orderProducts = OrderProduct::where('status', OrderStatus::DELIVERY)->get();
        foreach ($orderProducts as $orderProduct) {

            if (DeliveryCompany::from($orderProduct->delivery_company)
                ->isDelivered($orderProduct->delivery_tracking_number)) {

                $orderProduct->status = OrderStatus::DELIVERY_COMPLETE;
                $orderProduct->save();

            }
        }
    }
}
