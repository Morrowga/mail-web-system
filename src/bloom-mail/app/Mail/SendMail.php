<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
                    // Add custom headers such as Message-ID
                    ->withSwiftMessage(function ($message) {
                        $message->getHeaders()->addTextHeader('Message-ID', $this->data['message_id']);
                    });
    }
}
