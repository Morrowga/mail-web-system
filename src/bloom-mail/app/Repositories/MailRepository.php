<?php

namespace App\Repositories;

use App\Models\Reply;
use App\Mail\SendMail;
use App\Models\MailLog;
use Webklex\PHPIMAP\IMAP;
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
        // Step 1: Get all messages from the inbox
        // $inbox = $this->client->getFolder('INBOX');
        // $messages = $inbox->messages()->all()->get();

        // foreach ($messages as $message) {
        //     // Step 2: Extract necessary headers and details
        //     $uid = $message->getUid();
        //     $body = $message->getHTMLBody();
        //     $dateSent = $message->getDate();
        //     $subject = $message->getSubject()[0];
        //     $senderArray = $message->getFrom();
        //     $senderName = strval($senderArray[0]->personal);
        //     $senderEmail = $senderArray[0]->mail;

        //     // Message headers for threading
        //     $header = $message->getHeader();
        //     $messageId = $header->message_id[0];
        //     $inReplyTo = $header->get('in-reply-to');
        //     $references = $header->get('references');

        //     // Determine parent_message_id (if this message is a reply)
        //     $parentMessageId = null;
        //     if ($inReplyTo) {
        //         $parentMessageId = trim($inReplyTo[0], '<>');
        //     } elseif ($references) {
        //         // Use the last message ID in references as the immediate parent
        //         $referencesList = explode(' ', trim($references[0]));
        //         $parentMessageId = trim(end($referencesList), '<>');
        //     }

        //     // Step 3: Save the email to the database
        //     MailLog::firstOrCreate(
        //         ['message_id' => $messageId],
        //         [
        //             'uid' => $uid,
        //             'message_id' => $messageId,
        //             'subject' => $subject,
        //             'sender' => $senderEmail,
        //             'name' => $senderName,
        //             'body' => $body,
        //             'datetime' => $dateSent[0]->toDateTimeString(),
        //             'parent_message_id' => $parentMessageId,
        //             'status' => 'new',
        //         ]
        //     );
        // }

        // Step 4: Fetch the original messages and build the threads
        $threads = MailLog::whereNull('parent_message_id')
                    ->orderBy('datetime', 'desc')
                    ->with('allReplies')
                    ->paginate(10);

        return $threads;
    }


    public function getThreadReplies($mailLog)
    {
        $replies = MailLog::where('parent_message_id', $mailLog->message_id)->get();

        foreach ($replies as $reply) {
            // Recursively get replies to each reply
            $reply->replies = $this->getThreadReplies($reply);
        }

        return $replies;
    }



    public function newMessage()
    {
        $inbox = $this->client->getFolder('INBOX');
        $messages = $inbox->messages()->all()->get();

        $newEmails = [];

        foreach ($messages as $message) {
            $uid = $message->getUid();
            $body = $message->getHTMLBody() ?? $message->getTextBody();
            $dateSent = $message->getDate();
            $subject = $message->getSubject()[0];
            $senderArray = $message->getFrom();

            if (!empty($senderArray) && isset($senderArray[0])) {
                $senderName = strval($senderArray[0]->personal);
                $senderEmail = $senderArray[0]->mail;
            } else {
                $senderName = 'Unknown Sender';
                $senderEmail = 'unknown@example.com';
            }

            $header = $message->getHeader();
            $messageId = $header->message_id[0];
            $inReplyTo = $header->get('in-reply-to');
            $references = $header->get('references');

            $flags = $message->getFlags();

            $isRead = $flags->has('\\Seen');

            $parentMessageId = null;
            if ($inReplyTo) {
                $parentMessageId = trim($inReplyTo[0], '<>');
            } elseif ($references) {
                $referencesList = explode(' ', trim($references[0]));
                $parentMessageId = trim(end($referencesList), '<>');
            }

            $existingMail = MailLog::where('message_id', $messageId)->first();

            if (!$existingMail) {
                $newMail = MailLog::create([
                    'uid' => $uid,
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'sender' => $senderEmail,
                    'name' => $senderName,
                    'body' => $body,
                    'datetime' => $dateSent[0]->toDateTimeString(),
                    'parent_message_id' => $parentMessageId,
                    'status' => $isRead ? 'read' : 'new',
                ]);

                $newEmails[] = $newMail;
            }
        }
    }

    public function markAsRead($uid)
    {
        $inbox = $this->client->getFolder('INBOX');
        $message = $inbox->query()->getMessageByUid($uid);

        if ($message) {
            $message->setFlag(['\Seen']);

            $messageId = $message->getMessageId();

            $mailLog = MailLog::where('message_id', $messageId)->first();
            if ($mailLog) {
                $mailLog->status = 'read';
                $mailLog->save();
            }

            $this->client->disconnect();

            return response()->json(['status' => 'success', 'message' => 'Email marked as read and updated in database.']);
        }

        return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
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
