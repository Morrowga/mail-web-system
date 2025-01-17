<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\MailLog;
use App\Models\SentMail;
use App\Jobs\ProcessMails;
use Illuminate\Http\Request;
use App\Http\Requests\MailRequest;
use App\Http\Requests\ReplyForwardRequest;
use App\Interfaces\MailRepositoryInterface;
use App\Interfaces\FolderRepositoryInterface;
use App\Interfaces\TemplateRepositoryInterface;

class MailController extends Controller
{
    private MailRepositoryInterface $mailRepository;
    private TemplateRepositoryInterface $templateRepository;
    private FolderRepositoryInterface $folderRepository;

    public function __construct(
        MailRepositoryInterface $mailRepository,
        TemplateRepositoryInterface $templateRepository,
        FolderRepositoryInterface $folderRepository
    )
    {
        $this->mailRepository = $mailRepository;
        $this->templateRepository = $templateRepository;
        $this->folderRepository = $folderRepository;
    }

    public function index()
    {
        $templates = $this->templateRepository->getOnlyTemplates();
        $folders = $this->folderRepository->getOnlyFolders();

        // return $this->mailRepository->newMessage();

        $person_in_charges = User::get();

        $from = env('IMAP_USERNAME');

        return Inertia::render('Inbox', [
            "templates" => $templates['data'],
            "from" => $from,
            "folders" => $folders['data'],
            "person_in_charges" => $person_in_charges
        ]);
    }

    public function replyForward(ReplyForwardRequest $request, MailLog $mail_log)
    {
        $sendRequest = $request->type == 'reply' ?  $this->mailRepository->reply($request, $mail_log) :  $this->mailRepository->forward($request, $mail_log);

        return redirect()->route('inbox');
    }

    public function store(MailRequest $request)
    {
        $createMail = $this->mailRepository->store($request);

        return redirect()->route('inbox');
    }

    public function destroy(MailLog $mail_log)
    {
        $this->mailRepository->delete($mail_log);

        return redirect()->route('inbox');
    }

    public function redo(MailLog $mail_log)
    {
        $this->mailRepository->redo($mail_log);

        return redirect()->route('inbox');
    }


    public function destroyForever(MailLog $mail_log)
    {
        $this->mailRepository->deleteForever($mail_log);

        return redirect()->route('inbox');
    }


    public function sentDestroy(SentMail $sent_mail)
    {
        $createMail = $this->mailRepository->deleteSentMail($sent_mail);

        return redirect()->route('inbox');
    }
}
