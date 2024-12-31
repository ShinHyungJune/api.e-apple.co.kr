<?php

use App\Console\Commands\TestTodayDataSeed;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


// 개발에서만 실행
Schedule::command(TestTodayDataSeed::class)
    ->withoutOverlapping()
    ->daily()//자정에 실행
    //->environments(['local'])
;


/*Schedule::command(ExpirePoints::class)
    ->withoutOverlapping()
    //->everySecond();
    ->daily()//자정에 실행
;*/


