<?php

namespace App\Console\Commands;

use App\Models\Point;
use App\Models\ProductReview;
use Illuminate\Console\Command;

class ExpirePoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '리뷰 포인트 만료';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationDate = now()->subDays(Point::EXPIRATION_DAYS);
        //$expirationDate = Carbon::parse('2024-12-21 00:00:00')->subDays(Point::EXPIRATION_DAYS);
        $this->info($expirationDate);

        $expiredPoints = Point::whereNull('expired_at')
            ->where('pointable_type', ProductReview::class)
            ->where('deposit', '>', 0)
            ->where('created_at', '<', $expirationDate)
            //->get();
            ->update(['expired_at' => now()]);

        $this->info($expiredPoints);

        $this->info("적립금 소멸 처리가 완료되었습니다.");
        return Command::SUCCESS;
    }
}
