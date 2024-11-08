<?php

use App\Jobs\ProcessMails;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Artisan::command('mail-fetching', function () {
    ProcessMails::dispatch();
})->purpose('Running Realtime')->everyFifteenSeconds();

