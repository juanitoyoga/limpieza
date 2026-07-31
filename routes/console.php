<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;


use App\Console\Commands\VencerNotificacionesExpiradas;
use App\Console\Commands\PagoMultaCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(VencerNotificacionesExpiradas::class)->hourly();

Schedule::command(PagoMultaCommand::class)
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->onOneServer();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
