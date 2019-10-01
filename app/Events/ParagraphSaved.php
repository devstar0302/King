<?php

namespace App\Events;

use App\Models\Paragraph;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ParagraphSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $paragraph;

    public function __construct(Paragraph $paragraph)
    {
        $this->paragraph = $paragraph;
    }
}
