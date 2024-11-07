<?php

namespace App\Jobs;

use App\Events\TakingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Repositories\MailRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessMails implements ShouldQueue
{
    use Dispatchable, Queueable;  // Only these two traits

    public function __construct()
    {
    }

    public function handle()
    {
        $mailRepository = app(MailRepository::class);

        $emails = $mailRepository->inbox();

        broadcast(new TakingMail($emails));

        ProcessMails::dispatch()->delay(now()->addSeconds((int) env('JOB_INTERVAL_TIME')));

        Log::info('worked');
    }

}
