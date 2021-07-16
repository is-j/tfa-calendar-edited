<?php

namespace App\Mail;

use DateTimeZone;
use App\Models\User;
use App\Models\Event;
use DateTimeImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public $roleName;
    public $event;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userId, $eventId, $content)
    {
        $event = Event::find($eventId);
        $this->roleName = User::find($userId)->role->name;
        $this->event = [
            'formatted_start' => (new DateTimeImmutable($event->start))->setTimezone(new DateTimeZone(User::find($userId)->timezone))->format('D M j, Y g:i A'),
            'reason' => $content['reason'],
            'tutor_name' => User::find($event->tutor_id)->name,
            'student_name' => User::find($event->student_id)->name,
            'responsible' => $content['responsible'],
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.events.canceled');
    }
}
