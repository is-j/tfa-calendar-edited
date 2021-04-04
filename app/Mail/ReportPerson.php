<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportPerson extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    public $slotstart;    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $slotstart)
    {
        $this->id = $id;
        $this->slotstart = $slotstart;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reports.person');
    }
}
