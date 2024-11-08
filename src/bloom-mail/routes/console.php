<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Artisan::command('mail-fetching', function () {
    $mailRepository = app(MailRepository::class);

    $emails = $mailRepository->inbox();

    broadcast(new TakingMail($emails));
})->purpose('Running Realtime')->hourly();

