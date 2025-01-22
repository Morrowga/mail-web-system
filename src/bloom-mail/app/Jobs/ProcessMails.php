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
    use Dispatchable, Queueable;

    public $broadcastQueue = 'mail-fetching';

    // public $timeout = 120;
    // public $tries = 3;
    // public $backoff = [30, 60, 120];

    public function __construct()
    {
    }

    public function handle()
    {
        Log::info('ProcessMails job started');

        $mailRepository = app(MailRepository::class);

        $emails = $mailRepository->newMessage();

        Log::info('ProcessMails job ended');
    }

}
