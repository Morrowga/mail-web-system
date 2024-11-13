<?php

namespace App\Events;

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

    public $mailId;
    public $newStatus;

    public function __construct(MailLog $mailLog, string $newStatus)
    {
        $this->mailId = $mailLog->id;
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
        return [
            'mail_id' => $this->mailId,
            'new_status' => $this->newStatus,
        ];
    }
}
