<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MailLog;
use App\Models\SentMail;
use App\Jobs\ProcessMails;
use Illuminate\Http\Request;
use App\Http\Requests\MailRequest;
use App\Http\Requests\ReplyForwardRequest;
use App\Interfaces\MailRepositoryInterface;
use App\Interfaces\TemplateRepositoryInterface;

class MailController extends Controller
{
    private MailRepositoryInterface $mailRepository;
    private TemplateRepositoryInterface $templateRepository;

    public function __construct(MailRepositoryInterface $mailRepository, TemplateRepositoryInterface $templateRepository)
    {
        $this->mailRepository = $mailRepository;
        $this->templateRepository = $templateRepository;
    }

    public function index()
    {
        $templates = $this->templateRepository->getOnlyTemplates();

        $from = env('IMAP_USERNAME');

        return Inertia::render('Dashboard', [
            "templates" => $templates['data'],
            "from" => $from
        ]);
    }

    public function replyForward(ReplyForwardRequest $request, MailLog $mail_log)
    {
        $sendRequest = $request->type == 'reply' ?  $this->mailRepository->reply($request, $mail_log) :  $this->mailRepository->forward($request, $mail_log);

        return redirect()->route('dashboard');
    }

    public function store(MailRequest $request)
    {
        $createMail = $this->mailRepository->store($request);

        return redirect()->route('dashboard');
    }

    public function destroy(MailLog $mail_log)
    {
        $createMail = $this->mailRepository->deleteForever($mail_log);

        return redirect()->route('dashboard');
    }

    public function sentDestroy(SentMail $sent_mail)
    {
        $createMail = $this->mailRepository->deleteSentMail($sent_mail);

        return redirect()->route('dashboard');
    }
}
