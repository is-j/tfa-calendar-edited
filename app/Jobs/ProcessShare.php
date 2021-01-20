<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Tutor;
use Illuminate\Bus\Queueable;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessShare implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $action;
    protected $eventid;
    protected $event;
    protected $message;

    public function __construct($action, $eventid, $event, $message)
    {
        //
        $this->action = $action;
        $this->eventid = $eventid;
        $this->event = $event;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendEmail($this->action, $this->eventid, $this->event, $this->message);
    }

    protected function sendEmail($action, $eventid, $event, $message)
    {
        $tutorid = $event['tutor_id'];
        $studentid = $event['student_id'];
        $tutorname = User::find($tutorid)->name;
        $studentname = User::find($studentid)->name;
        $tutoremail = User::find($tutorid)->email;
        $studentemail = User::find($studentid)->email;
        $start = $event['start'];

        if ($action == "delete") {
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("D M j, Y g:i A", strtotime($start . "$offtutor hours"));
            if ($studentname != "n/a") {
                $offstudent = User::find($studentid)->timezone;
                $dtstudent = date("D M j, Y g:i A", strtotime($start . "$offstudent hours"));
                $input = array("tutor" => array("subject" => "Canceled: Session @ $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> You canceled the session at $dttutor with $studentname. Please remember to avoid doing so after a student claims the slot. <br><br> <b>Cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutorname), "student" => array("subject" => "Canceled: Session @ $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> Your tutor $tutorname canceled the session at $dtstudent with you. <br><br> <b>Cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $studentname));
            }
            $this->editEvent('delete', $start, [$tutorid, $studentid], $eventid);
        } else if ($action == "unclaim") {
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("D M j, Y g:i A", strtotime($start . "$offtutor hours"));
            $offstudent = User::find($studentid)->timezone;
            $dtstudent = date("D M j, Y g:i A", strtotime($start . "$offstudent hours"));
            $input = array("tutor" => array("subject" => "Canceled: Session @ $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> Your student $studentname canceled the session at $dttutor with you. <br><br> <b>Cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutorname), "student" => array("subject" => "Canceled: Session @ $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> You canceled the session at $dtstudent with $tutorname. Please remember to avoid doing so after you claim the slot. <br><br> <b>Cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $studentname));
            $this->editEvent('unclaim', $start, [$tutorid, $studentid], $eventid);
        } else if ($action == "claim") {
            $meetinglink =  url("/ml/$eventid");
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("D M j, Y g:i A", strtotime($start . "$offtutor hours"));
            $offstudent = User::find($studentid)->timezone;
            $dtstudent = date("D M j, Y g:i A", strtotime($start . "$offstudent hours"));
            $input = array("tutor" => array("subject" => "Claimed: Session @ $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> The student $studentname claimed the session at $dttutor with you. Please remember to avoid canceling. <br> Please go to the following meeting link at least 5 minutes before $dttutor. If this is not your preferred meeting link, please change it on your profile on the tfa-calendar website before the session. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$meetinglink'>Meeting link (opens in new page)</a> <br> <br> <b>Session's topic needs:</b> <br> $message <br> <br> <b>Contact the student if you have any problems:</b> <br> $studentemail", "email" => $tutoremail, "username" => $tutorname), "student" => array("subject" => "Claimed: Session @ $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> You claimed the session at $dtstudent with the tutor $tutorname. Remember to avoid canceling. <br> Please go to the following meeting link at least 5 minutes before $dtstudent. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$meetinglink'>Meeting link (opens in new page)</a> <br> <br> <b>Session's topic needs:</b> <br> $message <br> <br> <b>Contact the tutor if you have any problems:</b> <br> $tutoremail", "email" => $studentemail, "username" => $studentname));
            $this->editEvent('claim', $start, [$tutorid, $studentid], $eventid, $message);
        }

        // init phpmailer
        $mail = new PHPMailer();
        $mail->isSMTP();

        // debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        // smtp auth
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';
        $email = 'scheduler@tutoringforall.org';
        $clientId = '813639001936-0nefnbeqr0ech4tiusjioh5e77em228m.apps.googleusercontent.com';
        $clientSecret = 'v-w27AgTcIUM7JMNF4Fsz_a1';
        $refreshToken = '1//04x6jOyrrdvgOCgYIARAAGAQSNwF-L9IrmWiyjzjTuSbcPN4bjKf2qd5gl5QfeqWwhFQaJeIiU6h-CZRWAWu_1zPceiW6kFeti1I';

        // coauth2 provider
        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );
        $mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $email,
                ]
            )
        );

        // setup + send email
        $mail->setFrom($email, 'tfa-calendar');
        $mail->isHTML(true);
        foreach ($input as $value) {
            $mail->ClearAllRecipients();
            $mail->addAddress($value["email"], $value["username"]);
            $mail->Subject = $value["subject"];
            $mail->Body = $value["body"];
            $mail->AltBody = "If you want to view this message, please use a html supported browser and email client. We apologize for the inconvenience.";
            $mail->send();
        }
    }

    protected function editEvent($action, $start, $ids, $eventid, $message = NULL)
    {
        // $ids[0] = tutor_id
        $client = $this->getClient();
        $service = new \Google_Service_Calendar($client);
        if ($action == 'claim') {
            $event = new \Google_Service_Calendar_Event(array(
                'summary' => "Tutoring session with " . User::find($ids[0])->name . " and " . User::find($ids[1])->name,
                'description' => "Meeting link:\n" . url("/ml/$eventid") . "\n\nSession topic needs:\n" . $message,
                'start' => array(
                    'dateTime' => date("Y-m-d\\TH:i:s", strtotime($start)),
                    'timeZone' => 'UTC',
                ),
                'end' => array(
                    // 2015-05-28T17:00:00
                    'dateTime' => date("Y-m-d\\TH:i:s", strtotime($start . "+1 hours")),
                    'timeZone' => 'UTC',
                ),
                'attendees' => array(
                    array('email' => User::find($ids[0])->email),
                    array('email' => User::find($ids[1])->email),
                ),
                'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                        array('method' => 'email', 'minutes' => 30),
                        array('method' => 'popup', 'minutes' => 10),
                    ),
                ),
                'id' => $eventid
            ));
            $event = $service->events->insert('primary', $event);
        } else {
            $service->events->delete('primary', $eventid);
        }
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
}
