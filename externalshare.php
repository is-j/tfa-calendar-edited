<?php

use App\Models\Slot;
use App\Models\User;
use App\Models\Tutor;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\PHPMailer;
use League\OAuth2\Client\Provider\Google;

require __DIR__ . '/vendor/autoload.php';

class ExternalShare
{
    public function sendEmail($action, $eventid, $message)
    {

        $tutorid = Slot::where('event_id', $eventid)->first()->tutor_id;
        $studentid = Slot::where('event_id', $eventid)->first()->student_id;
        $tutorname = User::find($tutorid)->name;
        $studentname = User::find($studentid)->name;
        $tutoremail = User::find($tutorid)->email;
        $studentemail = User::find($studentid)->email;
        $start = Slot::where('event_id', $eventid)->first()->start;

        if ($action == "delete") {
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("Y-m-d h:i A", strtotime($start . "$offtutor hours"));
            if ($studentname != "n/a") {
                $offstudent = User::find($studentid)->timezone;
                $dtstudent = date("Y-m-d H:i", strtotime($start . "$offstudent hours"));
                $input = array("tutor" => array("subject" => "canceled: session at $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you canceled the session at $dttutor with $studentname. please remember to avoid doing so after a student claims the slot. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutorname), "student" => array("subject" => "canceled: session at $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> your tutor $tutorname canceled the session at $dtstudent with you. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $studentname));
            }
            $this->editEvent('delete', $start, [$tutorid, $studentid], $eventid);
        } else if ($action == "unclaim") {
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("Y-m-d h:i A", strtotime($start . "$offtutor hours"));
            $offstudent = User::find($studentid)->timezone;
            $dtstudent = date("Y-m-d h:i A", strtotime($start . "$offstudent hours"));
            $input = array("tutor" => array("subject" => "canceled: session at $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> your student $studentname canceled the session at $dttutor with you. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $tutoremail, "username" => $tutorname), "student" => array("subject" => "canceled: session at $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you canceled the session at $dtstudent with $tutorname. please remember to avoid doing so after you claim the slot. <br><br> <b>cancellation reasons:</b> <br> $message", "email" => $studentemail, "username" => $studentname));
            $this->editEvent('unclaim', $start, [$tutorid, $studentid], $eventid);
        } else if ($action == "claim") {
            $zoom = getZoom($tutorname);
            $offtutor = User::find($tutorid)->timezone;
            $dttutor = date("Y-m-d h:i A", strtotime($start . "$offtutor hours"));
            $offstudent = User::find($studentid)->timezone;
            $dtstudent = date("Y-m-d h:i A", strtotime($start . "$offstudent hours"));
            $emailstudent = getEmail($studentname);
            $emailtutor = getEmail($tutorname);
            $input = array("tutor" => array("subject" => "claimed: session at $dttutor with $studentname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> the student $studentname claimed the session at $dttutor with you. please remember to avoid canceling. <br> please go to the following zoom link at least 5 minutes before $dttutor. if this is not your preferred meeting link, please change it on your profile on the tfa-calendar website. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$zoom'>zoom link (opens in new page)</a> <br> <br> <b>session's topic needs/help:</b> <br> $message <br> <br> <b>contact the student if you have any problems:</b> <br> $emailstudent", "email" => $tutoremail), "student" => array("subject" => "claimed: session at $dtstudent with $tutorname", "body" => "<h1 style='border-radius: .25rem;color: #f8f9fa!important;padding-left: .5rem!important;padding-right: .5rem!important;padding-top: .3125rem; padding-bottom: .3125rem;font-size: 1.25rem;background-color: #6c757d!important;display:inline-block;'> tfa-calendar</h1> <br> <i> *note that all times are in your local timezone.</i> <br> <br> you claimed the session at $dtstudent with the tutor $tutorname. please remember to avoid canceling. <br> please go to the following zoom link at least 5 minutes before $dtstudent. <br><br> <a style='display: inline-block;text-decoration:none; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; background-color: transparent; border: 1px solid transparent; padding: 2px 5px; font-size: 1rem; line-height: 1.5; border-radius: .25rem;color: #fff; background-color: #007bff; border-color: #007bff;' target='_blank' href='$zoom'>zoom link (opens in new page)</a> <br> <br> <b>session's topic needs/help:</b> <br> $message <br> <br> <b>contact the tutor if you have any problems:</b> <br> $emailtutor", "email" => $studentemail, "username" => $studentname));
            $this->editEvent('claim', $start, [$tutorid, $studentid], $eventid);
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

    function editEvent($action, $start, $ids, $eventid)
    {
        // $ids[0] = tutor_id
        $client = $this->getClient();
        $service = new Google_Service_Calendar($client);
        if ($action == 'claim') {
            $event = new Google_Service_Calendar_Event(array(
                'summary' => "Tutoring session with " . User::find($ids[0])->name . " and " . User::find($ids[1])->name,
                'description' => "zoom:\n" . Tutor::find($ids[0])->meeting_link . "\n\nsession topic needs/help:\n" . Slot::where('event_id', $eventid)->info,
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
            $calendarId = 'primary';
            $event = $service->events->insert($calendarId, $event);
            require "../config.php";
            $sql = "UPDATE slots SET eventid = ? WHERE start = ? AND tutor = ?";
            if ($stmt = $link->prepare($sql)) {
                $stmt->execute([$temp, $start, $usernames[0]]);
            }
        } else {
            $service->events->delete('primary', $eventid);
        }
    }

    function getClient()
    {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $client = new Google_Client();
        $client->setApplicationName('tfa-calendar');
        $client->setAuthConfig("credentials.json");
        $client->setRedirectUri($redirect_uri);
        $client->addScope("https://www.googleapis.com/auth/calendar");
        $client->setSubject("scheduler@tutoringforall.org");
        $tokenPath = "token.json";
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
        return $client;
    }
}
