<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class CompleteOrderPayment extends Command
{
    protected $signature = 'order:complete {order_id} {imp_uid}';

    protected $description = '주문 결제완료 수동 처리 (웹훅 실패 시 사용)';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $impUid = $this->argument('imp_uid');

        $order = Order::find($orderId);

        if (!$order) {
            $this->error("주문을 찾을 수 없습니다: {$orderId}");
            return 1;
        }

        $this->info("주문 정보:");
        $this->info("- 주문번호: {$order->merchant_uid}");
        $this->info("- 구매자: {$order->buyer_name}");
        $this->info("- 결제금액: {$order->price}원");
        $this->info("- 현재상태: {$order->status->value}");

        if ($order->status !== OrderStatus::ORDER_COMPLETE) {
            $this->error("ORDER_COMPLETE 상태가 아닙니다. 현재 상태: {$order->status->value}");
            if (!$this->confirm('그래도 진행하시겠습니까?')) {
                return 1;
            }
        }

        if ($order->imp_uid) {
            $this->error("이미 imp_uid가 있습니다: {$order->imp_uid}");
            if (!$this->confirm('덮어쓰시겠습니까?')) {
                return 1;
            }
        }

        if (!$this->confirm("결제완료 처리하시겠습니까?")) {
            $this->info("취소되었습니다.");
            return 0;
        }

        try {
            $order->complete([
                'imp_uid' => $impUid,
                'status' => OrderStatus::PAYMENT_COMPLETE,
                'payment_completed_at' => now(),
            ]);

            $this->info("결제완료 처리되었습니다!");
            $this->info("- imp_uid: {$impUid}");
            $this->info("- 상태: payment_complete");

            return 0;
        } catch (\Exception $e) {
            $this->error("처리 중 오류 발생: {$e->getMessage()}");
            return 1;
        }
    }
}
