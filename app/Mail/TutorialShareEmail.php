<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class TutorialShareEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->to($this->data['to_address'])
            ->subject($this->data['subject'])
            ->view('tutorial-videos.share', [
                'messageBody'   => $this->data['message'],
                "locale"        => App::getLocale()
            ]);
    }
}
