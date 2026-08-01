<?php

use App\Console\Commands\ExpireStaleOrdersCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Smart Dispenser Scheduled Tasks
|--------------------------------------------------------------------------
|
| Runs every minute to catch orders whose QRIS window has lapsed
| without a webhook ever arriving (user abandoned the payment).
|
*/
Schedule::command(ExpireStaleOrdersCommand::class)->everyMinute()->withoutOverlapping();
