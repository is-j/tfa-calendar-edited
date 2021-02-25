<?php

namespace App\Mail;

use App\Models\Slot;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SlotClaimed extends Mailable
{
    use Queueable, SerializesModels;
    public $slot;
    public $role;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($slot, $role)
    {
        $this->slot = $slot;
        $this->role = $role;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.slots.claimed');
    }
}
