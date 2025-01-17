<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Symfony\Component\Mime\Email;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Header\IdentificationHeader;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;


    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->view('templates.template')
                    ->subject($this->data->subject)
                    ->from($this->data->sender)
                    ->to($this->data->mailto)
                    ->cc($this->data->cc ?? [])
                    ->bcc($this->data->bcc ?? [])
                    ->with([
                        'message_content' => $this->data->body,
                        'template' => $this->data->template,
                    ])
                    ->withSymfonyMessage(function (Email $message) {
                        $headers = $message->getHeaders();

                        $headers->add(new IdentificationHeader('Message-ID', $this->data->message_id));
                        $headers->add(new IdentificationHeader('References', $this->data->message_id));
                    });
    }
}
