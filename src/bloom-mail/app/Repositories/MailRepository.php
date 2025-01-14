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
use Illuminate\Support\Facades\Cache;
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

        // Pre-fetch the counts for inbox and trash to avoid redundant queries
        $inboxCount = MailLog::where('status', 'new')
        // ->where('parent_id', null)
        ->doesntHave('folders')
        ->count();

        $trashCount = MailLog::where('status', 'deleted')
        // ->where('parent_id', null)
        ->count();

        $folders = Folder::withCount(['mails' => function ($query) {
            $query->where('status', 'new');
            // ->where('parent_id', null);
        }])->get();

        $sentCount = SentMail::where('type', 'sent')->count();

        switch ($pageType) {
            case 'sent':
                $data = SentMail::with('template')
                    ->orderBy('datetime', 'desc')
                    ->where('type', 'sent')
                    ->paginate(100);
                break;

            case 'trash':
                $data = MailLog::where('status', 'deleted')
                    ->orderBy('datetime', 'desc')
                    ->paginate(100);
                break;

            case 'inbox':
            default:
                $query = MailLog::where('status', '!=', 'deleted');
                // ->where('parent_id', null);

                // Apply filters if available
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


                // Apply pagination
                $data = $query->orderBy('datetime', 'desc')->doesntHave('folders')->paginate(100);
                break;
        }

        // Return the data with counts and folders
        return [
            "data" => $data,
            "inbox" => $inboxCount,
            "sent" => $sentCount,
            "folders" => $folders,
            "trash" => $trashCount
        ];
    }

    public function inboxWithFolderId(Folder $folder)
    {
        $folderId = $folder->id;

        $inboxCount = MailLog::where('status', 'new')
        // ->where('parent_id', null)
        ->doesntHave('folders')
        ->count();

        $trashCount = MailLog::where('status', 'deleted')
        // ->where('parent_id', null)
        ->count();

        $inbox = $inboxCount ?? 0;
        $trash = $trashCount ?? 0;

        $sent = SentMail::where('type', 'sent')->count();

        $folders = Folder::withCount(['mails' => function ($query) {
            $query->where('status', 'new');
            // ->where('parent_id', null);
        }])->get();

        $data = MailLog::where('status', '!=', 'deleted')
            // ->where('parent_id', null)
            ->when($folderId, function ($query) use ($folderId) {
                $query->whereHas('folders', fn($q) => $q->where('folder_id', $folderId));
            })
            ->orderBy('datetime', 'desc')
            ->paginate(100);

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
        Log::info('Message fetching started');

        $inbox = $this->client->getFolder('INBOX');
        $messages = $inbox->messages()->all()->setFetchOrder("desc")->limit(100)->get();
        $newEmails = [];

        $spamEmails = Spam::pluck('mail_address')->toArray();
        $spamDomains = Spam::pluck('mail_address')->map(function ($email) {
            return explode('@', $email)[1];
        })->toArray();

        $existingMessageIds = MailLog::select('message_id')
        ->pluck('message_id')
        ->toArray();

        foreach (array_chunk($messages->toArray(), 10) as $messageChunk) {
            foreach ($messageChunk as $message) {
                $uid = $message->getUid();
                $messageId = $message->getMessageId();

                if (in_array($messageId, $existingMessageIds)) {
                    continue;
                }

                $subject = $this->decodeString($message->getSubject()[0]);
                $senderArray = $message->getFrom();
                $senderName = $senderArray[0]->personal ?? 'Unknown Sender';
                $senderEmail = !empty($senderArray) && isset($senderArray[0]->mail) ? $this->decodeString($senderArray[0]->mail) : 'unknown@example.com';

                $isSpam = in_array($senderEmail, $spamEmails) || in_array(explode('@', $senderEmail)[1], $spamDomains);

                if ($isSpam) {
                    $message->delete(false);
                    continue;
                }

                $flags = $message->getFlags()->toArray();
                // $status = in_array('Seen', $flags) ? 'read' : 'new';
                $status = 'new';

                $body = '';

                if ($message->hasTextBody()) {
                    $body = $message->getTextBody();
                } else {
                    $body = $message->getHTMLBody();
                }

                $dateSent = $message->getDate();
                $deleted_date = null;

                $inReplyTo = $message->getHeader()->get('in-reply-to');
                $references = $message->getHeader()->get('references');
                if (($inReplyTo || $references) && empty(trim($body)) && stripos($subject, 'Re:') === 0) {
                    continue;
                }

                $newMail = MailLog::create([
                    'uid' => $uid,
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'sender' => $senderEmail,
                    'name' => $senderName,
                    'body' => $body,
                    'datetime' => $this->convertToJapanTimezone($dateSent[0]),
                    'status' => $status,
                    'deleted_at' => $deleted_date
                ]);


                $attachments = $message->getAttachments();
                foreach ($attachments as $attachment) {
                    $this->processAttachment($attachment, $newMail);
                }

                $newEmails[] = [
                    'uid' => $uid,
                    'message_id' => $messageId,
                    'subject' => $subject,
                    'sender' => $senderEmail,
                    'name' => $senderName,
                    'body' => $body,
                    'datetime' => $this->convertToJapanTimezone($dateSent[0]),
                    'status' => $status,
                ];
            }
        }

        // $this->singleFolderMatching();

        Log::info('Message fetching ended');

        Log::info('Sending to queue started');

        $checkNew = count($newEmails) > 0 ? 1 : 0;

        broadcast(new TakingMail(["new" => $checkNew]));

        Log::info('Sending to queue ended');
    }

    private function processAttachment($attachment, $mail)
    {
        $fileName = $attachment->getName();
        $filePath = 'mails/attachments/' . $fileName;

        Storage::disk('public')->put($filePath, $attachment->content);

        Attachment::create([
            'file_name' => $fileName,
            'mime_type' => $attachment->getMimeType(),
            'file_size' => $attachment->getSize(),
            'path' => 'storage/' . $filePath,
            'mail_log_id' => $mail->id,
        ]);
    }

    public function convertToJapanTimezone($date)
    {
        $dateInJapan = $date->setTimezone('Asia/Tokyo');

        return $dateInJapan->toDateTimeString();
    }

    public function singleFolderMatching()
    {
        Folder::with('extra_searches')->chunk(100, function ($folders) {
            foreach ($folders as $folder) {
                $searchCharacter = $folder->search_character;
                $method = strtolower($folder->method);
                $extraSearches = $folder->extra_searches;

                MailLog::where('is_match', 0)->chunk(100, function ($allMails) use ($folder, $searchCharacter, $method, $extraSearches) {
                    foreach ($allMails as $mail) {

                        $mail->is_match = 1;
                        $mail->save();

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

                        $extraMatch = true;

                        foreach ($extraSearches as $extraSearch) {
                            $extraSearchCharacter = $extraSearch->search_character;
                            $extraMethod = strtolower($extraSearch->method);
                            $isExclude = $extraSearch->is_exclude;

                            $match = false;
                            if ($extraMethod === 'exact_match' && $subject === $extraSearchCharacter) {
                                $match = true;
                            } elseif ($extraMethod === 'partial_match' && str_contains($subject, $extraSearchCharacter)) {
                                $match = true;
                            } elseif ($extraMethod === 'front_match' && str_starts_with($subject, $extraSearchCharacter)) {
                                $match = true;
                            } elseif ($extraMethod === 'backward_match' && str_ends_with($subject, $extraSearchCharacter)) {
                                $match = true;
                            }

                            if ($isExclude && $match) {
                                $extraMatch = false;
                                break;
                            }

                            if (!$isExclude && !$match) {
                                $extraMatch = false;
                                break;
                            }
                        }

                        if ($isMatch && $extraMatch) {
                            DB::table('folder_mails')->updateOrInsert(
                                ['mail_log_id' => $mail->id, 'folder_id' => $folder->id],
                                []
                            );
                        } else {
                            DB::table('folder_mails')
                                ->where('mail_log_id', $mail->id)
                                ->where('folder_id', $folder->id)
                                ->delete();
                        }
                    }
                });
            }
        });
    }

    public function folderMatching()
    {
        Folder::with('extra_searches')->chunk(100, function ($folders) {
            foreach ($folders as $folder) {
                $searchCharacter = $folder->search_character;
                $method = strtolower($folder->method);
                $extraSearches = $folder->extra_searches;

                MailLog::chunk(100, function ($allMails) use ($folder, $searchCharacter, $method, $extraSearches) {
                    foreach ($allMails as $mail) {

                        $mail->is_match = 1;
                        $mail->save();

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

                        $extraMatch = true;

                        foreach ($extraSearches as $extraSearch) {
                            $extraSearchCharacter = $extraSearch->search_character;
                            $extraMethod = strtolower($extraSearch->method);
                            $isExclude = $extraSearch->is_exclude;

                            $match = false;
                            if ($extraMethod === 'exact_match' && $subject === $extraSearchCharacter) {
                                $match = true;
                            } elseif ($extraMethod === 'partial_match' && str_contains($subject, $extraSearchCharacter)) {
                                $match = true;
                            } elseif ($extraMethod === 'front_match' && str_starts_with($subject, $extraSearchCharacter)) {
                                $match = true;
                            } elseif ($extraMethod === 'backward_match' && str_ends_with($subject, $extraSearchCharacter)) {
                                $match = true;
                            }

                            if ($isExclude && $match) {
                                $extraMatch = false;
                                break;
                            }

                            if (!$isExclude && !$match) {
                                $extraMatch = false;
                                break;
                            }
                        }

                        if ($isMatch && $extraMatch) {
                            DB::table('folder_mails')->updateOrInsert(
                                ['mail_log_id' => $mail->id, 'folder_id' => $folder->id],
                                []
                            );
                        } else {
                            DB::table('folder_mails')
                                ->where('mail_log_id', $mail->id)
                                ->where('folder_id', $folder->id)
                                ->delete();
                        }
                    }
                });
            }
        });
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

    private function processThreadMessage($threadMessage)
    {
        $senderArray = $threadMessage->getFrom();

        return [
            'uid' => $threadMessage->getUid(),
            'message_id' => $threadMessage->getMessageId()[0],
            'subject' => isset($threadMessage->getSubject()[0])
                ? $this->decodeString($threadMessage->getSubject()[0])
                : 'No Subject',
            'sender' => !empty($senderArray) && isset($senderArray[0]->mail)
                ? $this->decodeString($senderArray[0]->mail)
                : 'unknown@example.com',
            'name' => $this->decodeString(
                isset($senderArray[0]) ? (string)$senderArray[0]->personal : 'Unknown Sender'
            ),
            'body' => $threadMessage->hasHTMLBody()
                ? $threadMessage->getHTMLBody()
                : $threadMessage->getTextBody(),
            'datetime' => ($threadMessage->getDate()[0] ?? Carbon::now('Asia/Tokyo'))->toDateTimeString(),
            'status' => in_array('Seen', $threadMessage->getFlags()->toArray()) ? 'read' : 'unread',
        ];
    }

    private function decodeString($value)
    {
        try {
            if (preg_match('/=\?[^?]+\?/', $value)) {
                $decodedValue = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
                return $decodedValue !== false ? $decodedValue : $value;
            }
            return $value;
        } catch (\Exception $e) {
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
