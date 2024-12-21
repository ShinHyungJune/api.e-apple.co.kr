<?php

use App\Console\Commands\ExpirePoints;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::command(ExpirePoints::class)
    ->withoutOverlapping()
    //->everySecond();
    ->daily();//자정에 실행

