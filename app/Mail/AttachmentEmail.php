<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttachmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $email = $this->to($this->data['to_mail'])
          ->from($this->data['from_email'])
          ->subject($this->data['subject'])
          ->view('emails.sendpdf', $this->data);

        foreach ($this->data['paths'] as $path) {
          $email->attach($path);
        }

        return $email;
    }
}
