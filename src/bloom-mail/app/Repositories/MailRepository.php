<?php

namespace App\Repositories;

use DateTime;
use Carbon\Carbon;
use App\Models\Spam;
use App\Models\Reply;
use App\Mail\SendMail;
use App\Models\Folder;
use App\Models\MailLog;
use App\Models\SentMail;
use Webklex\PHPIMAP\IMAP;
use App\Events\TakingMail;
use App\Models\Attachment;
use App\Models\FolderMail;
use Illuminate\Http\Request;
use Webklex\PHPIMAP\Message;
use App\Traits\CRUDResponses;
use Webklex\IMAP\Facades\Client;
use App\Events\EmailStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
    public function inbox($filter = [])
    {
        $pageType = request()->query('page_type');

        $folders = Folder::withCount(['mails' => function ($query) {
            $query->where('status', 'new');
        }])->get();

        $sent = SentMail::orderBy('datetime', 'desc')->where('type', 'sent')->count();

        $inbox = 0;
        $trash = 0;

        switch ($pageType) {
            case 'sent':
                $data = SentMail::orderBy('datetime', 'desc')->where('type', 'sent')->with('template')->paginate(10);
                $inbox = MailLog::where('status','new')->count();
                $trash = MailLog::where('status', 'deleted')->count();
                break;
            case 'trash':
                $query = MailLog::orderBy('datetime', 'desc')->where('status','deleted');
                $trash = $query->count();
                $data = $query->paginate(10);
                $inbox = MailLog::where('status', '=', 'new')->count();
                break;
            case 'inbox':
            default:
                $query = MailLog::where('status', '!=', 'deleted');

                if (!empty($filter['status'])) {
                    $query->where('status', $filter['status']);
                }

                if (!empty($filter['keyword'])) {
                    $query->where(function ($q) use ($filter) {
                        $q->where('subject', 'like', '%' . $filter['keyword'] . '%')
                        ->orWhere('body', 'like', '%' . $filter['keyword'] . '%')
                        ->orWhere('sender', 'like', '%' . $filter['keyword'] . '%')
                        ->orWhere('name', 'like', '%' . $filter['keyword'] . '%')
                        ->orWhere('person_in_charge', 'like', '%' . $filter['keyword'] . '%');
                    });
                }

                if (!empty($filter['person_in_charge'])) {
                    $query->where('person_in_charge', $filter['person_in_charge']);
                }
                if (!empty($filter['from']) && !empty($filter['to'])) {
                    $query->whereBetween('datetime', [$filter['from'], $filter['to']]);
                }

                $inbox = MailLog::where('status', 'new')->count();
                $trash = MailLog::where('status','deleted')->count();

                $data = $query->orderBy('datetime', 'desc')->paginate(10);
                break;
        }

        return [
            "data" => $data,
            "inbox" => $inbox,
            "sent" => $sent,
            "folders" => $folders,
            "trash" => $trash
        ];
    }

    public function inboxWithFolderId(Folder $folder)
    {
        $folders = Folder::withCount(['mails' => function ($query) {
            $query->where('status', 'new');
        }])->get();

        $folderId = $folder->id;

        $sent = SentMail::orderBy('datetime', 'desc')->where('type', 'sent')->count();
        $inbox = MailLog::where('status', 'new')->orderBy('datetime', 'desc')->count();
        $trash = MailLog::orderBy('datetime', 'desc')->where('status','deleted')->count();

        $data = MailLog::where('status', '!=', 'deleted')
        ->when(isset($folderId), function ($query) use ($folderId) {
            $query->whereHas('folders', fn($q) => $q->where('folder_id', $folderId));
        })
        ->orderBy('datetime', 'desc')
        ->paginate(10);


        return [
            "data" => $data,
            "inbox" => $inbox,
            "sent" => $sent,
            "trash" => $trash,
            "folders" => $folders
        ];
    }

    public function newMessage()
    {
        // try {
            Log::info('Message fetching started');

            // $inbox = $this->client->getFolder('INBOX');
            // $messages = $inbox->messages()->all()->setFetchOrder("desc")->get();
            $newEmails = [];

            // $status = 'new';

            // $deleted_date = null;

            // foreach ($messages as $message) {
            //     $uid = $message->getUid();
            //     $messageId = $message->getMessageId();
            //     $subject = $this->decodeString($message->getSubject()[0]);

            //     $existingMail = MailLog::where('message_id', $messageId)->first();

            //     if (!$existingMail) {
            //         $senderArray = $message->getFrom();
            //         $dateSent = $message->getDate();
            //         $body = $message->getHTMLBody() ?? $message->getTextBody();
            //         $senderName = $senderArray[0]->personal ?? 'Unknown Sender';

            //         if (!empty($senderArray) && isset($senderArray[0]->mail)) {
            //             $senderEmail = $this->decodeString($senderArray[0]->mail);
            //         } else {
            //             $senderEmail = 'unknown@example.com';
            //         }

            //         $spamCheck = Spam::where('mail_address', $senderEmail)->first();

            //         $flags = $message->getFlags()->toArray();

            //         if(!empty($spamCheck))
            //         {
            //             $message->delete(false);

            //             $status = 'deleted';
            //         } else {

            //             $emailParts = explode('@', $senderEmail);
            //             $domain = isset($emailParts[1]) ? $emailParts[1] : null;

            //             $spamDomainCheck = Spam::where('mail_address', $domain)->first();

            //             if(!empty($spamDomainCheck))
            //             {
            //                 $message->delete(false);

            //                 $status = 'deleted';
            //                 $deleted_date = Carbon::now('Asia/Tokyo')->toDateTimeString();
            //             } else {
            //                 $status = in_array('Seen', $flags) ? 'read' : 'new';
            //             }
            //         }

            //         $inReplyTo = $message->getHeader()->get('in-reply-to');
            //         $references = $message->getHeader()->get('references');

            //         if (($inReplyTo || $references) && empty(trim($body)) && stripos($subject, 're:') === 0) {
            //             continue;
            //         }

            //         $attachments = $message->getAttachments();

            //         $newMail = MailLog::create([
            //             'uid' => $uid,
            //             'message_id' => $messageId,
            //             'subject' => $subject,
            //             'sender' => $senderEmail,
            //             'name' => $senderName,
            //             'body' => $body,
            //             'datetime' => $dateSent[0]->toDateTimeString(),
            //             'status' => $status,
            //             'deleted_at' => $deleted_date
            //         ]);

            //         foreach ($attachments as $attachment) {
            //             $fileName = $attachment->getName();
            //             $filePath = 'mails/attachments/' . $fileName;

            //             Storage::disk('public')->put($filePath, $attachment->content);

            //             $mimeType = $attachment->getMimeType();
            //             $fileSize = $attachment->getSize();

            //             Attachment::create([
            //                 'file_name' => $fileName,
            //                 'mime_type' => $mimeType,
            //                 'file_size' => $fileSize,
            //                 'path' => 'storage/' . $filePath,
            //                 'mail_log_id' => $newMail->id
            //             ]);
            //         }

            //         $newEmails[] = [
            //             'uid' => $uid,
            //             'message_id' => $messageId,
            //             'subject' => $subject,
            //             'sender' => $senderEmail,
            //             'name' => $senderName,
            //             'body' => $body,
            //             'datetime' => $dateSent[0]->toDateTimeString(),
            //             'status' => $status,
            //         ];
            //     }
            // }

            Log::info('Message fetching ended');

            Log::info('Sending to queue started');

            $checkNew = count($newEmails) > 0 ? 1 : 0;

            broadcast(new TakingMail(["new" => $checkNew]));

            Log::info('Sending to queue ended');

        // } catch (Exception $e) {
        //     logger()->error("Error fetching emails: " . $e->getMessage());
        // }
    }

    public function folderMatching()
    {
        $folders = Folder::all();

        foreach ($folders as $folder) {
            $searchCharacter = $folder->search_character;
            $method = strtolower($folder->method);

            $allMails = MailLog::all();

            foreach ($allMails as $mail) {
                $subject = $mail->subject;
                $isMatch = false;

                if ($method === 'exact_match' && $subject === $searchCharacter) {
                    $isMatch = true;
                } elseif ($method === 'partial_match' && str_contains($subject, $searchCharacter)) {
                    $isMatch = true;
                } elseif ($method === 'front_match' && str_starts_with($subject, $searchCharacter)) {
                    $isMatch = true;
                } elseif ($method === 'backward_match' && str_ends_with($subject, $searchCharacter)) {
                    $isMatch = true;
                }

                if ($isMatch) {
                    DB::table('folder_mails')->updateOrInsert(
                        ['mail_log_id' => $mail->id],
                        ['folder_id' => $folder->id]
                    );
                } else {
                    DB::table('folder_mails')
                        ->where('mail_log_id', $mail->id)
                        ->where('folder_id', $folder->id)
                        ->delete();
                }
            }
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

            broadcast(new EmailStatusUpdated($mailLog, 'read'));

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
                $subject = isset($threadMessage->getSubject()[0])
                ? $this->decodeString($threadMessage->getSubject()[0])
                : 'No Subject';
                $senderArray = $threadMessage->getFrom();
                if (!empty($senderArray) && isset($senderArray[0]->mail)) {
                    $senderEmail = $this->decodeString($senderArray[0]->mail);
                } else {
                    $senderEmail = 'unknown@example.com';
                }
                $senderName = isset($senderArray[0]) ? (string)$senderArray[0]->personal : 'Unknown Sender';

                if ($threadMessage->hasHTMLBody()) {
                    $body = $threadMessage->getHTMLBody();
                } else {
                    $body = $threadMessage->getTextBody();
                }

                $dateSent = $threadMessage->getDate()[0] ?? Carbon::now('Asia/Tokyo')->toDateTimeString();
                $status = in_array('Seen', $threadMessage->getFlags()->toArray()) ? 'read' : 'unread';

                $findAttachement = MailLog::where('uid',$uid)->with(['attachments'])->first();

                $histories[] = [
                    'uid' => $uid,
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'sender' => $senderEmail,
                    'name' => $this->decodeString($senderName),
                    'body' => $body,
                    'datetime' => $dateSent->toDateTimeString(),
                    'status' => $status,
                    'attachments' => $findAttachement != null ? $findAttachement->attachments : []
                ];
            }

            $systemMailHistories = $mailLog->mail_histories->toArray();

            $mergedHistories = array_merge($histories, $systemMailHistories);

            usort($mergedHistories, function ($a, $b) {
                return strtotime($b['datetime']) - strtotime($a['datetime']);
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

    private function decodeString($value)
    {
        try {
            if (preg_match('/=\?[^?]+\?/', $value)) {
                $decodedValue = iconv_mime_decode($value, 0, 'UTF-8');
                return $decodedValue ?: $value;
            }

            return $value;
        } catch (\Exception $e) {
            logger()->error("Error decoding {$attribute}: " . $e->getMessage());
            return $value;
        }
    }

    public function reply(Request $request, MailLog $mail_log)
    {
        if (!$mail_log) {
            return $this->error('Original email not found.');
        }

        $emailData = [
            'subject' => $request->subject,
            'from' => $request->from,
            'to' => $request->to,
            'template_id' => $request->template_id ?? null,
            'message_content' => $request->message_content,
            'in_reply_to' => $mail_log->message_id,
            'references' => $mail_log->message_id,
        ];

        try {

            $replyMailData = SentMail::create([
                'subject' => $emailData['subject'],
                'sender' => $emailData['from'],
                'mailto' => $emailData['to'],
                'body' => $emailData['message_content'],
                'name' => Auth::user()->name,
                'parent_id' => $mail_log->id,
                'template_id' => $emailData['template_id'],
                'type' => 'reply',
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
            ]);

            // Send the reply email
            Mail::send('emails.reply', [
                'emailData' => $emailData,
                'replyMailData' => $replyMailData,
                'originalEmail' => $mail_log,
            ], function ($message) use ($emailData) {
                $message->from($emailData['from'])
                        ->to($emailData['to'])
                        ->subject($emailData['subject'])
                        ->getHeaders()
                        ->addTextHeader('In-Reply-To', '<' . $emailData['in_reply_to'] . '>')
                        ->addTextHeader('References', '<' . $emailData['references'] . '>');
            });

            if($mail_log->status != 'confirmed' || $mail_log->status != 'resolved')
            {
                $mail_log->status = 'resolved';
                $mail_log->previous_status = null;
                $mail_log->person_in_charge = Auth::user()->name;
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
        try {
            $emailData = [
                'subject' => "Fwd: " . $mail_log->subject,
                'from' => $request->from,
                'to' => $request->to,
                'message_content' => $request->message_content,
                'template_id' => $request->template_id ?? null
            ];

            $originalEmail = [
                'sender' => $mail_log->sender,
                'datetime' => $mail_log->datetime,
                'subject' => $mail_log->subject,
                'body' => $this->cleanHtmlContent($mail_log->body),
            ];

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

            $forwardMailData = SentMail::create([
                'subject' => $emailData['subject'],
                'sender' => $emailData['from'],
                'body' => $forwardedBody,
                'parent_id' => $mail_log->id,
                'to' => $emailData['to'],
                'template_id' => $emailData['template_id'],
                'type' => 'forward',
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
            ]);

            Mail::send('emails.forward', compact('emailData', 'originalEmail' , 'forwardMailData'), function ($message) use ($emailData) {
                $message->from($emailData['from'])
                        ->to($emailData['to'])
                        ->subject($emailData['subject']);
            });


            return response()->json(['status' => 'success', 'message' => 'Email forwarded successfully.']);
        } catch (\Exception $e) {
            // Log the error if email sending fails
            Log::error('Error sending forward email: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to send email.'], 500);
        }
    }

    public function delete(MailLog $mailLog)
    {
        try {
            if (!$mailLog) {
                return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
            }

            $this->softDelete($mailLog);

            return response()->json(['status' => 'success', 'message' => 'Email deleted permanently.']);
        } catch (Exception $e) {
            logger()->error("Error deleting email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete email.'], 500);
        }
    }

    public function redo(MailLog $mailLog)
    {
        try {
            if (!$mailLog) {
                return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
            }

            $this->redoProcess($mailLog);

            return response()->json(['status' => 'success', 'message' => 'Email deleted permanently.']);
        } catch (Exception $e) {
            logger()->error("Error deleting email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete email.'], 500);
        }
    }

    public function redoProcess(MailLog $mailLog)
    {
        $inbox = $this->client->getFolder('INBOX');

        $message = $inbox->query()->getMessageByUid($mailLog->uid);

        $message->restore(false);

        $mailLog->status = $mailLog->previous_status ?? 'read';
        $mailLog->previous_status = '';
        $mailLog->deleted_at = null;
        $mailLog->update();
    }

    public function softDelete(MailLog $mailLog)
    {

        $inbox = $this->client->getFolder('INBOX');

        $message = $inbox->query()->getMessageByUid($mailLog->uid);

        $message->delete(false);

        $prev = $mailLog->status;

        $mailLog->status = 'deleted';
        $mailLog->previous_status = $prev;
        $mailLog->deleted_at = Carbon::now('Asia/Tokyo')->toDateTimeString();
        $mailLog->update();

    }

    public function deleteForever(MailLog $mailLog)
    {
        try {
            if (!$mailLog) {
                return response()->json(['status' => 'error', 'message' => 'Email not found.'], 404);
            }

            $this->deleteForeverProcess($mailLog);

            return response()->json(['status' => 'success', 'message' => 'Email deleted permanently.']);
        } catch (Exception $e) {
            logger()->error("Error deleting email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete email.'], 500);
        }
    }

    public function deleteForeverProcess(MailLog $mailLog)
    {

        $inbox = $this->client->getFolder('INBOX');

        $message = $inbox->query()->getMessageByUid($mailLog->uid);

        $message->delete(true);

        $attachments = Attachment::where('mail_log_id', $mailLog->id)->get();

        foreach ($attachments as $attachment) {
            $relativeFilePath = $attachment->path;

            $relativeFilePath = str_replace('storage/', '', $relativeFilePath);

            $filePath = storage_path('app/public/' . $relativeFilePath); // E.g., '/var/www/html/storage/app/public/mails/attachments/filename.ext'

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $attachment->delete();
        }

        $mailLog->delete();

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
                'cc' => $request->cc ?? null,
                'bcc' => $request->bcc ?? null,
                'message_content' => $request->message_content,
                'template_id' => $request->template_id ?? null,
                'message_id' => $messageId,
            ];

            $email = SentMail::create([
                'subject' => $data['subject'],
                'sender' => $data['fromAddress'],
                'name' => $request->from_name ?? env('IMAP_USERNAME'),
                'body' => $data['message_content'],
                'cc' => $data['cc'],
                'mailto' => $data['to'],
                'bcc' => $data['bcc'],
                'template_id' => $data['template_id'],
                'datetime' => Carbon::now('Asia/Tokyo')->toDateTimeString(),
                'type' => 'sent',
                'message_id' => $messageId,
            ]);

            Mail::to($email->mailto)
                ->cc($email->cc ?? [])
                ->bcc($email->bcc ?? [])
                ->send(new SendMail($email));

            return $this->success('Email Sent.');
        } catch (Exception $e) {
            logger()->error("Error sending email: " . $e->getMessage());
            return $this->error('Connection Failed.', []);
        }
    }

    private function getDomainFromEmail($email)
    {
        $parts = explode('@', $email);

        return isset($parts[1]) ? $parts[1] : null;
    }
}
