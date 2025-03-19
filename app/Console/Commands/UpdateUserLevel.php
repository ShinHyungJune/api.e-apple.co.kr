<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Enums\UserLevel;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUserLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:user-level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '사용자 포인트 조정';

    /**
     * Execute the console command.
     *
     * 사용자 등급
     *   일반  : 이전달       0원 이상 구매, 1%적립
     *   FAMILY: 이전달 100,000원 이상 구매, 2%적립
     *   VI P  : 이전달 200,000원 이상 구매, 3%적립
     *
     * 매월 1일 자정에 실행
     */
    public function handle()
    {
        $formattedLastMonth = Carbon::now()->subMonth()->format('Y-m');
        $userLevels = Order::selectRaw("
                MAX(user_id) user_id,
                CASE
                    WHEN SUM(price - refund_amount_sum) > 200000 THEN '" . UserLevel::VIP->value . "'
                    WHEN SUM(price - refund_amount_sum) > 100000 THEN '" . UserLevel::FAMILY->value . "'
                    ELSE '" . UserLevel::GENERAL->value . "'
                END AS level
            ")
            ->where('status', OrderStatus::PURCHASE_CONFIRM)
            ->where('purchase_confirmed_at', 'LIKE', $formattedLastMonth . '%')
            ->whereNotNull('user_id')->groupBy('user_id')
            ->get()
            ->groupBy('level');
        foreach ($userLevels as $key => $userLevel) {
            $userIds = $userLevel->pluck('user_id');
            //TODO LOGGING
            echo('key=>' . $key . ':user_ids=>' . $userIds->implode(', ') . PHP_EOL);
            User::whereIn('id', $userIds)->update(['level' => $key]);
        }
    }
}
