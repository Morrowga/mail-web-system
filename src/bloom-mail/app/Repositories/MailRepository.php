<?php

namespace App\Repositories;

use App\Mail\SendMail;
use App\Models\MailLog;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;
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

        $messageIds = $messages->map(fn($message) => $message->getMessageId()[0])->toArray();

        $existingLogs = MailLog::whereIn('message_id', $messageIds)->get()->keyBy('message_id');

        $emails = $messages->map(function ($message) use ($existingLogs) {

            $messageId = $message->getMessageId()[0];

            $mailLog = $existingLogs[$messageId] ?? MailLog::create(["message_id" => $messageId, "status" => "new"]);

            return [
                'id' => $messageId,
                'subject' => $message->getSubject()[0],
                'sender' => $message->getFrom()[0]->mail,
                'date' => $message->getDate()[0]->toDateString(),
                'time' => $message->getDate()[0]->toTimeString(),
                'datetime' => $message->getDate()[0]->toDateTimeString(),
                'name' => $message->getFrom()[0]->personal, // Sender's name
                'body' => $message->getHTMLBody() ?? $message->getTextBody(),
                'status' => $mailLog->status // Include the status from the database
            ];
        })
        ->sortByDesc('datetime')
        ->values()
        ->all();


        return $emails;
    }

    public function newMessage()
    {
        $inbox = $this->client->getFolder('INBOX');

        $messages = $inbox->messages()->all()->get();

        // Collect all message IDs from the emails
        $messageIds = $messages->map(fn($message) => $message->getMessageId()[0])->toArray();

        // Fetch existing logs for these message IDs in bulk
        $existingLogs = MailLog::whereIn('message_id', $messageIds)->get()->keyBy('message_id');

        // Find the first new message that is not in the existing logs
        $newMessage = $messages->first(function ($message) use ($existingLogs) {
            $messageId = $message->getMessageId()[0];
            return !isset($existingLogs[$messageId]);
        });

        // If there is a new message, create a log and format the response
        if ($newMessage) {
            $messageId = $newMessage->getMessageId()[0];

            // Create a new log entry for this message
            $mailLog = MailLog::create([
                "message_id" => $messageId,
                "status" => "new"
            ]);

            // Prepare the response for the single new message
            $email = [
                'id' => $messageId,
                'subject' => $newMessage->getSubject()[0],
                'sender' => $newMessage->getFrom()[0]->mail,
                'date' => $newMessage->getDate()[0]->toDateString(),
                'time' => $newMessage->getDate()[0]->toTimeString(),
                'datetime' => $newMessage->getDate()[0]->toDateTimeString(),
                'name' => $newMessage->getFrom()[0]->personal, // Sender's name
                'body' => $newMessage->getHTMLBody() ?? $newMessage->getTextBody(),
                'status' => $mailLog->status
            ];

            return $email;
        }

        return null;
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

        Log::info('Sending email using the following configuration:', [
            'MAIL_MAILER' => config('mail.mailers.smtp'),
        ]);

        Mail::to($data['to'])
            ->cc($data['cc'])
            ->bcc($data['bcc'])
            ->send(new SendMail($data));


        return $this->success('Email Sent.');
    }

}
