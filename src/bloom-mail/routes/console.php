<?php

use App\Models\MailLog;
use App\Jobs\ProcessMails;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Artisan::command('mail-fetching', function () {
    ProcessMails::dispatch();
})->purpose('Running Realtime')->everyTwentySeconds();


Artisan::command('trash-deletion', function () {
    MailLog::where('status', 'deleted')->delete();
})->purpose('Running Trash Deletion')->twiceMonthly(1, 16, '13:00');


