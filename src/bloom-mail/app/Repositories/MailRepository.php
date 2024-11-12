<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Reply;
use App\Mail\SendMail;
use App\Models\MailLog;
use App\Models\SentMail;
use Webklex\PHPIMAP\IMAP;
use App\Events\TakingMail;
use Illuminate\Http\Request;
use Webklex\PHPIMAP\Message;
use App\Traits\CRUDResponses;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\TextPart;
use App\Interfaces\MailRepositoryInterface;
use Webklex\IMAP\Exceptions\ConnectionFailedException;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;

class MailRepository implements MailRepositoryInterface
{
    use CRUDResponses;

    protected $client;
    protected $isConnected = false;

    /**
     * MailRepository constructor.
     */
    public function __construct()
    {
        $this->client = Client::account('default');

        try {
            $this->client->connect();
            $this->isConnected = true;
        } catch (ConnectionFailedException $e) {
            $this->isConnected = false;
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

        $pageType = request()->query('page_type');

        $sent = SentMail::orderBy('datetime', 'desc')->count();
        $inbox = MailLog::where('status', '!=', 'deleted')->orderBy('datetime', 'desc')->count();

        switch ($pageType) {
            case 'sent':
                $data = SentMail::orderBy('datetime', 'desc')->paginate(10);
                break;

            case 'inbox':
            default:
                $data = MailLog::where('status', '!=', 'deleted')->orderBy('datetime', 'desc')->paginate(10);
                break;
        }

        return [
            "data" => $data,
            "inbox" => $inbox,
            "sent" => $sent,
        ];
    }

    public function newMessage()
    {
        try {
            $inbox = $this->client->getFolder('INBOX');

            $messages = $inbox->messages()->all()->get();

            $newEmails = [];

            foreach ($messages as $message) {
                $uid = $message->getUid();
                $messageId = $message->getMessageId();
                $subject = $message->getSubject()[0];
                $senderArray = $message->getFrom();
                $dateSent = $message->getDate();
                $body = $message->getHTMLBody() ?? $message->getTextBody();
                $senderName = $senderArray[0]->personal ?? 'Unknown Sender';
                $senderEmail = $senderArray[0]->mail ?? 'unknown@example.com';

                $inReplyTo = $message->getHeader()->get('in-reply-to');
                $references = $message->getHeader()->get('references');

                // Check if it's an original email (not a reply)
                if (($inReplyTo || $references) && empty(trim($body)) && stripos($subject, 're:') === 0) {
                    continue;
                }

                $flags = $message->getFlags()->toArray();
                $status = in_array('Seen', $flags) ? 'read' : 'new';

                // Check if email already exists in the database
                $existingMail = MailLog::where('message_id', $messageId)->first();

                if (!$existingMail) {
                    // Save the original email to the database
                    MailLog::create([
                        'uid' => $uid,
                        'message_id' => $messageId,
                        'subject' => $subject,
                        'sender' => $senderEmail,
                        'name' => $senderName,
                        'body' => $body,
                        'datetime' => $dateSent[0]->toDateTimeString(),
                        'status' => $status,
                    ]);

                    // Add to the newEmails array to return
                    $newEmails[] = [
                        'uid' => $uid,
                        'message_id' => $messageId,
                        'subject' => $subject,
                        'sender' => $senderEmail,
                        'name' => $senderName,
                        'body' => $body,
                        'datetime' => $dateSent[0]->toDateTimeString(),
                        'status' => $status,
                    ];

                    broadcast(new TakingMail($newEmails));
                }
        }

            $deletedMails = MailLog::where('status', 'deleted')->delete();
        } catch (Exception $e) {
            logger()->error("Error fetching emails: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to fetch mails.'], 500);
        }
    }

    public function markAsRead($id)
    {
        $mailLog = MailLog::find($id);

        $inbox = $this->client->getFolder('INBOX');

        $message = $inbox->query()->getMessageByUid($mailLog->uid);

        if ($message) {
            $message->setFlag('Seen');

            $messageId = $message->getMessageId();

            if ($mailLog) {
                $mailLog->status = 'read';
                $mailLog->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Email marked as read and updated in database.']);
        }
    }

    public function getHistories($id)
    {
        $mailLog = MailLog::find($id);
        $inbox = $this->client->getFolder('INBOX');
        $message = $inbox->query()->getMessageByUid($mailLog->uid);

        $histories = [];

        if ($message) {
            $threadMessages = $message->thread($inbox);

            foreach ($threadMessages as $threadMessage) {
                $uid = $threadMessage->getUid();
                $messageId = $threadMessage->getMessageId()[0];
                $subject = $threadMessage->getSubject()[0] ?? 'No Subject';
                $senderArray = $threadMessage->getFrom();
                $senderEmail = $senderArray[0]->mail ?? 'unknown@example.com';
                $senderName = isset($senderArray[0]) ? (string)$senderArray[0]->personal : 'Unknown Sender';

                $body = $threadMessage->getHTMLBody();

                $dateSent = $threadMessage->getDate()[0] ?? now();
                $status = in_array('\\Seen', $threadMessage->getFlags()->toArray()) ? 'read' : 'unread';

                $histories[] = [
                    'uid' => $uid,
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'sender' => $senderEmail,
                    'name' => $senderName,
                    'body' => $body,
                    'datetime' => $dateSent->toDateTimeString(),
                    'status' => $status,
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Email histories fetched successfully.',
                'data' => $histories,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found.',
            ], 404);
        }
    }

    public function reply(Request $request, MailLog $mail_log)
    {
        $emailData = [
            'subject' => $request->subject,
            'from' => $request->from,
            'to' => $request->to,
            'message_content' => $request->message_content,
            'in_reply_to' => $request->og_message_id,
            'references' => $request->og_message_id,
        ];

        Mail::send('emails.reply', ['emailData' => $emailData], function ($message) use ($emailData) {
            $message->from($emailData['from'])
                    ->to($emailData['to'])
                    ->subject($emailData['subject']);

            $message->getHeaders()->addTextHeader('In-Reply-To', '<' . $emailData['in_reply_to'] . '>');
            $message->getHeaders()->addTextHeader('References', '<' . $emailData['references'] . '>');
        });

        return $this->success('Email Sent.');
    }

    public function forward(Request $request, MailLog $mail_log)
    {
        $emailData = [
            'subject' => "Fwd: " . $mail_log->subject,
            'from' => $request->from,
            'to' => $request->to,
            'message_content' => $request->message_content,
        ];

        $originalEmail = [
            'sender' => $mail_log->sender,
            'datetime' => $mail_log->datetime,
            'subject' => $mail_log->subject,
            'body' => $this->cleanHtmlContent($mail_log->body),
        ];

        Mail::send('emails.forward', compact('emailData', 'originalEmail'), function ($message) use ($emailData) {
            $message->from($emailData['from'])
                    ->to($emailData['to'])
                    ->subject($emailData['subject']);
        });

        return response()->json(['status' => 'success', 'message' => 'Email forwarded successfully.']);
    }

    public function deleteForever(MailLog $mailLog)
    {
        try {
            if (!$mailLog) {
                return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
            }

            $inbox = $this->client->getFolder('INBOX');
            $message = $inbox->query()->getMessageByUid($mailLog->uid);

            if ($message) {
                $message->delete();
            }

            $mailLog->status = 'deleted';
            $mailLog->update();

            return response()->json(['status' => 'success', 'message' => 'Email deleted permanently.']);
        } catch (Exception $e) {
            logger()->error("Error deleting email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete email.'], 500);
        }
    }

    public function deleteSentMail(SentMail $sent_mail)
    {
        try {
            $sent_mail->delete();

            return response()->json(['status' => 'success', 'message' => 'Email deleted permanently.']);
        } catch (Exception $e) {
            logger()->error("Error deleting email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete email.'], 500);
        }
    }


    private function cleanHtmlContent($content)
    {
        return preg_replace('/<div dir="ltr">(.*?)<\/div>/s', '$1', $content);
    }

    public function store(Request $request)
    {

        if (!$this->isConnected) {
            logger()->error("IMAP connection is not established. Cannot send mail.");
            return $this->error('Connection Failed.', []);
        }

        try {
            $messageId = '<' . uniqid('email-', true) . '@' . config('app.url') . '>';

            $data = [
                'subject' => $request->subject,
                'fromAddress' => $request->from,
                'to' => $request->to,
                'cc' => $request->cc ?? [],
                'bcc' => $request->bcc ?? [],
                'message_content' => $request->message_content,
                'template_id' => $request->template_id,
                'message_id' => $messageId,
            ];

            $email = SentMail::create([
                'subject' => $data['subject'],
                'sender' => $data['fromAddress'],
                'name' => $request->from_name ?? env('IMAP_USERNAME'),
                'body' => $data['message_content'],
                'datetime' => now(),
                'message_id' => $messageId,
            ]);

            Log::info('Sending email using the following configuration:', [
                'MAIL_MAILER' => config('mail.mailers.smtp'),
                'message_id' => $messageId
            ]);

            Mail::to($data['to'])
                ->cc($data['cc'])
                ->bcc($data['bcc'])
                ->send(new SendMail($data));

            return $this->success('Email Sent.');
        } catch (Exception $e) {
            logger()->error("Error sending email: " . $e->getMessage());
            return $this->error('Connection Failed.', []);
        }
    }
}
