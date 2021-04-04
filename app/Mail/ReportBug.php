<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportBug extends Mailable
{
    use Queueable, SerializesModels;
    public $userid;
    public $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userid, $message)
    {
        $this->userid = $userid;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reports.bug');
    }
}
