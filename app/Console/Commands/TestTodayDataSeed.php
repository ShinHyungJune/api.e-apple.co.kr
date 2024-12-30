<?php

namespace App\Console\Commands;

use Database\Seeders\ProductReviewSeeder;
use Database\Seeders\SweetnessSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TestTodayDataSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:today-data-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'review, sweetness 테스트 데이터 seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seeders = [SweetnessSeeder::class, ProductReviewSeeder::class];
        foreach ($seeders as $seeder) {
            Artisan::call('db:seed', ['--class' => $seeder]);
        }
    }
}
