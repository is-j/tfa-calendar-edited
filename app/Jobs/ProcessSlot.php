<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessSlot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $type;
    protected $slot;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $slot)
    {
        $this->type = $type;
        $this->slot = $slot;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->editSlot($this->type, $this->slot);
    }

    protected function getClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName('tfa-calendar');
        $client->setAuthConfig(__DIR__ . "/credentials.json");
        $client->addScope("https://www.googleapis.com/auth/calendar");
        $client->setSubject("scheduler@tutoringforall.org");
        $tokenPath = __DIR__ . "/token.json";
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        return $client;
    }

    protected function editSlot($type, $slot)
    {
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);
        if ($type == 'claim') {
            $event = new \Google_Service_Calendar_Event(array(
                'summary' => "Tutoring session with " . User::find($slot['tutor_id'])->name . " and " . User::find($slot['student_id'])->name,
                'description' => "Meeting link:\n" . url('/ml/') . '/' . $slot['id'] . "\n\nWhat does student need help with?:\n" . $slot['info'],
                'start' => array(
                    'dateTime' => date("Y-m-d\\TH:i:s", strtotime($slot['start'])),
                    'timeZone' => 'UTC'
                ),
                'end' => array(
                    'dateTime' => date("Y-m-d\\TH:i:s", strtotime($slot['start'] . "+1 hours")),
                    'timeZone' => 'UTC'
                ),
                'attendees' => array(
                    array('email' => User::find($slot['tutor_id'])->email),
                    array('email' => User::find($slot['student_id'])->email)
                ),
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                        array('method' => 'email', 'minutes' => 30),
                        array('method' => 'popup', 'minutes' => 10),
                    ),
                ),
                'id' => $slot['id']
            ));
            $event = $service->events->insert('primary', $event);
        } else if ($type == 'cancel') {
            $service->events->delete('primary', $slot['id']);
        }
    }
}
