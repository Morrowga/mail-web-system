<?php

namespace App\Interfaces;

use App\Models\MailLog;
use App\Models\SentMail;
use Illuminate\Http\Request;

interface MailRepositoryInterface
{
    public function inbox();

    public function newMessage();

    public function reply(Request $request, MailLog $mail_log);

    public function forward(Request $request, MailLog $mail_log);

    public function markAsRead($id);

    public function getHistories($id);

    public function store(Request $request);

    public function deleteForever(MailLog $mailLog);

    public function deleteSentMail(SentMail $sent_mail);
}
