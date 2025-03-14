<?php

use Carbon\Carbon;
use App\Models\MailLog;
use App\Jobs\ProcessMails;
use App\Repositories\MailRepository;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('mail-fetching', function () {
    Log::info('Dispatched ProcessMails');
    ProcessMails::dispatch()->onQueue('mail-fetching');
})->purpose('Running Realtime')->everyMinute();

Artisan::command('trash-deletion', function () {
    $mailRepository = app(MailRepository::class);

    $twoWeeksAgo = Carbon::now()->subWeeks(2);

    $mails = MailLog::where('status', 'deleted')
        ->where('deleted_at', '<=', $twoWeeksAgo)
        ->get();

    foreach ($mails as $mail) {
        $mailRepository->deleteForeverProcess($mail);
    }
})->purpose('Running Trash Deletion')->hourly();

