<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Symfony\Component\Mime\Email;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Header\IdentificationHeader;

class ReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $originalContent;
    public $replyContent;

    public function __construct($emailData, $originalContent, $replyContent)
    {
        $this->emailData = $emailData;
        $this->originalContent = $originalContent;
        $this->replyContent = $replyContent;
    }

    public function build()
    {
        return $this->view('emails.reply')
            ->subject($this->emailData['subject'])
            ->from($this->emailData['from'])
            ->withSymfonyMessage(function (Email $message) {
                $headers = $message->getHeaders();

                $headers->add(new IdentificationHeader('Message-ID', $this->emailData['message_id']));
                $headers->add(new IdentificationHeader('In-Reply-To', $this->emailData['in_reply_to']));
                $headers->add(new IdentificationHeader('References', $this->emailData['in_reply_to']));
            })
            ->with([
                'content' => $this->emailData['message_content'],
                'originalContent' => $this->originalContent,
                'replyContent' => $this->replyContent
            ]);
    }
}
