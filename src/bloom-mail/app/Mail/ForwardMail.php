<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Symfony\Component\Mime\Email;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Mime\Header\IdentificationHeader;

class ForwardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $originalContent;
    public $fowardContent;

    public function __construct($emailData, $originalContent, $fowardContent)
    {
        $this->emailData = $emailData;
        $this->originalContent = $originalContent;
        $this->fowardContent = $fowardContent;
    }

    public function build()
    {
        return $this->view('emails.forward')
            ->subject($this->emailData['subject'])
            ->from($this->emailData['from'])
            ->withSymfonyMessage(function (Email $message) {
                $headers = $message->getHeaders();

                $headers->add(new IdentificationHeader('Message-ID', $this->emailData['message_id']));
                $headers->add(new IdentificationHeader('References', $this->emailData['references']));
            })
            ->with([
                'content' => $this->emailData['message_content'],
                'originalContent' => $this->originalContent,
                'fowardContent' => $this->fowardContent
            ]);
    }
}
