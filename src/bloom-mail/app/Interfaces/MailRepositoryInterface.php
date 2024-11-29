<?php

namespace App\Interfaces;

use App\Models\Folder;
use App\Models\MailLog;
use App\Models\SentMail;
use Illuminate\Http\Request;

interface MailRepositoryInterface
{
    public function inbox($filter);

    public function inboxWithFolderId(Folder $folder);

    public function newMessage();

    public function reply(Request $request, MailLog $mail_log);

    public function forward(Request $request, MailLog $mail_log);

    public function markAsRead($id);

    public function getHistories($id);

    public function store(Request $request);

    public function delete(MailLog $mailLog);

    public function redo(MailLog $mailLog);

    public function deleteForever(MailLog $mailLog);

    public function deleteForeverProcess(MailLog $mailLog);

    public function deleteSentMail(SentMail $sent_mail);
}
