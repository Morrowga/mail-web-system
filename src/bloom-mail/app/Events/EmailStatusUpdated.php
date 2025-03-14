<?php

namespace App\Events;

use App\Models\Folder;
use App\Models\MailLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $broadcastQueue = 'mail-status';

    public $mailId;
    public $person_in_charge;
    public $newStatus;

    public function __construct(MailLog $mailLog, $newStatus = 'new')
    {
        $this->mailId = $mailLog->id;
        $this->person_in_charge = $mailLog->person_in_charge;
        $this->newStatus = $newStatus;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('mail-status'),
        ];
    }

    public function broadcastAs()
    {
        return 'mail-status-changed';
    }

    public function broadcastWith()
    {
        $countData =  MailLog::where('status', 'new')
        ->doesntHave('folders')
        ->count();

        $folders = Folder::withCount(['mails' => function ($query) {
            $query->where('status', 'new');
        }])->get();

        return [
            'mail_id' => $this->mailId,
            'new_status' => $this->newStatus,
            'person_in_charge' => $this->person_in_charge,
            'count_data' => $countData,
            'folders_data' => $folders
        ];
    }
}
