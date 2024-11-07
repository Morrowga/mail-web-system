<?php

namespace App\Repositories;

use App\Mail\SendMail;
use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Mail;
use App\Interfaces\MailRepositoryInterface;
use Webklex\IMAP\Exceptions\ConnectionFailedException;

class MailRepository implements MailRepositoryInterface
{
    use CRUDResponses;

    protected $client;

    /**
     * MailRepository constructor.
     */
    public function __construct()
    {
        $this->client = Client::account('default');

        try {
            $this->client->connect();
        } catch (ConnectionFailedException $e) {
            logger()->error("IMAP connection failed: " . $e->getMessage());
        }
    }

    /**
     * Fetch emails from the inbox folder.
     *
     * @return array
     */
    public function inbox()
    {
        $inbox = $this->client->getFolder('INBOX');

        $messages = $inbox->messages()->all()->get();

        // Collect all message IDs from the emails
        $messageIds = $messages->map(fn($message) => $message->getMessageId()[0])->toArray();

        // Fetch existing logs for these message IDs in bulk
        $existingLogs = MailLog::whereIn('message_id', $messageIds)->get()->keyBy('message_id');

        // Now map over messages and use $existingLogs to check each message
        $emails = $messages->map(function ($message) use ($existingLogs) {
            $messageId = $message->getMessageId()[0];

            // Check if the message ID already exists in $existingLogs; if not, create a new entry
            $mailLog = $existingLogs[$messageId] ?? MailLog::create(["message_id" => $messageId, "status" => "new"]);

            return [
                'id' => $messageId,
                'subject' => $message->getSubject()[0],
                'sender' => $message->getFrom()[0]->mail,
                'date' => $message->getDate()[0]->toDateString(),
                'time' => $message->getDate()[0]->toTimeString(),
                'name' => $message->getFrom()[0]->personal, // Sender's name
                'body' => $message->getHTMLBody() ?? $message->getTextBody(),
                'status' => $mailLog->status // Include the status from the database
            ];
        })->all();

        return $this->success('Fetched Emails', $emails);
    }


    public function store(Request $request)
    {
        $data = [
            'subject' => $request->subject,
            'fromAddress' => $request->from,
            'to' => $request->to,
            'cc' => $request->cc ?? [],
            'bcc' => $request->bcc ?? [],
            'message_content' => $request->message_content,
            'template_id' => $request->template_id,
        ];

        Mail::to($data['to'])
            ->cc($data['cc'])
            ->bcc($data['bcc'])
            ->send(new SendMail($data));


        return $this->success('Email Sent.');
    }

}
