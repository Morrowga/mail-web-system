<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Jobs\ProcessMails;
use Illuminate\Http\Request;
use App\Http\Requests\MailRequest;
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
        $mails = $this->mailRepository->inbox();

        $templates = $this->templateRepository->getOnlyTemplates();

        $from = env('IMAP_USERNAME');

        return Inertia::render('Dashboard', [
            "mails" => $mails,
            "templates" => $templates['data'],
            "from" => $from
        ]);
    }

    public function store(MailRequest $request)
    {
        $createMail = $this->mailRepository->store($request);

        return redirect()->route('dashboard');
    }
}
