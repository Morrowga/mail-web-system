<?php

namespace App\Http\Controllers\Axio;

use App\Models\Folder;
use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Events\EmailStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\MailRepositoryInterface;

class MailController extends Controller
{
    private MailRepositoryInterface $mailRepository;

    public function __construct(MailRepositoryInterface $mailRepository)
    {
        $this->mailRepository = $mailRepository;
    }

    public function index(Request $request)
    {
        $filter = [
            "status" => $request->query('status'),
            "from" => $request->query('from'),
            "to" => $request->query('to'),
            "keyword" => $request->query('keyword'),
            "person_in_charge" => $request->query('person_in_charge'),
            "folder_id" => $request->query('folder_id')
        ];

        $mails = $this->mailRepository->inbox($filter);

        return response()->json($mails);
    }

    public function indexWithFolderId(Folder $folder, Request $request)
    {
        $filter = [
            "status" => $request->query('status'),
            "from" => $request->query('from'),
            "to" => $request->query('to'),
            "keyword" => $request->query('keyword'),
            "person_in_charge" => $request->query('person_in_charge'),
            "folder_id" => $request->query('folder_id')
        ];

        $mails = $this->mailRepository->inboxWithFolderId($folder, $filter);

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

    public function changeReply(Request $request, MailLog $mail_Log)
    {
        $mail_Log->update([
            'previous_status' => $mail_Log->status,
            'person_in_charge' => Auth::user()->name,
            'status' => 'replying'
        ]);

        broadcast(new EmailStatusUpdated($mail_Log, 'replying'));

        return response()->json([
            "message" => "success",
            "status" => "replying"
        ]);
    }

    public function changeStatus(Request $request, MailLog $mail_Log)
    {
        $mail_Log->update([
            'status' => $request->status,
            'person_in_charge' => Auth::user()->name
        ]);

        broadcast(new EmailStatusUpdated($mail_Log, $request->status));

        return response()->json([
            "message" => "success",
            "status" => $request->status
        ]);
    }

    public function cancelReply(MailLog $mail_Log)
    {
        $previousStatus = $mail_Log->previous_status;

        $mail_Log->update([
            'status' => $previousStatus ? ($previousStatus == 'replying' ? 'new' : $previousStatus) : 'new',
            'previous_status' => null,
        ]);

        broadcast(new EmailStatusUpdated($mail_Log, $previousStatus ? ($previousStatus == 'replying' ? 'new' : $previousStatus) : 'new'));

        return response()->json([
            "message" => "success",
            "status" => $previousStatus ? ($previousStatus == 'replying' ? 'new' : $previousStatus) : 'new'
        ]);
    }

    public function folderMatching()
    {
        $mails = $this->mailRepository->singleFolderMatching();
    }

    public function folderSwitch(Request $request)
    {
        $mails = $this->mailRepository->folderSwitch($request);

        return $mails;
    }
}
