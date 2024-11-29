<?php

use App\Models\MailLog;
use App\Jobs\ProcessMails;
use App\Repositories\MailRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Artisan::command('mail-fetching', function () {
    ProcessMails::dispatch();
})->purpose('Running Realtime')->everyTwentySeconds();


Artisan::command('trash-deletion', function () {
    $mailRepository = app(MailRepository::class);

    $mails = MailLog::where('status', 'deleted')->get();

    foreach($mails  as $mail)
    {
        $emails = $mailRepository->deleteForeverProcess($mail);
    }
})->purpose('Running Trash Deletion')->twiceMonthly(1, 16, '13:00');


