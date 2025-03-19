<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomEmail extends Mailable implements ShouldQueue // Implements Queue for performance
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $subjectLine;

    /**
     * Create a new message instance.
     *
     * @param string $subjectLine
     * @param string $messageContent
     */
    public function __construct($subjectLine, $messageContent)
    {
        $this->subjectLine = $subjectLine;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the email.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.custom')
                    ->with([
                        'messageContent' => $this->messageContent
                    ]);
    }
}
