<?php

namespace App\Http\Controllers\Axio;

use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Events\EmailStatusUpdated;
use App\Http\Controllers\Controller;
use App\Interfaces\MailRepositoryInterface;

class MailController extends Controller
{
    private MailRepositoryInterface $mailRepository;

    public function __construct(MailRepositoryInterface $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    public function index()
    {
        $mails = $this->mailRepository->inbox();

        return response()->json($mails);
    }

    public function markAsRead(Request $request,$id)
    {
        $response = $this->mailRepository->markAsRead($id);

        return $response;
    }

    public function getHistories(Request $request,$id)
    {
        $response = $this->mailRepository->getHistories($id);

        return $response;
    }

    public function changeStatus(Request $request, MailLog $mail_Log)
    {
        $mail_Log->update([
            'previous_status' => $mail_Log->status,
            'status' => 'replying'
        ]);

        broadcast(new EmailStatusUpdated($mail_Log, 'replying'));

        return response()->json([
            "message" => "success"
        ]);
    }

    public function cancelReply(MailLog $mail_Log)
    {
        $previousStatus = $mail_Log->previous_status;

        $mail_Log->update([
            'status' => $previousStatus,
            'previous_status' => null,
        ]);

        broadcast(new EmailStatusUpdated($mail_Log, $previousStatus));

        return response()->json([
            "message" => "success"
        ]);
    }
}
