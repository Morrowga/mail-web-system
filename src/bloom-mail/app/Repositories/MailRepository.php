<?php

namespace App\Repositories;

use DateTime;
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
use App\Events\EmailStatusUpdated;
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

    /**
     * MailRepository constructor.
     */
    public function __construct()
    {
        $this->client = Client::account('default');

        try {
            $this->client->connect();

        } catch (\Throwable $e) {

            return response()->json([
                "connection" => false
            ]);
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

        $sent = SentMail::orderBy('datetime', 'desc')->where('type', 'sent')->count();
        $inbox = MailLog::where('status', '!=', 'deleted')->orderBy('datetime', 'desc')->count();

        switch ($pageType) {
            case 'sent':
                $data = SentMail::orderBy('datetime', 'desc')->where('type', 'sent')->paginate(10);
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

            // Collect thread message data
            foreach ($threadMessages as $threadMessage) {
                $uid = $threadMessage->getUid();
                $messageId = $threadMessage->getMessageId()[0];
                $subject = $threadMessage->getSubject()[0] ?? 'No Subject';
                $senderArray = $threadMessage->getFrom();
                $senderEmail = $senderArray[0]->mail ?? 'unknown@example.com';
                $senderName = isset($senderArray[0]) ? (string)$senderArray[0]->personal : 'Unknown Sender';

                // Check if the message has an HTML body, otherwise use plain text
                if ($threadMessage->hasHTMLBody()) {
                    $body = $threadMessage->getHTMLBody();
                } else {
                    $body = $threadMessage->getTextBody();
                }

                // Get the date the message was sent, fallback to current time if not available
                $dateSent = $threadMessage->getDate()[0] ?? Carbon::now('Asia/Tokyo')->toDateTimeString();
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

            $systemMailHistories = $mailLog->mail_histories->toArray();

            $mergedHistories = array_merge($histories, $systemMailHistories);

            usort($mergedHistories, function ($a, $b) {
                return strtotime($a['datetime']) - strtotime($b['datetime']);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Email histories fetched successfully.',
                'data' => $mergedHistories,
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
        // $originalEmail = MailLog::where('message_id', $request->og_message_id)->first();

        if (!$mail_log) {
            return $this->error('Original email not found.');
        }

        $emailData = [
            'subject' => $request->subject,
            'from' => $request->from,
            'to' => $request->to,
            'message_content' => $request->message_content,
            'in_reply_to' => $mail_log->message_id,
            'references' => $mail_log->message_id,
        ];

        try {
            // Send the reply email
            Mail::send('emails.reply', [
                'emailData' => $emailData,
                'originalEmail' => $mail_log,
            ], function ($message) use ($emailData) {
                $message->from($emailData['from'])
                        ->to($emailData['to'])
                        ->subject($emailData['subject'])
                        ->getHeaders()
                        ->addTextHeader('In-Reply-To', '<' . $emailData['in_reply_to'] . '>')
                        ->addTextHeader('References', '<' . $emailData['references'] . '>');
            });

            SentMail::create([
                'subject' => $emailData['subject'],
                'sender' => $emailData['from'],
                'body' => $emailData['message_content'],
                'parent_id' => $mail_log->id,
                'type' => 'reply',
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
            ]);

            if($mail_log->status != 'confirmed' || $mail_log->status != 'resolved')
            {
                $mail_log->status = 'resolved';
                $mail_log->previous_status = null;
                $mail_log->update();
            }

            broadcast(new EmailStatusUpdated($mail_log, 'resolved'));

            return $this->success('Email Sent.');
        } catch (\Exception $e) {
            \Log::error('Error sending reply email: ' . $e->getMessage());

            return $this->error('Failed to send reply email.');
        }
    }



    public function forward(Request $request, MailLog $mail_log)
    {
        // try {
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

            Log::info('Sending forward email', $emailData);

            Mail::send('emails.forward', compact('emailData', 'originalEmail'), function ($message) use ($emailData) {
                $message->from($emailData['from'])
                        ->to($emailData['to'])
                        ->subject($emailData['subject']);
            });

            $forwardedBody = "
            <div style='margin: 0; padding: 0;'>
                <p style='margin: 0; padding: 0;'><strong>Forwarded message</strong></p>
                <p style='margin: 5px 0; padding: 0;'><strong>From:</strong> " . e($emailData['from']) . "</p>
                <p style='margin: 5px 0; padding: 0;'><strong>Date:</strong> " . e($mail_log->datetime) . "</p>
                <p style='margin: 5px 0; padding: 0;'><strong>Subject:</strong> " . e($mail_log->subject) . "</p>
                <p style='margin: 5px 0; padding: 0;'><strong>Body:</strong></p>
                <div style='margin: 0; padding: 0;'>" . $mail_log->body . "</div> <!-- Don't escape here -->
            </div>
            <hr>
            <div style='margin: 0; padding: 0;'>
                <p>" . e($request->message_content) . "</p>
            </div>";


            SentMail::create([
                'subject' => $emailData['subject'],
                'sender' => $emailData['from'],
                'body' => $forwardedBody,
                'parent_id' => $mail_log->id,
                'type' => 'forward',
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Email forwarded successfully.']);
        // } catch (\Exception $e) {
        //     // Log the error if email sending fails
        //     Log::error('Error sending forward email: ' . $e->getMessage());
        //     return response()->json(['status' => 'error', 'message' => 'Failed to send email.'], 500);
        // }
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

        // if (!$this->isConnected) {
        //     logger()->error("IMAP connection is not established. Cannot send mail.");
        //     return $this->error('Connection Failed.', []);
        // }

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
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
                'type' => 'sent',
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
