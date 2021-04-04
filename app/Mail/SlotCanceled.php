<?php

namespace App\Mail;

use App\Models\Slot;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SlotCanceled extends Mailable
{
    use Queueable, SerializesModels;
    public $slot;
    public $role;
    public $type;
    public $reason;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($slot, $role, $type, $reason)
    {
        $this->slot = $slot;
        $this->role = $role;
        $this->type = $type;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.slots.canceled');
    }
}
